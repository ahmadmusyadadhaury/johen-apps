<section>
    <div class="flex items-center gap-3 mb-6">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
        </div>
        <div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Informasi Profil</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui informasi profil akun Anda</p>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" name="name" type="text" class="input-field mt-1.5" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" name="username" type="text" class="input-field mt-1.5" :value="old('username', $user->username)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email untuk Verifikasi Slip Gaji')" />
            <x-text-input id="email" name="email" type="email" class="input-field mt-1.5" :value="old('email', $user->email)" autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            @if($user->email && $user->hasVerifiedEmail())
                <p class="mt-2 text-xs text-emerald-600">Email {{ $user->email }} sudah diverifikasi.</p>
            @elseif($user->email)
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <p class="text-xs text-amber-600">Email belum diverifikasi. Tautan dikirim otomatis saat email baru disimpan.</p>
                    <button form="send-verification" type="submit" class="text-xs font-semibold text-primary-600 hover:text-primary-500">Kirim ulang tautan</button>
                </div>
                @if(session('status') === 'verification-link-sent')
                    <p class="mt-2 text-xs text-emerald-600">Tautan verifikasi dikirim. Periksa kotak masuk email Anda.</p>
                @endif
                @if(session('email_verification_error'))
                    <p class="mt-2 text-xs text-red-600">{{ session('email_verification_error') }}</p>
                @endif
            @else
                <p class="mt-2 text-xs text-gray-500">Tambahkan email aktif untuk menggunakan verifikasi email saat membuka slip gaji.</p>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="btn-primary">Simpan Perubahan</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-emerald-600 font-medium"
                >Tersimpan!</p>
            @endif
        </div>
    </form>
</section>


