const rtcConfig = { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] };
let activeCall = null;
let incomingOffer = null;

function panel() { return document.querySelector('[data-chat-call-panel]'); }
function signalUrl() { return panel()?.dataset.signalUrl; }
function setStatus(text) { const node = panel()?.querySelector('[data-chat-call-status]'); if (node) node.textContent = text; }
function showPanel(show = true) { panel()?.classList.toggle('hidden', !show); }
function toggleButton(selector, show) { panel()?.querySelector(selector)?.classList.toggle('hidden', !show); }

async function sendSignal(type, call, payload = {}) {
    const url = signalUrl();
    if (!url) throw new Error('Percakapan tidak tersedia.');
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': token || '' },
        body: JSON.stringify({ type, call_id: call.id, mode: call.mode, payload }),
    });
    if (!response.ok) throw new Error('Sinyal panggilan gagal dikirim. Pastikan koneksi aktif.');
}

function attachMedia(stream) {
    const media = panel()?.querySelector('[data-chat-call-media]');
    const localVideo = panel()?.querySelector('[data-chat-local-video]');
    const remoteVideo = panel()?.querySelector('[data-chat-remote-video]');
    const remoteAudio = panel()?.querySelector('[data-chat-remote-audio]');
    if (media) media.classList.remove('hidden');
    if (localVideo && stream.getVideoTracks().length) {
        localVideo.srcObject = stream;
        localVideo.classList.remove('hidden');
    }
    if (remoteVideo && stream.getVideoTracks().length) remoteVideo.classList.remove('hidden');
    if (remoteVideo) remoteVideo.srcObject = null;
    if (remoteAudio) remoteAudio.srcObject = null;
}

async function makePeer(call, stream) {
    const pc = new RTCPeerConnection(rtcConfig);
    call.pc = pc;
    stream.getTracks().forEach((track) => pc.addTrack(track, stream));
    pc.onicecandidate = ({ candidate }) => {
        if (candidate) sendSignal('ice', call, { candidate: candidate.toJSON() }).catch((error) => setStatus(error.message));
    };
    pc.ontrack = ({ streams }) => {
        const remote = streams[0];
        const video = panel()?.querySelector('[data-chat-remote-video]');
        const audio = panel()?.querySelector('[data-chat-remote-audio]');
        if (video && remote.getVideoTracks().length) video.srcObject = remote;
        if (audio) audio.srcObject = remote;
        panel()?.querySelector('[data-chat-call-media]')?.classList.remove('hidden');
    };
    pc.onconnectionstatechange = () => {
        if (pc.connectionState === 'connected') setStatus(`Terhubung dengan ${panel()?.dataset.peerName || 'karyawan'}`);
        if (['failed', 'disconnected'].includes(pc.connectionState)) setStatus('Koneksi terputus. Periksa jaringan dan izin browser.');
    };
    return pc;
}

async function startCall(mode) {
    if (activeCall) return;
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true, video: mode === 'video' });
        const call = { id: crypto.randomUUID(), mode, stream, pc: null, pendingIce: [] };
        activeCall = call;
        attachMedia(stream);
        const pc = await makePeer(call, stream);
        const offer = await pc.createOffer();
        await pc.setLocalDescription(offer);
        await sendSignal('offer', call, { sdp: pc.localDescription.toJSON() });
        setStatus(`Memanggil ${panel()?.dataset.peerName || 'karyawan'}…`);
        showPanel();
        toggleButton('[data-chat-call-hangup]', true);
    } catch (error) {
        cleanupCall();
        setStatus(error.message || 'Tidak dapat memulai panggilan. Periksa izin mikrofon/kamera.');
        showPanel();
    }
}

