@if($birthdayEmployee)
<div class="birthday-fireworks pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
    @php
        $colors = ['#f59e0b', '#f472b6', '#a78bfa', '#34d399', '#38bdf8', '#fb7185', '#fbbf24', '#f97316', '#e879f9', '#4ade80'];
    @endphp
    @for($i = 0; $i < 24; $i++)
        @php
            $left = ($i * 47 + 17) % 100;
            $delay = fmod($i * 1.3, 6);
            $dur = 3 + fmod($i * 0.83, 3);
            $size = 4 + ($i % 4);
        @endphp
        <span class="fw-particle" style="left: {{ $left }}%; width: {{ $size }}px; height: {{ $size }}px; background: {{ $colors[$i % 10] }}; animation-delay: {{ $delay }}s; animation-duration: {{ $dur }}s;"></span>
    @endfor
    @for($i = 0; $i < 12; $i++)
        @php
            $left = ($i * 71 + 13) % 100;
            $delay = fmod($i * 2.1, 6);
            $dur = 4 + fmod($i * 0.61, 3);
            $size = 5 + ($i % 3);
        @endphp
        <span class="fw-spark" style="left: {{ $left }}%; width: {{ $size }}px; height: {{ $size }}px; animation-delay: {{ $delay }}s; animation-duration: {{ $dur }}s;"></span>
    @endfor
    @for($b = 0; $b < 7; $b++)
        @php
            $left = ($b * 43 + 7) % 100;
            $top = 18 + ($b * 11) % 45;
            $delay = fmod($b * 1.7, 5);
            $color = $colors[($b * 3) % 10];
        @endphp
        <span class="fw-burst" style="left: {{ $left }}%; top: {{ $top }}%; animation-delay: {{ $delay }}s; color: {{ $color }};">
            @for($r = 0; $r < 16; $r++)
                @php $angle = $r * 22.5; @endphp
                <i class="fw-ray" style="transform: rotate({{ $angle }}deg) translateY(-2px);"></i>
            @endfor
            <i class="fw-flash"></i>
        </span>
    @endfor
    @for($rk = 0; $rk < 6; $rk++)
        @php
            $left = ($rk * 37 + 9) % 100;
            $delay = fmod($rk * 2.6, 7);
            $dur = 3.2 + fmod($rk * 0.9, 2);
            $color = $colors[($rk * 5 + 2) % 10];
            $drift = ($rk % 2 === 0 ? -1 : 1) * (10 + ($rk % 5) * 4);
        @endphp
        <span class="fw-rocket" style="left: {{ $left }}%; animation-delay: {{ $delay }}s; animation-duration: {{ $dur }}s; color: {{ $color }}; --drift: {{ $drift }}px;">
            <i class="fw-rkcore"></i>
            <i class="fw-rktrail"></i>
            <i class="fw-rkburst">
                @for($r = 0; $r < 20; $r++)
                    @php $angle = $r * 18; @endphp
                    <i class="fw-rkray" style="transform: rotate({{ $angle }}deg) translateY(-2px);"></i>
                @endfor
            </i>
            <i class="fw-rkflash"></i>
        </span>
    @endfor
</div>

