@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Master Kantor</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Kelola identitas dan metode presensi setiap kantor</p>
    </div>
@endpush

<x-app-layout title="Master Kantor">
    <div class="mb-6 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-800 dark:border-blue-900 dark:bg-blue-950/40 dark:text-blue-200">
        Atur metode presensi dan titik koordinat kantor. Presensi Face ID hanya dapat dilakukan di dalam radius yang ditentukan.
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        @foreach($offices as $office)
            <section class="card overflow-hidden" aria-labelledby="office-title-{{ $office->id }}">
                <div class="flex items-start justify-between gap-4 border-b border-gray-100 px-5 py-4 dark:border-gray-800 sm:px-6">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 id="office-title-{{ $office->id }}" class="truncate text-base font-bold text-gray-900 dark:text-gray-100">
                                {{ $office->name }}
                            </h2>
                            <span class="{{ $office->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $office->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">{{ $office->code }} · /{{ $office->slug }}</p>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M8.25 7.5h1.5m-1.5 3.75h1.5m-1.5 3.75h1.5m4.5-7.5h1.5m-1.5 3.75h1.5m-1.5 3.75h1.5M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                    </div>
                </div>

                <form method="POST" action="{{ route('offices.update', $office) }}" class="space-y-5 p-5 sm:p-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="name-{{ $office->id }}" value="Nama Kantor" />
                            <x-text-input id="name-{{ $office->id }}" name="name" type="text" class="mt-1 block w-full" :value="old('name', $office->name)" required />
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-input-label for="code-{{ $office->id }}" value="Kode" />
                            <x-text-input id="code-{{ $office->id }}" name="code" type="text" class="mt-1 block w-full uppercase" :value="old('code', $office->code)" required />
                            @error('code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="slug-{{ $office->id }}" value="Slug" />
                            <x-text-input id="slug-{{ $office->id }}" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $office->slug)" required />
                            @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-input-label for="sort-order-{{ $office->id }}" value="Urutan" />
                            <x-text-input id="sort-order-{{ $office->id }}" name="sort_order" type="number" min="0" max="999" class="mt-1 block w-full" :value="old('sort_order', $office->sort_order)" required />
                            @error('sort_order') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <x-input-label for="address-{{ $office->id }}" value="Alamat" />
                        <textarea id="address-{{ $office->id }}" name="address" rows="3" class="mt-1 block w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('address', $office->address) }}</textarea>
                        @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-input-label for="attendance-method-{{ $office->id }}" value="Metode Presensi" />
                        <select id="attendance-method-{{ $office->id }}" name="attendance_method" class="mt-1 block w-full rounded-xl border-gray-200 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                            <option value="{{ \App\Models\Office::ATTENDANCE_MACHINE }}" @selected(old('attendance_method', $office->attendance_method) === \App\Models\Office::ATTENDANCE_MACHINE)>Mesin absensi</option>
                            <option value="{{ \App\Models\Office::ATTENDANCE_WEB_FACE }}" @selected(old('attendance_method', $office->attendance_method) === \App\Models\Office::ATTENDANCE_WEB_FACE)>Face ID melalui website</option>
                        </select>
                        @error('attendance_method') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/60">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Titik Presensi Face ID</h3>
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Wajib diisi jika metode presensi menggunakan Face ID website.</p>
                            </div>
                            <button type="button"
                                onclick="setOfficeAttendanceLocation({{ $office->id }})"
                                class="btn-secondary min-h-10 justify-center text-xs">
                                Gunakan Lokasi Perangkat
                            </button>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <x-input-label for="attendance-latitude-{{ $office->id }}" value="Latitude" />
                                <x-text-input id="attendance-latitude-{{ $office->id }}" name="attendance_latitude" type="number" step="0.0000001" min="-90" max="90" class="mt-1 block w-full" :value="old('attendance_latitude', $office->attendance_latitude)" placeholder="-6.0000000" />
                                @error('attendance_latitude') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <x-input-label for="attendance-longitude-{{ $office->id }}" value="Longitude" />
                                <x-text-input id="attendance-longitude-{{ $office->id }}" name="attendance_longitude" type="number" step="0.0000001" min="-180" max="180" class="mt-1 block w-full" :value="old('attendance_longitude', $office->attendance_longitude)" placeholder="107.0000000" />
                                @error('attendance_longitude') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="attendance-radius-{{ $office->id }}" value="Radius yang Diizinkan (meter)" />
                            <x-text-input id="attendance-radius-{{ $office->id }}" name="attendance_radius_meters" type="number" min="10" max="1000" class="mt-1 block w-full" :value="old('attendance_radius_meters', $office->attendance_radius_meters ?? 100)" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Disarankan 50–100 meter, menyesuaikan akurasi GPS di lokasi.</p>
                            @error('attendance_radius_meters') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <p id="attendance-location-status-{{ $office->id }}" class="mt-3 hidden text-xs font-medium"></p>
                    </div>

                    <div class="flex flex-col gap-4 border-t border-gray-100 pt-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
                        <label class="inline-flex cursor-pointer items-center gap-3">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $office->is_active)) class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-900">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Kantor aktif</span>
                        </label>

                        <x-primary-button type="submit">Simpan Perubahan</x-primary-button>
                    </div>
                </form>
            </section>
        @endforeach
    </div>

    @push('scripts')
        <script>
            function setOfficeAttendanceLocation(officeId) {
                const status = document.getElementById(`attendance-location-status-${officeId}`);
                if (!navigator.geolocation) {
                    status.textContent = 'Browser tidak mendukung pembacaan lokasi.';
                    status.className = 'mt-3 text-xs font-medium text-red-600 dark:text-red-400';
                    return;
                }

                status.textContent = 'Membaca lokasi perangkat...';
                status.className = 'mt-3 text-xs font-medium text-blue-600 dark:text-blue-400';
                navigator.geolocation.getCurrentPosition(position => {
                    document.getElementById(`attendance-latitude-${officeId}`).value = position.coords.latitude.toFixed(7);
                    document.getElementById(`attendance-longitude-${officeId}`).value = position.coords.longitude.toFixed(7);
                    status.textContent = `Lokasi berhasil diambil (akurasi ±${Math.round(position.coords.accuracy)} meter). Klik Simpan Perubahan.`;
                    status.className = 'mt-3 text-xs font-medium text-emerald-600 dark:text-emerald-400';
                }, error => {
                    status.textContent = error.message || 'Lokasi tidak dapat dibaca. Pastikan izin lokasi aktif.';
                    status.className = 'mt-3 text-xs font-medium text-red-600 dark:text-red-400';
                }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
            }
        </script>
    @endpush
</x-app-layout>