async function acceptCall() {
    if (!incomingOffer) return;
    const offer = incomingOffer;
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true, video: offer.mode === 'video' });
        const call = { id: offer.call_id, mode: offer.mode, stream, pc: null, pendingIce: offer.pendingIce || [] };
        activeCall = call;
        attachMedia(stream);
        const pc = await makePeer(call, stream);
        await pc.setRemoteDescription(new RTCSessionDescription(offer.payload.sdp));
        for (const candidate of call.pendingIce) await pc.addIceCandidate(candidate);
        call.pendingIce = [];
        const answer = await pc.createAnswer();
        await pc.setLocalDescription(answer);
        await sendSignal('answer', call, { sdp: pc.localDescription.toJSON() });
        incomingOffer = null;
        setStatus('Menghubungkan panggilan…');
        toggleButton('[data-chat-call-accept]', false);
        toggleButton('[data-chat-call-reject]', false);
        toggleButton('[data-chat-call-hangup]', true);
    } catch (error) {
        cleanupCall();
        setStatus(error.message || 'Panggilan gagal. Periksa izin mikrofon/kamera.');
    }
}

function cleanupCall() {
    if (activeCall) {
        activeCall.pc?.close();
        activeCall.stream?.getTracks().forEach((track) => track.stop());
    }
    activeCall = null;
    incomingOffer = null;
    const media = panel()?.querySelector('[data-chat-call-media]');
    media?.classList.add('hidden');
    panel()?.querySelectorAll('video, audio').forEach((element) => { element.srcObject = null; });
    toggleButton('[data-chat-call-accept]', false);
    toggleButton('[data-chat-call-reject]', false);
    toggleButton('[data-chat-call-hangup]', false);
}

async function handleSignal(signal) {
    const currentPanel = panel();
    if (!currentPanel || String(currentPanel.dataset.recipientId) !== String(signal.sender_id)) return;
    if (signal.type === 'offer') {
        if (activeCall || incomingOffer) {
            sendSignal('reject', { id: signal.call_id, mode: signal.mode }).catch(() => {});
            return;
        }
        incomingOffer = { ...signal, pendingIce: [] };
        showPanel();
        setStatus(`Panggilan ${signal.mode === 'video' ? 'video' : 'suara'} dari ${signal.sender_name}`);
        toggleButton('[data-chat-call-accept]', true);
        toggleButton('[data-chat-call-reject]', true);
        return;
    }
    if (!activeCall || activeCall.id !== signal.call_id) {
        if (incomingOffer?.call_id === signal.call_id && signal.type === 'ice' && signal.payload.candidate) {
            incomingOffer.pendingIce.push(new RTCIceCandidate(signal.payload.candidate));
        }
        return;
    }
    if (signal.type === 'answer') {
        await activeCall.pc.setRemoteDescription(new RTCSessionDescription(signal.payload.sdp));
        for (const candidate of activeCall.pendingIce) await activeCall.pc.addIceCandidate(candidate);
        activeCall.pendingIce = [];
    } else if (signal.type === 'ice' && signal.payload.candidate) {
        const candidate = new RTCIceCandidate(signal.payload.candidate);
        if (activeCall.pc.remoteDescription) await activeCall.pc.addIceCandidate(candidate);
        else activeCall.pendingIce.push(candidate);
    } else if (signal.type === 'end' || signal.type === 'reject') {
        setStatus(signal.type === 'reject' ? 'Panggilan ditolak.' : 'Panggilan berakhir.');
        cleanupCall();
    }
}

function initChatCalls() {
    if (document.documentElement.dataset.chatCallsReady === '1') return;
    document.documentElement.dataset.chatCallsReady = '1';
    document.addEventListener('click', (event) => {
        const start = event.target.closest('[data-chat-call-start]');
        if (start) startCall(start.dataset.chatCallStart);
        if (event.target.closest('[data-chat-call-accept]')) acceptCall();
        if (event.target.closest('[data-chat-call-reject]')) {
            if (incomingOffer) sendSignal('reject', { id: incomingOffer.call_id, mode: incomingOffer.mode }).catch(() => {});
            setStatus('Panggilan ditolak.'); cleanupCall();
        }
        if (event.target.closest('[data-chat-call-hangup]')) {
            if (activeCall) sendSignal('end', activeCall).catch(() => {});
            setStatus('Panggilan berakhir.'); cleanupCall();
        }
    });
    window.addEventListener('chat:call-signal', (event) => handleSignal(event.detail).catch((error) => setStatus(error.message)));
    document.addEventListener('livewire:navigated', () => { if (!panel()) cleanupCall(); });
}

document.addEventListener('DOMContentLoaded', initChatCalls);
document.addEventListener('livewire:navigated', initChatCalls);
