@props(['title', 'jenis' => 'pelatihan', 'description' => null, 'imageUrl' => null])

@php
    $normalizedTitle = \Illuminate\Support\Str::lower((string) $title);
    $thumbnailIcon = \Illuminate\Support\Str::contains($normalizedTitle, ['absen', 'presensi', 'qr', 'scan', 'mobile', 'hp'])
        ? 'phone'
        : (\Illuminate\Support\Str::contains($normalizedTitle, ['buku', 'manual', 'panduan', 'modul']) ? 'book' : 'presentation');
@endphp

<div class="relative isolate flex min-h-[150px] items-center overflow-hidden bg-gradient-to-r from-blue-700 via-blue-600 to-sky-400 sm:min-h-[156px]">
    @if($imageUrl)
        <img src="{{ $imageUrl }}" alt="Thumbnail {{ $title }}" class="absolute inset-0 h-full w-full object-cover" />
        <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-r from-blue-950/85 via-blue-900/55 to-transparent"></div>
    @else
        <div aria-hidden="true" class="absolute inset-0" style="background-image: radial-gradient(circle at 75% 20%, rgba(255,255,255,.42), transparent 32%), linear-gradient(125deg, transparent 48%, rgba(255,255,255,.12) 48.2%, transparent 70%)"></div>
        <div aria-hidden="true" class="absolute -right-16 -top-24 h-64 w-80 rotate-[-18deg] rounded-[45%] bg-sky-300/35"></div>
        <div aria-hidden="true" class="absolute -bottom-28 right-20 h-52 w-52 rounded-full bg-cyan-300/30 blur-sm"></div>
    @endif
    <div class="relative z-10 flex w-full items-center justify-between gap-3 px-5 py-5 sm:px-7">
        <div class="max-w-[68%] text-white">
            <span class="inline-flex rounded-full border border-white/20 bg-white/15 px-3 py-1 text-[10px] font-bold uppercase tracking-[.12em] shadow-sm backdrop-blur-sm">{{ $jenis }}</span>
            <h3 class="mt-2 line-clamp-2 break-words text-lg font-extrabold leading-tight sm:text-xl">{{ $title ?: 'Judul kegiatan' }}</h3>
            <p class="mt-1 line-clamp-2 max-w-md text-xs leading-relaxed text-blue-50 sm:text-sm">{{ $description ?: 'Kegiatan untuk meningkatkan keterampilan dan pengetahuan.' }}</p>
        </div>
        @unless($imageUrl)
        <div aria-hidden="true" class="relative flex h-[94px] w-[120px] shrink-0 items-center justify-center sm:h-[112px] sm:w-[176px]">
            <div class="absolute right-0 h-[76px] w-[100px] rounded-[1.6rem] bg-white/20 shadow-lg backdrop-blur-sm sm:h-[92px] sm:w-[142px]"></div>
            @if($thumbnailIcon === 'phone')
                <svg class="relative z-10 h-[82px] w-[110px] drop-shadow-xl sm:h-[100px] sm:w-[142px]" fill="none" viewBox="0 0 144 104" stroke-linecap="round" stroke-linejoin="round"><rect x="46" y="5" width="49" height="91" rx="10" fill="#f8fbff" stroke="#0754b8" stroke-width="5"/><rect x="54" y="17" width="33" height="59" rx="4" fill="#dbeeff"/><path d="m61 47 8 8 15-18" stroke="#08a889" stroke-width="6"/><path d="M65 85h11" stroke="#58a9ef" stroke-width="4"/></svg>
            @elseif($thumbnailIcon === 'book')
                <svg class="relative z-10 h-[82px] w-[110px] drop-shadow-xl sm:h-[100px] sm:w-[142px]" fill="none" viewBox="0 0 144 104" stroke-linecap="round" stroke-linejoin="round"><path d="M19 23c20-9 37-6 53 7v61C56 78 39 75 19 84V23Z" fill="#f8fbff" stroke="#0754b8" stroke-width="5"/><path d="M125 23c-20-9-37-6-53 7v61c16-13 33-16 53-7V23Z" fill="#dceeff" stroke="#0754b8" stroke-width="5"/><path d="M29 40c13-4 24-1 34 5M29 52c13-4 24-1 34 5M115 40c-13-4-24-1-34 5M115 52c-13-4-24-1-34 5" stroke="#47a6e9" stroke-width="4"/></svg>
            @else
                <svg class="relative z-10 h-[82px] w-[120px] drop-shadow-xl sm:h-[100px] sm:w-[150px]" fill="none" viewBox="0 0 152 104" stroke-linecap="round" stroke-linejoin="round"><rect x="13" y="12" width="101" height="63" rx="8" fill="#f8fbff" stroke="#0754b8" stroke-width="5"/><rect x="21" y="20" width="85" height="47" rx="3" fill="#cbe9ff"/><path d="M35 57h55" stroke="#5aa9e6" stroke-width="5"/><path d="m55 31 24 10-24 10V31Z" fill="#1769d2"/><path d="m36 83 93 0-13 9H24l12-9Z" fill="#e9f5ff" stroke="#0754b8" stroke-width="4"/><circle cx="124" cy="25" r="15" fill="#fff"/><path d="m114 23 10-5 10 5-10 5-10-5Zm4 3v6c4 3 8 3 12 0v-6" stroke="#1769d2" stroke-width="3"/></svg>
            @endif
        </div>
        @endunless
    </div>
</div>
