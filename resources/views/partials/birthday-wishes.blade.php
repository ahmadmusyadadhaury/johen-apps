@if(($birthdayWishes ?? collect())->isNotEmpty())
<div class="birthday-wishes pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
    @foreach($birthdayWishes as $i => $wish)
        @php
            $left = 50 + (($i * 13 + 5) % 44);
            $delay = fmod($i * 2.3, 10);
            $dur = 9 + fmod($i * 1.7, 6);
        @endphp
        <div class="bw-float" style="left: {{ $left }}%; animation-delay: {{ $delay }}s; animation-duration: {{ $dur }}s;">
            <div class="bw-bubble rounded-xl px-3 py-2 shadow-md ring-1 ring-white/20" style="background: rgba(255,255,255,0.95);">
                <p class="bw-msg text-[13px] font-medium leading-snug text-gray-800">"{{ $wish->message }}"</p>
                <span class="bw-sender mt-1 inline-block text-[11px] font-semibold text-gray-500">— {{ $wish->user?->employee?->nama ?? $wish->user?->name }}</span>
            </div>
        </div>
    @endforeach
</div>

<style>
    .birthday-wishes .bw-float {
        position: absolute;
        top: -90px;
        max-width: 44%;
        opacity: 0;
        animation-name: bw-fall;
        animation-timing-function: linear;
        animation-iteration-count: infinite;
        will-change: transform, opacity;
    }
    @keyframes bw-fall {
        0%   { transform: translateY(0) rotate(-4deg) scale(0.95); opacity: 0; }
        8%   { opacity: 1; }
        80%  { opacity: 1; }
        100% { transform: translateY(340px) rotate(4deg) scale(1); opacity: 0; }
    }
</style>
@endif