<style>
    .birthday-fireworks .fw-particle {
        position: absolute;
        top: -12px;
        border-radius: 9999px;
        box-shadow: 0 0 6px 1px currentColor;
        opacity: 0;
        animation-name: fw-fall;
        animation-timing-function: linear;
        animation-iteration-count: infinite;
    }
    .birthday-fireworks .fw-spark {
        position: absolute;
        top: -10px;
        clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
        background: #fde68a;
        opacity: 0;
        animation-name: fw-fall;
        animation-timing-function: linear;
        animation-iteration-count: infinite;
    }
    .birthday-fireworks .fw-burst {
        position: absolute;
        width: 0;
        height: 0;
        opacity: 0;
        animation-name: fw-burst;
        animation-timing-function: ease-out;
        animation-iteration-count: infinite;
    }
    .birthday-fireworks .fw-ray {
        position: absolute;
        left: -2px;
        top: -2px;
        width: 4px;
        height: 34px;
        border-radius: 9999px;
        background: currentColor;
        box-shadow: 0 0 6px 1px currentColor;
        transform-origin: 2px 2px;
        opacity: 0.95;
    }
    .birthday-fireworks .fw-flash {
        position: absolute;
        left: -9px;
        top: -9px;
        width: 18px;
        height: 18px;
        border-radius: 9999px;
        background: radial-gradient(circle, #ffffff 0%, currentColor 55%, transparent 75%);
        filter: blur(0.5px);
    }
    .birthday-fireworks .fw-rocket {
        position: absolute;
        top: 100%;
        width: 0;
        height: 0;
        animation-name: fw-rise;
        animation-timing-function: ease-in;
        animation-iteration-count: infinite;
        --drift: 0px;
    }
    .birthday-fireworks .fw-rkcore {
        position: absolute;
        left: -3px;
        top: -3px;
        width: 6px;
        height: 6px;
        border-radius: 9999px;
        background: currentColor;
        box-shadow: 0 0 8px 2px currentColor, 0 0 16px 5px currentColor;
        animation-name: fw-rkcore;
        animation-timing-function: linear;
        animation-iteration-count: infinite;
        animation-delay: inherit;
        animation-duration: inherit;
    }
    .birthday-fireworks .fw-rktrail {
        position: absolute;
        left: -1px;
        top: 4px;
        width: 2px;
        height: 48px;
        background: linear-gradient(to bottom, currentColor, transparent);
        opacity: 0.85;
        animation-name: fw-rktrail;
        animation-timing-function: linear;
        animation-iteration-count: infinite;
        animation-delay: inherit;
        animation-duration: inherit;
    }
    .birthday-fireworks .fw-rkburst {
        position: absolute;
        width: 0;
        height: 0;
        animation-name: fw-rkburst;
        animation-timing-function: ease-out;
        animation-iteration-count: infinite;
        animation-delay: inherit;
        animation-duration: inherit;
    }
    .birthday-fireworks .fw-rkray {
        position: absolute;
        left: -2px;
        top: -2px;
        width: 3px;
        height: 42px;
        border-radius: 9999px;
        background: currentColor;
        box-shadow: 0 0 6px 1px currentColor;
        transform-origin: 2px 2px;
        opacity: 0.95;
    }
    .birthday-fireworks .fw-rkflash {
        position: absolute;
        left: -12px;
        top: -12px;
        width: 24px;
        height: 24px;
        border-radius: 9999px;
        background: radial-gradient(circle, #ffffff 0%, currentColor 55%, transparent 75%);
        animation-name: fw-rkflash;
        animation-timing-function: ease-out;
        animation-iteration-count: infinite;
        animation-delay: inherit;
        animation-duration: inherit;
    }
    @keyframes fw-rise {
        0%   { top: 100%; transform: translateX(0); }
        68%  { top: 16%; transform: translateX(var(--drift)); }
        100% { top: 16%; transform: translateX(var(--drift)); }
    }
    @keyframes fw-rkcore {
        0%, 66% { opacity: 1; transform: scale(1); }
        68%     { opacity: 0; transform: scale(0.4); }
        100%    { opacity: 0; transform: scale(0.4); }
    }
    @keyframes fw-rktrail {
        0%, 64% { opacity: 0.85; transform: scaleY(1); transform-origin: top; }
        68%     { opacity: 0; transform: scaleY(0.3); transform-origin: top; }
        100%    { opacity: 0; transform: scaleY(0.3); transform-origin: top; }
    }
    @keyframes fw-rkburst {
        0%, 66% { opacity: 0; transform: scale(0); }
        73%     { opacity: 1; transform: scale(0.4); }
        100%    { opacity: 0; transform: scale(1.6); }
    }
    @keyframes fw-rkflash {
        0%, 66% { opacity: 0; transform: scale(0.3); }
        70%     { opacity: 1; transform: scale(1); }
        80%     { opacity: 0.6; transform: scale(1.1); }
        100%    { opacity: 0; transform: scale(1.4); }
    }
    @keyframes fw-fall {
        0%   { transform: translateY(0) translateX(0) scale(0.6) rotate(0deg); opacity: 0; }
        10%  { opacity: 1; }
        60%  { opacity: 1; }
        100% { transform: translateY(560px) translateX(28px) scale(1.1) rotate(320deg); opacity: 0; }
    }
    @keyframes fw-burst {
        0%   { opacity: 0; transform: scale(0); }
        12%  { opacity: 1; }
        55%  { opacity: 1; transform: scale(1); }
        100% { opacity: 0; transform: scale(1.6); }
    }
</style>

<div class="relative z-10 mt-4 sm:mt-5 flex flex-wrap items-center gap-2.5 rounded-xl bg-white/10 backdrop-blur-sm px-4 py-2.5 ring-1 ring-white/20">
    <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v.5m-6-2.5v.5m4.875-1.75v.5m4.5-3.5v.5m-2.25-1.5v.5m.75-2.25v.5M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" /></svg>
    <p class="text-sm sm:text-base font-display font-bold text-white">Selamat Ulang Tahun, {{ $birthdayEmployee->nama }}! 🎉</p>
</div>
@endif
