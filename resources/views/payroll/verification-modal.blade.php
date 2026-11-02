<div id="payroll-verification-modal" class="fixed inset-0 z-[100] hidden items-center justify-center overflow-y-auto bg-gray-950/60 p-4" role="dialog" aria-modal="true" aria-labelledby="payroll-verification-title">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900" data-modal-panel>
        <div class="flex items-start justify-between gap-4">
            <div><h2 id="payroll-verification-title" class="text-lg font-bold text-gray-900 dark:text-gray-100">Verifikasi untuk membuka payslip</h2><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih verifikasi email atau Face ID. Akses berlaku 5 menit untuk payslip ini.</p></div>
            <button type="button" data-modal-close class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800" aria-label="Tutup">✕</button>
        </div>
        <p data-modal-message class="mt-4 hidden rounded-lg px-3 py-2 text-sm" role="status" aria-live="polite"></p>

        <section class="mt-5 rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Verifikasi dengan email</h3>
            @if(auth()->user()->email && auth()->user()->hasVerifiedEmail())
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kode akan dikirim ke {{ auth()->user()->email }}.</p>
                <button type="button" data-send-code class="btn-secondary mt-3 text-sm">Kirim kode verifikasi</button>
                <div class="mt-3 flex gap-2"><input data-email-code type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" class="input-field" placeholder="Kode 6 digit" aria-label="Kode verifikasi email"><button type="button" data-verify-code class="btn-primary shrink-0 text-sm">Verifikasi</button></div>
            @else
                <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">Tambahkan dan verifikasi email di Profil terlebih dahulu.</p><a href="{{ route('profile.edit') }}" class="mt-2 inline-flex text-sm font-semibold text-primary-600">Buka Profil</a>
            @endif
        </section>

        <section class="mt-4 rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Verifikasi dengan Face ID</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Hadapkan wajah ke kamera dan berkedip saat diminta.</p>
            <div data-face-panel class="mt-3 hidden space-y-3"><video data-face-video autoplay muted playsinline class="w-full max-h-64 rounded-xl bg-gray-950 object-cover"></video><p data-face-status class="text-sm text-gray-600 dark:text-gray-300" role="status" aria-live="polite"></p></div>
            <button type="button" data-face-start class="btn-secondary mt-3 text-sm">Mulai verifikasi Face ID</button>
        </section>

        <button type="button" data-continue class="btn-primary mt-5 hidden w-full">Lanjutkan ke payslip</button>
    </div>
</div>

