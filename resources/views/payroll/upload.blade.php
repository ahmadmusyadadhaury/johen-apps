@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Upload Payroll</h1>
        <p class="text-xs text-gray-400 mt-0.5">Upload file Excel berisi data payroll karyawan</p>
    </div>
@endpush

<x-app-layout title="Upload Payroll">


    <div class="card p-6">
        <form action="{{ route('payroll.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Periode Payroll</label>
                <div class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        <select name="bulan" id="bulan"
                                class="appearance-none w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-10 pr-8 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200 cursor-pointer">
                            <option value="">Pilih Bulan</option>
                            <option value="Januari" {{ old('bulan') == 'Januari' ? 'selected' : '' }}>Januari</option>
                            <option value="Februari" {{ old('bulan') == 'Februari' ? 'selected' : '' }}>Februari</option>
                            <option value="Maret" {{ old('bulan') == 'Maret' ? 'selected' : '' }}>Maret</option>
                            <option value="April" {{ old('bulan') == 'April' ? 'selected' : '' }}>April</option>
                            <option value="Mei" {{ old('bulan') == 'Mei' ? 'selected' : '' }}>Mei</option>
                            <option value="Juni" {{ old('bulan') == 'Juni' ? 'selected' : '' }}>Juni</option>
                            <option value="Juli" {{ old('bulan') == 'Juli' ? 'selected' : '' }}>Juli</option>
                            <option value="Agustus" {{ old('bulan') == 'Agustus' ? 'selected' : '' }}>Agustus</option>
                            <option value="September" {{ old('bulan') == 'September' ? 'selected' : '' }}>September</option>
                            <option value="Oktober" {{ old('bulan') == 'Oktober' ? 'selected' : '' }}>Oktober</option>
                            <option value="November" {{ old('bulan') == 'November' ? 'selected' : '' }}>November</option>
                            <option value="Desember" {{ old('bulan') == 'Desember' ? 'selected' : '' }}>Desember</option>
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </div>
                    <div class="relative w-28">
                        <input type="number" name="tahun" id="tahun" value="{{ old('tahun', now()->year) }}"
                               class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 text-center font-semibold focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                    </div>
                </div>
                @error('bulan')
                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
                @error('tahun')
                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div
                x-data="{
                    dragging: false,
                    fileName: '',
                    fileSize: '',
                    uploaded: false,
                    init() {
                        this.$refs.fileInput.addEventListener('change', (e) => {
                            const file = e.target.files[0];
                            if (file) {
                                this.fileName = file.name;
                                this.fileSize = (file.size / 1024).toFixed(1) + ' KB';
                                this.uploaded = true;
                            }
                        });
                    }
                }"
                class="relative"
            >
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">File Excel</label>
                <div
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="
                        dragging = false;
                        const file = $event.dataTransfer.files[0];
                        if (file) {
                            $refs.fileInput.files = $event.dataTransfer.files;
                            fileName = file.name;
                            fileSize = (file.size / 1024).toFixed(1) + ' KB';
                            uploaded = true;
                            $refs.fileInput.dispatchEvent(new Event('change'));
                        }
                    "
                    :class="{
                        'border-primary-400 bg-primary-50': dragging,
                        'border-emerald-300 bg-emerald-50': uploaded && !dragging,
                    }"
                    class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-800 px-6 py-10 transition-all duration-200"
                >
                    <template x-if="!uploaded">
                        <div class="text-center">
                            <div class="flex justify-center mb-4">
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100">
                                    <svg class="w-7 h-7 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                    </svg>
                                </div>
                            </div>
                            <button type="button" @click="$refs.fileInput.click()" class="btn-primary text-sm inline-flex items-center gap-2 mx-auto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                Pilih File Excel
                            </button>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">atau drag & drop file ke sini</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Format: .xlsx, .xls, .csv (Maks. 10MB)</p>
                        </div>
                    </template>
                    <template x-if="uploaded">
                        <div class="text-center">
                            <div class="flex justify-center mb-4">
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100">
                                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="fileName"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="fileSize"></p>
                            <button type="button" @click="uploaded = false; $refs.fileInput.value = ''; fileName = ''; fileSize = ''" class="mt-2 text-xs font-medium text-primary-600 hover:text-primary-500 transition-colors">Ganti file</button>
                        </div>
                    </template>
                    <input
                        x-ref="fileInput"
                        type="file"
                        name="file"
                        accept=".xlsx,.xls,.csv"
                        class="hidden"
                    >
                </div>
                @error('file')
                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Upload & Validasi
                </button>
                <a href="{{ route('dashboard') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>

    <div class="card p-6 mt-4">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Urutan Kolom File Excel</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Baris pertama file Excel akan dianggap sebagai header dan dilewati. Urutan kolom wajib sebagai berikut:</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach([
                '1. NIK',
                '2. Nama',
                '3. Divisi',
                '4. Jabatan',
                '5. Gaji Pokok',
                '6. Tambahan Upah',
                '7. Bonus',
                '8. THR',
                '9. Apresiasi',
                '10. Tunjangan Jabatan',
                '11. Premi BPJS Kesehatan (4%)',
                '12. THR Dibayarkan',
                '13. Potongan Pinjaman',
                '14. Potongan Absensi',
                '15. Potongan BPJS Kesehatan (4%)',
                '16. Potongan BPJS Kesehatan (1%)',
                '17. Password PDF',
            ] as $col)
                <div class="flex items-center gap-2 rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 px-3 py-2">
                    <span class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ $col }}</span>
                </div>
            @endforeach
        </div>
    </div>

</x-app-layout>