@push('scripts')
<script>
(() => {
    const modal = document.getElementById('payroll-verification-modal');
    if (!modal) return;
    const base = @js(url('/payroll/detail'));
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const message = modal.querySelector('[data-modal-message]'), code = modal.querySelector('[data-email-code]');
    const panel = modal.querySelector('[data-face-panel]'), video = modal.querySelector('[data-face-video]'), faceStatus = modal.querySelector('[data-face-status]');
    const faceStart = modal.querySelector('[data-face-start]'), continueButton = modal.querySelector('[data-continue]');
    let detailId = null, action = 'download', media = null, nextUrl = null;

    const notify = (text, success = false) => { message.textContent=text; message.className=`mt-4 rounded-lg px-3 py-2 text-sm ${success?'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300':'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300'}`; };
    const endpoint = (suffix) => `${base}/${detailId}/verify/${suffix}`;
    const post = async (url, body) => {
        const response = await fetch(url,{method:'POST',credentials:'same-origin',headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest'},body:JSON.stringify(body)});
        const data=await response.json(); if(!response.ok) throw new Error(data.message || Object.values(data.errors||{}).flat()[0] || 'Verifikasi gagal.'); return data;
    };
    const open = (id, requestedAction) => {
        detailId=String(id); action=requestedAction; nextUrl=null; code && (code.value=''); continueButton.classList.add('hidden');
        message.classList.add('hidden'); modal.classList.remove('hidden'); modal.classList.add('flex'); document.body.classList.add('overflow-hidden');
    };
    const close = () => { media?.getTracks().forEach(track=>track.stop()); media=null; if(video) video.srcObject=null; modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.classList.remove('overflow-hidden'); };
    const success = (data) => { nextUrl=data.redirect_url; notify(data.message || 'Verifikasi berhasil.',true); continueButton.classList.remove('hidden'); };

    document.addEventListener('click', event => {
        const anchor=event.target.closest('a[href]'); if(!anchor) return;
        const match=new URL(anchor.href,location.href).pathname.match(/^\/payroll\/detail\/(\d+)\/(view|download)$/);
        if(!match) return;
        event.preventDefault(); open(match[1],match[2]);
    }, true);
    modal.querySelector('[data-modal-close]').addEventListener('click',close);
    modal.addEventListener('click',event=>{if(event.target===modal) close();});
    document.addEventListener('keydown',event=>{if(event.key==='Escape'&&!modal.classList.contains('hidden'))close();});

    modal.querySelector('[data-send-code]')?.addEventListener('click',async event=>{
        const button=event.currentTarget; button.disabled=true;
        try { const result=await post(endpoint('email/send'),{action}); notify(result.message,true); }
        catch(error){notify(error.message);} finally{button.disabled=false;}
    });
    modal.querySelector('[data-verify-code]')?.addEventListener('click',async event=>{
        const button=event.currentTarget; button.disabled=true;
        try { const result=await post(endpoint('email'),{action,code:code.value.trim()}); success(result); }
        catch(error){notify(error.message);} finally{button.disabled=false;}
    });
    continueButton.addEventListener('click',()=>{if(!nextUrl)return; if(action==='view')window.open(nextUrl,'_blank','noopener'); else window.location.assign(nextUrl); close();});

    faceStart.addEventListener('click',async()=>{
        faceStart.disabled=true; panel.classList.remove('hidden'); faceStatus.textContent='Menyiapkan kamera...';
        try {
            const challengeResponse=await fetch(endpoint('face/challenge'),{credentials:'same-origin',headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
            const challengeData=await challengeResponse.json(); if(!challengeResponse.ok) throw new Error(challengeData.message||'Face ID belum terdaftar.');
            if(!navigator.mediaDevices?.getUserMedia)throw new Error('Kamera memerlukan HTTPS dan dukungan browser.');
            if(!window.Human){const script=document.createElement('script');script.src='https://cdn.jsdelivr.net/npm/@vladmandic/human@3.3.6/dist/human.js';await new Promise((resolve,reject)=>{script.onload=resolve;script.onerror=()=>reject(new Error('Modul Face ID gagal dimuat.'));document.head.appendChild(script);});}
            media=await navigator.mediaDevices.getUserMedia({video:{facingMode:'user'},audio:false});video.srcObject=media;await new Promise(resolve=>video.onloadedmetadata=resolve);await video.play();
            const HumanClass=window.Human?.Human||window.Human;const human=new HumanClass({modelBasePath:'https://cdn.jsdelivr.net/npm/@vladmandic/human@3.3.6/models',face:{enabled:true,detector:{rotation:true,maxDetected:2},mesh:{enabled:true},description:{enabled:true},antispoof:{enabled:true},liveness:{enabled:true}},body:{enabled:false},hand:{enabled:false},object:{enabled:false},gesture:{enabled:false}});await human.load();await human.warmup();
            const embeddings=[];let live=0,ticks=0;while(embeddings.length<2){const result=await human.detect(video),face=result.face?.length===1?result.face[0]:null;if(face?.embedding?.length===1024&&Number(face.real??0)>0.5&&Number(face.live??0)>0.5){live++;if(live>=3)embeddings.push(face.embedding.map(Number));faceStatus.textContent='Wajah terbaca. Tahan posisi sebentar...';}else{live=0;faceStatus.textContent=face?'Pastikan hanya satu wajah terlihat dan berkedip.':'Arahkan wajah ke kamera dengan pencahayaan cukup.';}if(++ticks>1800)throw new Error('Waktu verifikasi habis. Coba lagi.');}
            faceStatus.textContent='Memeriksa kecocokan wajah...';const result=await post(endpoint('face'),{challenge:challengeData.challenge,action,embeddings});success(result);
        } catch(error){faceStatus.textContent=error.message||'Verifikasi Face ID gagal.';faceStart.disabled=false;faceStart.textContent='Coba Face ID lagi';}
        finally{media?.getTracks().forEach(track=>track.stop());media=null;video.srcObject=null;}
    });
    window.addEventListener('pagehide',()=>media?.getTracks().forEach(track=>track.stop()),{once:true});

    const initial=@js(session('open_payroll_verification'));
    if(initial?.detail_id)open(initial.detail_id,initial.action||'download');
})();
</script>
@endpush
