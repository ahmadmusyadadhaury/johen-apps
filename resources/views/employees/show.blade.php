@push('topbar-left')
    <div class="flex items-center gap-3">
        <a href="{{ route('hris.employees.index') }}" class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition-all hover:-translate-x-0.5">
            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Detail Karyawan</h1>
            <p class="text-xs text-gray-400 mt-0.5">HRIS &mdash; Data SDM &mdash; Karyawan</p>
        </div>
    </div>
@endpush

<x-app-layout title="Detail Karyawan">

    <div id="page-data"
         data-documents="{{ $employee->documents->toJson() }}"
         data-doc-success="{{ session('doc_success') }}"
         data-contracts="{{ json_encode($employee->contracts->map(fn($c) => [
            'id' => $c->id,
            'employee_id' => $c->employee_id,
            'jenis_kontrak' => $c->jenis_kontrak,
            'posisi' => $c->posisi,
            'atasan' => $c->atasan,
            'tanggal_mulai' => $c->tanggal_mulai?->format('Y-m-d'),
            'tanggal_berakhir' => $c->tanggal_berakhir?->format('Y-m-d'),
            'status' => $c->status,
            'keterangan' => $c->keterangan,
            'is_addendum' => $c->is_addendum ?? false,
            'created_at' => $c->created_at,
            'updated_at' => $c->updated_at,
        ])->values()) }}"
         data-contract-success="{{ session('contract_success') }}"
          data-position-histories="{{ json_encode($employee->positionHistories->map(fn($p) => [
            'id' => $p->id,
            'employee_id' => $p->employee_id,
            'jabatan' => $p->jabatan,
            'divisi' => $p->divisi,
            'atasan' => $p->atasan ?? '—',
            'mulai' => $p->mulai?->format('Y-m-d'),
            'selesai' => $p->selesai?->format('Y-m-d'),
            'status' => $p->status,
        ])->values()) }}"
          data-promotions="{{ json_encode($employee->promotions->map(fn($p) => [
            'id' => $p->id,
            'nomor_surat' => $p->nomor_surat,
            'posisi_lama' => $p->posisi_lama,
            'posisi_baru' => $p->posisi_baru,
            'divisi_lama' => $p->divisi_lama,
            'divisi_baru' => $p->divisi_baru,
            'atasan_lama' => $p->atasan_lama,
            'atasan_baru' => $p->atasan_baru,
            'tanggal_efektif' => $p->tanggal_efektif?->format('Y-m-d'),
            'jenis' => $p->jenis,
            'alasan' => $p->alasan,
            'pdf_path' => $p->pdf_path,
        ])->values()) }}"
          data-position-success="{{ session('position_success') }}"
          data-promotion-success="{{ session('promotion_success') }}"
          data-payroll-details="{{ json_encode($payrollDetails) }}"
          data-payroll-stats="{{ json_encode($stats) }}"
          class="hidden"></div>

    <div x-data="{
        activeTab: 'dasar',
        aksiOpen: false,
        editModal: false,
        dokumenModal: false,
        viewDokumen: null,
        deleteDokumenId: null,
        cariDokumen: '',
        selectedFile: null,
        showSuccess: false,
        successMessage: '',
        documents: [],
        kontrakModal: false,
        tambahKontrakModal: false,
        editKontrakModal: false,
        deleteKontrakId: null,
        viewKontrak: null,
        formKontrakJenis: '',
        formKontrakMulai: '',
        formKontrakBerakhir: '',
        formKontrakPosisi: '',
        formKontrakAtasan: '',
        formKontrakId: null,
        contracts: [],
        tambahJabatanModal: false,
        jabatanList: [],
        promosiModal: false,
        promosiList: [],
        formPromosiJenis: 'promosi',
        formPromosiPosisi: '',
        formPromosiDivisi: '',
        formPromosiAtasan: '',
        formPromosiTanggal: '',
        formPromosiAlasan: '',
        formPromosiNomor: '',
        hapusPromosiId: null,
        payrollList: [],
        payrollStats: { gaji_pokok: 0, total_tunjangan: 0, total_potongan: 0, gaji_bersih: 0 },
        tabs: ['dasar', 'dokumen', 'kontrak', 'jabatan', 'payroll'],
        init() {
            const data = document.getElementById('page-data');
            if (data) {
                try {
                    this.documents = JSON.parse(data.dataset.documents || '[]');
                } catch (e) { this.documents = []; }
                try {
                    this.contracts = JSON.parse(data.dataset.contracts || '[]');
                } catch (e) { this.contracts = []; }
                try {
                    this.jabatanList = JSON.parse(data.dataset.positionHistories || '[]');
                } catch (e) { this.jabatanList = []; }
                try {
                    this.promosiList = JSON.parse(data.dataset.promotions || '[]');
                } catch (e) { this.promosiList = []; }
                try {
                    this.payrollList = JSON.parse(data.dataset.payrollDetails || '[]');
                } catch (e) { this.payrollList = []; }
                try {
                    this.payrollStats = JSON.parse(data.dataset.payrollStats || '{}');
                } catch (e) { this.payrollStats = { gaji_pokok: 0, total_tunjangan: 0, total_potongan: 0, gaji_bersih: 0 }; }
                if (data.dataset.docSuccess || data.dataset.contractSuccess || data.dataset.positionSuccess || data.dataset.promotionSuccess) {
                    this.successMessage = data.dataset.docSuccess || data.dataset.contractSuccess || data.dataset.positionSuccess || data.dataset.promotionSuccess;
                    this.showSuccess = true;
                    setTimeout(() => this.showSuccess = false, 3000);
                }
            }
            if (window.location.hash) {
                const hash = window.location.hash.replace('#', '');
                if (this.tabs.includes(hash)) this.activeTab = hash;
            }
        },
        setTab(tab) {
            this.activeTab = tab;
            history.replaceState(null, '', '#' + tab);
        },
        get dokumenFiltered() {
            if (!this.cariDokumen) return this.documents;
            const q = this.cariDokumen.toLowerCase();
            return this.documents.filter(d => d.nama_dokumen.toLowerCase().includes(q) || d.jenis_dokumen.toLowerCase().includes(q));
        },
        get dokumenYangDihapus() {
            const doc = this.documents.find(d => d.id === this.deleteDokumenId);
            return doc ? doc.nama_dokumen : '';
        },
        get iconDokumen() {
            const icons = {
                'KTP': 'id-card',
                'KK': 'users',
                'NPWP': 'file-text',
                'Ijazah': 'book-open',
                'Sertifikat': 'award',
                'Kontrak': 'file-signature',
                'SK': 'scroll',
            };
            return icons[this.viewDokumen?.jenis_dokumen] || 'file';
        },
        get docUrl() {
            return this.viewDokumen?.file ? '/storage/documents/' + this.viewDokumen.file : null;
        },
        get docExt() {
            if (!this.viewDokumen?.file) return '';
            return this.viewDokumen.file.split('.').pop()?.toLowerCase() || '';
        },
        get docIsImage() {
            return ['jpg', 'jpeg', 'png'].includes(this.docExt);
        },
        get docIsPdf() {
            return this.docExt === 'pdf';
        },
        get kontrakDurasi() {
            if (!this.viewKontrak?.tanggal_mulai || !this.viewKontrak?.tanggal_berakhir) return '';
            const start = new Date(this.viewKontrak.tanggal_mulai);
            const end = new Date(this.viewKontrak.tanggal_berakhir);
            const months = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
            if (months < 1) return 'Kurang dari 1 bulan';
            return months + ' Bulan';
        },
        daysUntilEnd(k) {
            if (!k?.tanggal_berakhir || this.isKontrakSelesai(k)) return null;
            const end = new Date(k.tanggal_berakhir + 'T23:59:59');
            const now = new Date();
            return Math.ceil((end - now) / (1000 * 60 * 60 * 24));
        },
        isKontrakSelesai(k) {
            if (k.status === 'selesai') return true;
            if (k.status === 'berlaku' && k.tanggal_berakhir) {
                const end = new Date(k.tanggal_berakhir + 'T23:59:59');
                const now = new Date();
                return end < now;
            }
            return false;
        },
        editKontrak(k) {
            this.formKontrakJenis = k.jenis_kontrak;
            this.formKontrakMulai = k.tanggal_mulai;
            this.formKontrakBerakhir = k.tanggal_berakhir;
            this.formKontrakPosisi = k.posisi;
            this.formKontrakAtasan = k.atasan || '';
            this.formKontrakId = k.id;
            this.editKontrakModal = true;
        },
        openPromosiModal() {
            const currentPosisi = this.jabatanList.find(j => j.status === 'Aktif')?.jabatan || '';
            this.formPromosiPosisi = '';
            this.formPromosiDivisi = '';
            this.formPromosiAtasan = currentPosisi ? '' : '';
            this.formPromosiTanggal = new Date().toISOString().split('T')[0];
            this.formPromosiJenis = 'promosi';
            this.formPromosiAlasan = '';
            this.formPromosiNomor = '';
            this.promosiModal = true;
        },
    }" class="space-y-5">

        {{-- Hero Card --}}
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-blue-400 px-7 py-3 pb-8 relative">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_90%_0%,rgba(255,255,255,0.18),transparent_55%)] pointer-events-none"></div>
                <div class="flex items-center justify-between relative z-10 pt-5">
                    <div class="sm:ml-[148px] text-white text-2xl sm:text-3xl font-extrabold tracking-tight leading-tight">
                        @php $mainPos = $employee->mainPosition(); @endphp
                        {{ $mainPos?->nama ?? '—' }}
                        @if($employee->positions->count() > 1)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-lg bg-white/20 text-xs font-semibold">
                                +{{ $employee->positions->count() - 1 }} lainnya
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2.5">
                        @if(auth()->user()->can('update-data') || auth()->user()->employee_id === $employee->id)
                                    <button @click="editModal = true" class="inline-flex items-center gap-2 rounded-xl bg-white dark:bg-gray-900 px-4 py-2 text-sm font-semibold text-blue-700 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit Informasi
                        </button>
                        @endif
                        <div class="relative" @click.outside="aksiOpen = false">
                            <button @click="aksiOpen = !aksiOpen" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white border border-white/60 hover:bg-white/20 transition-all">
                                Aksi Lainnya
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                            </button>
                            <div x-show="aksiOpen" x-cloak @click="aksiOpen = false" class="absolute top-full right-0 mt-2 min-w-[190px] bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 py-1.5 z-50">
                                @can('create-data')
                                <button type="button" @click="aksiOpen = false; openPromosiModal()" class="w-full text-left px-3 py-2.5 text-sm font-medium text-violet-700 hover:bg-violet-50 flex items-center gap-2.5 rounded-lg">
                                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 15V3"/><path d="M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>
                                    Promosi / Mutasi
                                </button>
                                @endcan
                                <button type="button" class="w-full text-left px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-2.5 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 15V3"/><path d="M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>
                                    Ekspor Data
                                </button>
                                <button type="button" class="w-full text-left px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-2.5 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 8h20"/></svg>
                                    Cetak Kartu Pegawai
                                </button>
                                @can('delete-data')
                                <button type="button" class="w-full text-left px-3 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 flex items-center gap-2.5 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                    Hapus Karyawan
                                </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-7 pb-6 flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-6 -mt-12 sm:-mt-20">
                <div class="w-[120px] h-[120px] sm:w-[140px] sm:h-[140px] rounded-2xl bg-gray-100 dark:bg-gray-700 border-4 border-white dark:border-gray-800 shadow-xl flex-shrink-0 overflow-hidden relative group">
                    @php $canEditPhoto = auth()->user()->can('update-data') || auth()->user()->employee_id === $employee->id; @endphp
                    @if($canEditPhoto)
                    <form method="POST" action="{{ route('hris.employees.upload-photo', $employee) }}" enctype="multipart/form-data" id="photo-form-{{ $employee->id }}">
                        @csrf
                        <label for="photo-input-{{ $employee->id }}" class="block w-full h-full cursor-pointer">
                    @endif
                            @if($employee->foto_url)
                                <img src="{{ $employee->foto_url }}" alt="{{ $employee->nama }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-4xl sm:text-5xl font-bold text-white bg-gradient-to-br from-primary-500 to-violet-600 w-full h-full flex items-center justify-center">{{ strtoupper(substr($employee->nama, 0, 1)) }}</span>
                            @endif
                    @if($canEditPhoto)
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-2xl">
                                <div class="flex flex-col items-center gap-1">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h3.72a2 2 0 0 0 2-2 .996.996 0 0 1 1-.88h2.66M15 13a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path d="M21 16v4"/><path d="M19 18h4"/></svg>
                                    <span class="text-white text-[11px] font-semibold leading-tight text-center">Ubah Foto</span>
                                </div>
                            </div>
                        </label>
                        <input id="photo-input-{{ $employee->id }}" type="file" name="foto" accept="image/*" class="hidden" onchange="this.form.submit()">
                    </form>
                    @error('foto')
                        <p class="absolute -bottom-6 left-0 right-0 text-[11px] font-medium text-red-500 text-center">{{ $message }}</p>
                    @enderror
                    @endif
                </div>
                <div class="text-center sm:text-left sm:pt-20 pt-2">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5 mb-1.5">
                        <h2 class="text-xl font-extrabold text-gray-900 dark:text-gray-100">{{ $employee->nama }}</h2>
                        @php
                            $statusLabel = [
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                                'resign' => 'Resign',
                            ];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full {{ $statusClasses[$employee->status] ?? 'bg-gray-50 text-gray-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $employee->status === 'aktif' ? 'bg-emerald-600' : ($employee->status === 'nonaktif' ? 'bg-amber-600' : 'bg-red-600') }}"></span>
                            {{ $statusLabel[$employee->status] ?? ucfirst($employee->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2 sm:mb-2">
                        NIK <strong class="text-gray-700 dark:text-gray-200 font-semibold">{{ $employee->nik }}</strong>
                        &nbsp;&mdash;&nbsp; {{ $employee->positions->count() > 0 ? $employee->positions->pluck('nama')->implode(' & ') : '—' }}
                        &nbsp;&mdash;&nbsp; Divisi {{ $employee->division?->nama ?? '—' }}
                    </p>
                    <div class="flex items-center justify-center sm:justify-start gap-5 flex-wrap">
                        @if($employee->no_hp)
                        <span class="inline-flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            {{ $employee->no_hp }}
                        </span>
                        @endif
                        @if($employee->email)
                        <span class="inline-flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/></svg>
                            {{ $employee->email }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            </div>

        {{-- Tabs Card --}}
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            {{-- Tab Bar --}}
            <div class="flex gap-1 px-7 border-b border-gray-100 dark:border-gray-700 overflow-x-auto">
                <template x-for="tab in [
                    { key: 'dasar', label: 'Informasi Dasar' },
                    { key: 'dokumen', label: 'Dokumen' },
                    { key: 'kontrak', label: 'Riwayat Kontrak' },
                    { key: 'jabatan', label: 'Riwayat Jabatan' },
                    { key: 'payroll', label: 'Riwayat Payroll' },
                ]" :key="tab.key">
                    <button type="button"
                        @click="setTab(tab.key)"
                        class="relative px-1 py-4 text-sm font-semibold whitespace-nowrap mr-4 transition-colors"
                        :class="activeTab === tab.key ? 'text-blue-600' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100'"
                    >
                        <span x-text="tab.label"></span>
                        <span x-show="activeTab === tab.key"
                            class="absolute left-0 right-0 bottom-0 h-0.5 bg-blue-600 rounded-t-sm"></span>
                    </button>
                </template>
            </div>

            {{-- Panel: Informasi Dasar --}}
            <div x-show="activeTab === 'dasar'" class="p-7">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Informasi Pribadi --}}
                    <div class="border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden">
                        <div class="flex items-center gap-2 px-5 py-3.5 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600 text-sm font-bold text-gray-700 dark:text-gray-200">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Informasi Pribadi
                        </div>
                        <div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Nama Lengkap</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->nama }}</span>
                            </div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">NIK</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->nik }}</span>
                            </div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Status</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $statusLabel[$employee->status] ?? ucfirst($employee->status) }}</span>
                            </div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Tempat Lahir</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->tempat_lahir ?? '-' }}</span>
                            </div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Tanggal Lahir</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->tanggal_lahir?->isoFormat('D MMMM Y') ?? '-' }}</span>
                            </div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Jenis Kelamin</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">
                                    @if($employee->jenis_kelamin == 'L') Laki-laki
                                    @elseif($employee->jenis_kelamin == 'P') Perempuan
                                    @else -
                                    @endif
                                </span>
                            </div>
                            <div class="px-5 py-3 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Alamat Lengkap</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5 leading-relaxed">{{ $employee->alamat ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Data Pekerjaan --}}
                    <div class="border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden">
                        <div class="flex items-center gap-2 px-5 py-3.5 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600 text-sm font-bold text-gray-700 dark:text-gray-200">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M6 7V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v3"/></svg>
                            Data Pekerjaan
                        </div>
                        <div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Jabatan</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">
                                    @if($employee->positions->count() > 0)
                                        @foreach($employee->positions as $pos)
                                            <span class="inline-flex items-center gap-1.5 {{ !$loop->last ? 'mb-1' : '' }}">
                                                {{ $pos->nama }}
                                                @if($pos->pivot?->is_main)
                                                    <span class="text-[10px] font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/30 px-1.5 py-0.5 rounded">Utama</span>
                                                @endif
                                            </span>
                                            @if(!$loop->last)<br>@endif
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Divisi</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->division?->nama ?? '-' }}</span>
                            </div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Atasan 1</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->atasan ?? '-' }}</span>
                            </div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Atasan 2</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->atasan2 ?? '-' }}</span>
                            </div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Tanggal Bergabung</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->tanggal_masuk?->isoFormat('D MMMM Y') ?? '-' }}</span>
                            </div>
                            @if($employee->tanggal_resign)
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Tanggal Resign</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->tanggal_resign->isoFormat('D MMMM Y') }}</span>
                            </div>
                            @endif
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Jenis Karyawan</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->jenis_karyawan ?? '-' }}</span>
                            </div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Lokasi Kerja</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->lokasi_kerja ?? '-' }}</span>
                            </div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Jenis Kerja</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->jenis_kerja ?? '-' }}</span>
                            </div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Jam Kerja</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->jam_kerja ?? '-' }}</span>
                            </div>
                            <div class="px-5 py-3 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Jobdesk</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->jobdesk ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Kontak dan Darurat --}}
                    <div class="border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden">
                        <div class="flex items-center gap-2 px-5 py-3.5 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600 text-sm font-bold text-gray-700 dark:text-gray-200">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            Kontak dan Darurat
                        </div>
                        <div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Nomor Telepon</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->no_hp ?? '-' }}</span>
                            </div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Email</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->email ?? '-' }}</span>
                            </div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Kontak Darurat 1</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">
                                    @if($employee->no_kontak_darurat1 && $employee->hubungan_darurat1)
                                        {{ $employee->no_kontak_darurat1 }} ({{ $employee->hubungan_darurat1 }})
                                    @elseif($employee->no_kontak_darurat1)
                                        {{ $employee->no_kontak_darurat1 }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">Kontak Darurat 2</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">
                                    @if($employee->no_kontak_darurat2 && $employee->hubungan_darurat2)
                                        {{ $employee->no_kontak_darurat2 }} ({{ $employee->hubungan_darurat2 }})
                                    @elseif($employee->no_kontak_darurat2)
                                        {{ $employee->no_kontak_darurat2 }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="px-5 py-3 last:border-b-0">
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">BPJS Kesehatan</span>
                                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $employee->no_bpjs ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel: Dokumen --}}
            <div x-show="activeTab === 'dokumen'" x-cloak class="p-7">
                <div class="flex items-center justify-between gap-4 mb-5 flex-wrap">
                    <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-0.5 w-80 focus-within:border-blue-500 focus-within:bg-white dark:focus-within:bg-gray-800 focus-within:shadow-sm transition-all">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <input type="text" x-model="cariDokumen" placeholder="Cari Dokumen" class="border-none outline-none bg-transparent text-sm text-gray-900 dark:text-gray-100 w-full placeholder:text-gray-400">
                    </div>
                    <button @click="dokumenModal = true"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                        Tambah Dokumen
                    </button>
                </div>

                <div class="grid grid-cols-[repeat(auto-fill,minmax(190px,1fr))] gap-4" x-show="dokumenFiltered.length > 0 || cariDokumen">
                    <template x-for="doc in dokumenFiltered" :key="doc.id">
                        <div class="border border-gray-200 dark:border-gray-600 rounded-2xl p-4 bg-white dark:bg-gray-900 hover:shadow-md hover:border-blue-100 dark:hover:border-blue-800 hover:-translate-y-0.5 transition-all">
                            <div class="w-[42px] h-[42px] rounded-xl bg-blue-50 dark:bg-blue-950 flex items-center justify-center mb-3.5 group-hover:bg-blue-100">
                                <svg class="w-[21px] h-[21px] text-blue-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                            </div>
                            <div class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-0.5 truncate" x-text="doc.nama_dokumen"></div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mb-3.5" x-text="doc.jenis_dokumen"></div>
                            <div class="flex gap-2">
                                <button @click="viewDokumen = doc"
                                        class="flex-1 border-none rounded-lg py-1.5 text-xs font-semibold flex items-center justify-center gap-1 cursor-pointer bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900 transition-all active:scale-96">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Lihat
                                </button>
                                <button @click="deleteDokumenId = doc.id"
                                        class="flex-1 border-none rounded-lg py-1.5 text-xs font-semibold flex items-center justify-center gap-1 cursor-pointer bg-red-50 dark:bg-red-950 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900 transition-all active:scale-96">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </template>

                     <div x-show="!cariDokumen" @click="dokumenModal = true"
                          class="border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-2xl flex flex-col items-center justify-center gap-2 text-gray-400 dark:text-gray-500 min-h-[172px] cursor-pointer hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950 hover:text-blue-600 transition-all active:scale-[.98]">
                        <svg class="w-[26px] h-[26px]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                        <span class="text-xs font-semibold">Tambah Dokumen</span>
                    </div>
                </div>

                <div x-show="dokumenFiltered.length === 0 && !cariDokumen" class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
                    <svg class="w-11 h-11 text-gray-300 dark:text-gray-600 mb-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Belum Ada Dokumen</h4>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Klik tombol "Tambah Dokumen" untuk mengunggah dokumen pertama.</p>
                </div>

                <div x-show="dokumenFiltered.length === 0 && cariDokumen" class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
                    <svg class="w-11 h-11 text-gray-300 dark:text-gray-600 mb-3.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Dokumen tidak ditemukan</h4>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Coba kata kunci lain atau tambah dokumen baru.</p>
                </div>

                {{-- Modal Tambah Dokumen --}}
                <div x-show="dokumenModal" x-cloak
                     x-transition:enter="transition-opacity ease-linear duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-linear duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-[200] flex items-center justify-center p-5 bg-gray-900/50 backdrop-blur-sm"
                     @click="dokumenModal = false">
                    <div x-show="dokumenModal" x-cloak
                         x-transition:enter="transition-all ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition-all ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.stop
                          class="w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-xl max-h-[90vh] flex flex-col overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-700 shrink-0">
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Tambah Dokumen</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Unggah dokumen baru untuk karyawan ini</p>
                            </div>
                            <button @click="dokumenModal = false" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-500 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <form action="{{ route('hris.employees.store-document', $employee) }}" method="POST" enctype="multipart/form-data" class="overflow-y-auto p-6 space-y-4">
                            @csrf
                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Nama Dokumen <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_dokumen" required placeholder="Contoh: KTP (Kartu Tanda Penduduk)"
                                       class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Kategori <span class="text-red-500">*</span></label>
                                <select name="jenis_dokumen" required
                                        class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all appearance-none bg-[url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 20 20%27 fill=%27none%27 stroke=%27%236b7280%27 stroke-width=%272%27%3E%3Cpath d=%27M5 7l5 5 5-5%27/%3E%3C/svg%3E')] bg-no-repeat bg-[right_12px_center] pr-9">
                                    <option value="">Pilih kategori dokumen</option>
                                    @foreach($jenisDokumenList as $jenis)
                                        <option value="{{ $jenis }}">{{ $jenis }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">File Dokumen <span class="text-red-500">*</span></label>
                                <div class="border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-xl py-6 px-4 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950 transition-all"
                                     @click="$event.target.closest('div').querySelector('input[type=file]').click()"
                                     :class="selectedFile ? 'border-solid border-blue-300 bg-blue-50 dark:bg-blue-950' : ''">
                                    <svg class="w-[26px] h-[26px] mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 15V3"/><path d="m7 8 5-5 5 5"/><path d="M5 21h14"/></svg>
                                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Klik atau seret file ke sini</div>
                                    <div class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">PDF, JPG, atau PNG — maks 5MB</div>
                                    <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png" class="hidden"
                                           @change="selectedFile = $event.target.files[0]?.name || null">
                                </div>
                                <div x-show="selectedFile" class="flex items-center gap-2.5 bg-white dark:bg-gray-900 border border-blue-100 dark:border-blue-900 rounded-xl px-3 py-2 mt-2 text-xs text-gray-700 dark:text-gray-300">
                                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                                    <span class="flex-1 truncate" x-text="selectedFile"></span>
                                    <button type="button" @click="selectedFile = null; $el.closest('div').parentElement.querySelector('input[type=file]').value = ''" class="text-gray-400 hover:text-red-500 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1.5">Format: PDF, JPG, PNG. Maks: 5MB.</p>
                            </div>
                            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <button type="button" @click="dokumenModal = false"
                                        class="btn-ghost px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">
                                    Simpan Dokumen
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Modal Lihat Dokumen --}}
                <div x-show="viewDokumen" x-cloak
                     x-transition:enter="transition-opacity ease-linear duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-linear duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-[200] flex items-center justify-center p-5 bg-gray-900/50 backdrop-blur-sm"
                     @click="viewDokumen = null">
                    <div x-show="viewDokumen" x-cloak
                         x-transition:enter="transition-all ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition-all ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.stop
                          class="w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100" x-text="viewDokumen?.nama_dokumen"></h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="viewDokumen?.jenis_dokumen"></p>
                            </div>
                            <button @click="viewDokumen = null" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-500 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="p-6 text-center">
                            <template x-if="docIsImage">
                                <img :src="docUrl" :alt="viewDokumen?.nama_dokumen"
                                     class="w-full max-h-64 object-contain rounded-xl border border-gray-200 dark:border-gray-600">
                            </template>
                            <template x-if="docIsPdf">
                                <iframe :src="docUrl" class="w-full h-64 rounded-xl border border-gray-200 dark:border-gray-600"></iframe>
                            </template>
                            <template x-if="!docIsImage && !docIsPdf">
                                <div class="w-full bg-gray-50 dark:bg-gray-700/50 border border-dashed border-gray-200 dark:border-gray-600 rounded-xl py-12 px-4 flex flex-col items-center gap-2.5 text-gray-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Pratinjau tidak tersedia untuk jenis file ini</span>
                                </div>
                            </template>
                        </div>
                        <div class="flex items-center justify-end gap-2.5 px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                            <button @click="viewDokumen = null"
                                    class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                                Tutup
                            </button>
                            <a :href="viewDokumen?.id ? '{{ route('hris.employees.download-document', [$employee, '__DOCID__']) }}'.replace('__DOCID__', viewDokumen.id) : '#'"
                               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 15V3"/><path d="M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>
                                Unduh
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Modal Konfirmasi Hapus --}}
                <div x-show="deleteDokumenId" x-cloak
                     x-transition:enter="transition-opacity ease-linear duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-linear duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-[200] flex items-center justify-center p-5 bg-gray-900/50 backdrop-blur-sm"
                     @click="deleteDokumenId = null">
                    <div x-show="deleteDokumenId" x-cloak
                         x-transition:enter="transition-all ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition-all ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.stop
                          class="w-full max-w-sm bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-7 pt-9 text-center">
                            <div class="w-[52px] h-[52px] rounded-2xl bg-red-50 dark:bg-red-950 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-[26px] h-[26px] text-red-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                            </div>
                            <h4 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-1.5">Hapus dokumen ini?</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                Dokumen <b class="text-gray-700 dark:text-gray-300" x-text="dokumenYangDihapus"></b> akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                        <div class="flex items-center justify-center gap-2.5 px-6 pb-7">
                            <button @click="deleteDokumenId = null"
                                    class="flex-1 justify-center px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                                Batal
                            </button>
                            <form method="POST" :action="deleteDokumenId ? '{{ route('hris.employees.destroy-document', [$employee, '__DOCID__']) }}'.replace('__DOCID__', deleteDokumenId) : '#'" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full justify-center px-5 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-all shadow-sm">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Modal Sukses --}}
                <div x-show="showSuccess" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="fixed inset-0 z-[300] flex items-center justify-center p-5 bg-gray-900/60 backdrop-blur-sm"
                     @click="showSuccess = false">
                    <div @click.stop
                         class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 mx-auto mb-4">
                            <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center mb-2">Berhasil!</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6" x-text="successMessage"></p>
                        <div class="flex items-center justify-center pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button @click="showSuccess = false" class="btn-primary text-xs px-8">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel: Riwayat Kontrak --}}
            <div x-show="activeTab === 'kontrak'" x-cloak class="p-7">
                <div class="flex items-center justify-between gap-4 mb-5 flex-wrap">
                    <div class="flex items-center gap-2 text-base font-bold text-gray-900 dark:text-gray-100">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Riwayat Kontrak
                    </div>
                    <button @click="tambahKontrakModal = true"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                        Tambah Kontrak
                    </button>
                </div>

                <template x-if="contracts.length > 0">
                    <div class="flex flex-col gap-0 pl-11 relative">
                        <div class="absolute left-[21px] top-2 bottom-2 w-0.5 bg-gray-200 dark:bg-gray-600"></div>
                        <template x-for="(k, i) in contracts" :key="k.id">
                            <div class="flex relative pb-7 last:pb-0">
                                <div class="absolute left-[-44px] top-0 bottom-0 w-11 flex items-start justify-center">
                                    <div class="w-5 h-5 rounded-full bg-white dark:bg-gray-900 border-2 flex items-center justify-center flex-shrink-0 z-10 mt-1"
                                         :class="isKontrakSelesai(k) ? 'border-emerald-500 bg-emerald-50' : 'border-blue-500 bg-blue-500 shadow-[0_0_0_4px_rgba(37,99,235,0.15)]'">
                                         <template x-if="isKontrakSelesai(k)">
                                            <svg class="w-2.5 h-2.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                                        </template>
                                         <template x-if="!isKontrakSelesai(k)">
                                            <div class="w-2 h-2 rounded-full bg-white dark:bg-gray-300 animate-pulse"></div>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex-1 border border-gray-200 dark:border-gray-600 rounded-xl p-4 bg-gray-50/70 dark:bg-gray-700/30 hover:shadow-sm hover:border-blue-100 dark:hover:border-blue-800 transition-all">
                                    <div class="flex justify-between items-start gap-3 mb-2.5">
                                        <div class="flex flex-col gap-1">
                                            <div class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                                <span x-text="k.jenis_kontrak"></span>
                                                <span x-show="k.is_addendum" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">Addendum</span>
                                            </div>
                                            <div class="flex gap-4 text-xs text-gray-500 dark:text-gray-400">
                                                <span>Mulai: <b class="text-gray-700 dark:text-gray-300 font-semibold" x-text="k.tanggal_mulai"></b></span>
                                                <span>Berakhir: <b class="text-gray-700 dark:text-gray-300 font-semibold" x-text="k.tanggal_berakhir"></b></span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full whitespace-nowrap"
                                                  :class="isKontrakSelesai(k) ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700'"
                                                  x-text="isKontrakSelesai(k) ? 'Selesai' : 'Berlaku'"></span>
                                            <span x-show="daysUntilEnd(k) !== null && daysUntilEnd(k) <= 7"
                                                  class="text-xs font-bold px-2.5 py-0.5 rounded-full whitespace-nowrap"
                                                  :class="daysUntilEnd(k) !== null && daysUntilEnd(k) <= 3 ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600'"
                                                  x-text="daysUntilEnd(k) !== null && daysUntilEnd(k) <= 3 ? 'Segera Habis' : 'Akan Berakhir'"></span>
                                        </div>
                                    </div>
                                    <div class="border-t border-gray-100 dark:border-gray-700 my-3"></div>
                                    <div class="flex justify-between items-center">
                                        <div class="text-xs text-gray-700 dark:text-gray-300">
                                            <b class="font-bold text-gray-900 dark:text-gray-100">{{ $employee->nama }}</b>
                                            <span class="text-gray-400 mx-1.5">—</span>
                                            <span x-text="k.posisi"></span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button @click="viewKontrak = k"
                                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                                                Lihat Kontrak
                                            </button>
                                            <button @click="editKontrak(k)"
                                                    class="p-1.5 text-gray-400 dark:text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950 rounded-lg transition-all" title="Edit Kontrak">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                            </button>
                                            <button @click="deleteKontrakId = k.id"
                                                    class="p-1.5 text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950 rounded-lg transition-all" title="Hapus Kontrak">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="contracts.length === 0">
                    <div class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/></svg>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Belum Ada Riwayat Kontrak</h4>
                        <p class="text-xs">Klik tombol "Tambah Kontrak" untuk membuat kontrak baru.</p>
                    </div>
                </template>
            </div>

             {{-- Panel: Riwayat Jabatan --}}
            <div x-show="activeTab === 'jabatan'" x-cloak class="p-7">
                <div class="flex items-center justify-between gap-4 mb-5 flex-wrap">
                    <div class="flex items-center gap-2 text-base font-bold text-gray-900 dark:text-gray-100">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m3 17 6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg>
                        Riwayat Jabatan
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button @click="openPromosiModal()"
                                class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700 transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 15V3"/><path d="M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>
                            Promosi / Mutasi
                        </button>
                        <button @click="tambahJabatanModal = true"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                            Tambah Jabatan
                        </button>
                    </div>
                </div>

                <template x-if="jabatanList.length > 0">
                    <div class="border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider w-12">No</th>
                                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Jabatan</th>
                                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Divisi</th>
                                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Atasan</th>
                                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Mulai</th>
                                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Selesai</th>
                                    <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(j, idx) in jabatanList" :key="j.id">
                                    <tr class="border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors">
                                        <td class="px-4 py-3.5 text-sm text-gray-400 dark:text-gray-500 font-medium" x-text="idx + 1"></td>
                                        <td class="px-4 py-3.5 text-sm font-bold text-gray-900 dark:text-gray-100" x-text="j.jabatan"></td>
                                        <td class="px-4 py-3.5 text-sm text-gray-700 dark:text-gray-300" x-text="j.divisi"></td>
                                        <td class="px-4 py-3.5 text-sm text-gray-700 dark:text-gray-300" x-text="j.atasan"></td>
                                        <td class="px-4 py-3.5 text-sm text-gray-700 dark:text-gray-300" x-text="j.mulai"></td>
                                        <td class="px-4 py-3.5 text-sm text-gray-700 dark:text-gray-300" x-text="j.selesai"></td>
                                        <td class="px-4 py-3.5">
                                            <span class="inline-flex text-xs font-bold px-2.5 py-0.5 rounded-full"
                                                  :class="j.status === 'Aktif' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700'"
                                                  x-text="j.status"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template x-if="jabatanList.length === 0">
                    <div class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500 border border-gray-200 dark:border-gray-600 rounded-xl">
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m3 17 6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Belum Ada Riwayat Jabatan</h4>
                        <p class="text-xs">Klik tombol "Tambah Jabatan" untuk menambahkan riwayat jabatan baru.</p>
                    </div>
                </template>

                {{-- Riwayat Promosi --}}
                <template x-if="promosiList.length > 0">
                    <div class="mt-8">
                        <div class="flex items-center gap-2 text-base font-bold text-gray-900 dark:text-gray-100 mb-4">
                            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 15V3"/><path d="M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>
                            Riwayat Promosi / Mutasi
                        </div>
                            <div class="border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider w-12">No</th>
                                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Tanggal Efektif</th>
                                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Jenis</th>
                                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Posisi</th>
                                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Alasan</th>
                                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Surat</th>
                                        <th class="text-center px-4 py-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider w-16">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(p, idx) in promosiList" :key="p.id">
                                        <tr class="border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors">
                                            <td class="px-4 py-3.5 text-sm text-gray-400 dark:text-gray-500 font-medium" x-text="idx + 1"></td>
                                            <td class="px-4 py-3.5 text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="p.tanggal_efektif"></td>
                                            <td class="px-4 py-3.5">
                                                <span class="inline-flex text-xs font-bold px-2.5 py-0.5 rounded-full"
                                                      :class="p.jenis === 'promosi' ? 'bg-green-50 text-green-700' : (p.jenis === 'demosi' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700')"
                                                      x-text="p.jenis.charAt(0).toUpperCase() + p.jenis.slice(1)"></span>
                                            </td>
                                            <td class="px-4 py-3.5 text-sm text-gray-700 dark:text-gray-300">
                                                <span x-text="p.posisi_lama"></span>
                                                <svg class="w-4 h-4 inline mx-1 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                                <span class="font-bold" x-text="p.posisi_baru"></span>
                                            </td>
                                            <td class="px-4 py-3.5 text-sm text-gray-500 dark:text-gray-400 max-w-[200px] truncate" x-text="p.alasan || '—'"></td>
                                            <td class="px-4 py-3.5 text-center">
                                                <a x-show="p.pdf_path"
                                                   :href="'{{ route('hris.employees.download-promotion-pdf', [$employee, '__PROMOID__']) }}'.replace('__PROMOID__', p.id)"
                                                   class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-violet-700 bg-violet-50 hover:bg-violet-100 transition-all">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 15V3"/><path d="M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>
                                                    PDF
                                                </a>
                                                <span x-show="!p.pdf_path" class="text-xs text-gray-400 dark:text-gray-500">—</span>
                                            </td>
                                            <td class="px-4 py-3.5 text-center">
                                                <button @click="hapusPromosiId = p.id"
                                                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition-all">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Panel: Riwayat Payroll --}}
            <div x-show="activeTab === 'payroll'" x-cloak class="p-7">
                {{-- Table --}}
                <div x-show="payrollList.length > 0">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-600 overflow-hidden">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <th class="text-left px-4 py-3.5 w-14">No</th>
                                    <th class="text-left px-4 py-3.5">Periode</th>
                                    <th class="text-right px-4 py-3.5">Gaji Pokok</th>
                                    <th class="text-right px-4 py-3.5">Tunjangan</th>
                                    <th class="text-right px-4 py-3.5">Potongan</th>
                                    <th class="text-right px-4 py-3.5">Gaji Bersih</th>
                                    <th class="text-center px-4 py-3.5">Status</th>
                                    <th class="text-center px-4 py-3.5">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(p, i) in payrollList" :key="p.id">
                                    <tr class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors">
                                        <td class="px-4 py-3.5 text-sm text-gray-500 dark:text-gray-400" x-text="i + 1"></td>
                                        <td class="px-4 py-3.5 text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="p.periode"></td>
                                        <td class="px-4 py-3.5 text-sm text-right font-medium text-gray-900 dark:text-gray-100" x-text="'Rp ' + Number(p.gaji_pokok).toLocaleString('id-ID')"></td>
                                        <td class="px-4 py-3.5 text-sm text-right font-medium text-emerald-600" x-text="'Rp ' + Number(p.tambahan_upah + p.bonus + p.thr + p.apresiasi + p.tunjangan_jabatan).toLocaleString('id-ID')"></td>
                                        <td class="px-4 py-3.5 text-sm text-right font-medium text-red-600" x-text="'Rp ' + Number(p.thr_dibayarkan + p.potongan_pinjaman + p.potongan_absensi).toLocaleString('id-ID')"></td>
                                        <td class="px-4 py-3.5 text-sm text-right font-bold text-gray-900 dark:text-gray-100" x-text="'Rp ' + Number(p.take_home_pay).toLocaleString('id-ID')"></td>
                                        <td class="px-4 py-3.5 text-center">
                                            <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full"
                                                  :class="p.status === 'sent' ? 'bg-emerald-50 text-emerald-700' : (p.status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700')">
                                                <span class="w-1.5 h-1.5 rounded-full"
                                                      :class="p.status === 'sent' ? 'bg-emerald-600' : (p.status === 'pending' ? 'bg-amber-600' : 'bg-red-600')"></span>
                                                <span x-text="p.status === 'sent' ? 'Terkirim' : (p.status === 'pending' ? 'Tertunda' : 'Gagal')"></span>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            <a :href="`/payroll/detail/${p.id}/download`"
                                               class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-primary-600 hover:bg-primary-50 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                                Download PDF
                                            </a>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Empty State --}}
                <div x-show="payrollList.length === 0"
                     class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Belum Ada Riwayat Payroll</h4>
                    <p class="text-xs">Riwayat payroll akan tersedia setelah fitur aktif.</p>
                </div>
            </div>


        </div>

    {{-- Edit Informasi Modal --}}
    <div x-show="editModal" x-cloak
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
         @click="editModal = false">
        <div x-show="editModal" x-cloak
             x-transition:enter="transition-all ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             class="w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 shrink-0">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Edit Informasi Karyawan</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Perbarui data profil karyawan</p>
                </div>
                <button @click="editModal = false" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('hris.employees.update', $employee) }}" method="POST" class="overflow-y-auto p-6 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="_redirect" value="show">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $employee->nama) }}" required
                           class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik', $employee->nik) }}" required
                           class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Divisi</label>
                        <select name="division_id"
                                class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all">
                            <option value="">Pilih Divisi</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ $employee->division_id == $division->id ? 'selected' : '' }}>{{ $division->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-data="{ open: false }" class="relative">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Jabatan</label>
                        <input type="hidden" name="position" value="{{ old('position', $employee->position) }}">
                        @php $selectedIds = old('position_ids', $employee->positions->pluck('id')->toArray()); @endphp
                        <button type="button" @click="open = !open"
                                class="flex items-center justify-between w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all">
                            <span>{{ count($selectedIds) > 0 ? count($selectedIds) . ' jabatan dipilih' : 'Pilih jabatan' }}</span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute z-20 mt-1 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 shadow-lg max-h-48 overflow-y-auto p-1.5 space-y-0.5">
                            @foreach($allPositions as $pos)
                                <label class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors {{ in_array($pos->id, $selectedIds) ? 'bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                    <input type="checkbox" name="position_ids[]" value="{{ $pos->id }}"
                                           {{ in_array($pos->id, $selectedIds) ? 'checked' : '' }}
                                           class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300 flex-1">{{ $pos->nama }}</span>
                                    <input type="radio" name="main_position_id" value="{{ $pos->id }}"
                                           {{ $employee->mainPosition()?->id === $pos->id ? 'checked' : '' }}
                                           onclick="event.stopPropagation()"
                                           class="text-primary-600 focus:ring-primary-500">
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500">Utama</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Atasan 1</label>
                        <input type="text" name="atasan" value="{{ old('atasan', $employee->atasan) }}"
                               class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Atasan 2</label>
                        <input type="text" name="atasan2" value="{{ old('atasan2', $employee->atasan2) }}"
                               class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Status</label>
                    <select name="status" required
                            class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all">
                        <option value="aktif" {{ $employee->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $employee->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="resign" {{ $employee->status == 'resign' ? 'selected' : '' }}>Resign</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">No. HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $employee->no_hp) }}"
                               class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                               class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" @click="editModal = false"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl transition-all">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Lihat Kontrak --}}
    <div x-show="viewKontrak" x-cloak
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[200] flex items-center justify-center p-5 bg-gray-900/50 backdrop-blur-sm"
         @click="viewKontrak = null">
        <div x-show="viewKontrak" x-cloak
             x-transition:enter="transition-all ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             class="w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100" x-text="viewKontrak?.jenis_kontrak"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-2">
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full"
                              :class="isKontrakSelesai(viewKontrak) ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700'"
                              x-text="isKontrakSelesai(viewKontrak) ? 'Selesai' : 'Berlaku'"></span>
                    </p>
                </div>
                <button @click="viewKontrak = null" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-500 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <div class="text-[11.5px] font-medium text-gray-400 dark:text-gray-500 mb-0.5">Nama Karyawan</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->nama }}</div>
                    </div>
                    <div>
                        <div class="text-[11.5px] font-medium text-gray-400 dark:text-gray-500 mb-0.5">Posisi / Jabatan</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="viewKontrak?.posisi"></div>
                    </div>
                    <div>
                        <div class="text-[11.5px] font-medium text-gray-400 dark:text-gray-500 mb-0.5">Mulai</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="viewKontrak?.tanggal_mulai"></div>
                    </div>
                    <div>
                        <div class="text-[11.5px] font-medium text-gray-400 dark:text-gray-500 mb-0.5">Berakhir</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="viewKontrak?.tanggal_berakhir"></div>
                    </div>
                    <div>
                        <div class="text-[11.5px] font-medium text-gray-400 dark:text-gray-500 mb-0.5">Durasi</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="kontrakDurasi"></div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2.5 px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="viewKontrak = null"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    Tutup
                </button>
                <button @click="viewKontrak = null; editKontrak(viewKontrak)"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                    Edit
                </button>
                <button x-show="!isKontrakSelesai(viewKontrak)"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                    Perpanjang Kontrak
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Kontrak --}}
    <div x-show="tambahKontrakModal" x-cloak
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[200] flex items-center justify-center p-5 bg-gray-900/50 backdrop-blur-sm"
         @click="tambahKontrakModal = false">
        <div x-show="tambahKontrakModal" x-cloak
             x-transition:enter="transition-all ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             class="w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-700 shrink-0">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Tambah Kontrak</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Buat kontrak baru untuk karyawan ini</p>
                </div>
                <button @click="tambahKontrakModal = false" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-500 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('hris.employees.store-contract', $employee) }}" method="POST" class="overflow-y-auto p-6 space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Jenis Kontrak <span class="text-red-500">*</span></label>
                    <select name="jenis_kontrak" required
                            class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all appearance-none bg-[url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 20 20%27 fill=%27none%27 stroke=%27%236b7280%27 stroke-width=%272%27%3E%3Cpath d=%27M5 7l5 5 5-5%27/%3E%3C/svg%3E')] bg-no-repeat bg-[right_12px_center] pr-9">
                        <option value="">Pilih jenis kontrak</option>
                        <option value="Karyawan Kontrak">Karyawan Kontrak</option>
                        <option value="Karyawan Tetap">Karyawan Tetap</option>
                        <option value="Magang">Magang</option>
                        <option value="Freelance">Freelance</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" required
                               class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Berakhir <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_berakhir" required
                               class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" name="posisi" required placeholder="Contoh: IT Staff"
                           class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Atasan</label>
                    <input type="text" name="atasan" placeholder="Nama atasan langsung"
                           class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                </div>
                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" @click="tambahKontrakModal = false"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">
                        Simpan Kontrak
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Kontrak --}}
    <div x-show="editKontrakModal" x-cloak
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[200] flex items-center justify-center p-5 bg-gray-900/50 backdrop-blur-sm"
         @click="editKontrakModal = false">
        <div x-show="editKontrakModal" x-cloak
             x-transition:enter="transition-all ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             class="w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-700 shrink-0">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Perbarui Kontrak</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kontrak lama akan ditandai selesai & kontrak baru dibuat</p>
                </div>
                <button @click="editKontrakModal = false" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-500 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="`/hris/employees/{{ $employee->id }}/contracts/${formKontrakId}`" method="POST" class="overflow-y-auto p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700">Jenis Kontrak <span class="text-red-500">*</span></label>
                    <select name="jenis_kontrak" required
                            x-model="formKontrakJenis"
                            class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 outline-none hover:border-gray-300 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all appearance-none bg-[url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 20 20%27 fill=%27none%27 stroke=%27%236b7280%27 stroke-width=%272%27%3E%3Cpath d=%27M5 7l5 5 5-5%27/%3E%3C/svg%3E')] bg-no-repeat bg-[right_12px_center] pr-9">
                        <option value="">Pilih jenis kontrak</option>
                        <option value="Karyawan Kontrak">Karyawan Kontrak</option>
                        <option value="Karyawan Tetap">Karyawan Tetap</option>
                        <option value="Magang">Magang</option>
                        <option value="Freelance">Freelance</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-gray-700">Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" required x-model="formKontrakMulai"
                               class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 outline-none hover:border-gray-300 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-gray-700">Berakhir <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_berakhir" required x-model="formKontrakBerakhir"
                               class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 outline-none hover:border-gray-300 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700">Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" name="posisi" required placeholder="Contoh: IT Staff" x-model="formKontrakPosisi"
                           class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 outline-none hover:border-gray-300 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700">Atasan</label>
                    <input type="text" name="atasan" placeholder="Nama atasan langsung" x-model="formKontrakAtasan"
                           class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 outline-none hover:border-gray-300 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                </div>
                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100">
                    <button type="button" @click="editKontrakModal = false"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-100 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">
                        Simpan & Buat Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Hapus Kontrak --}}
    <div x-show="deleteKontrakId" x-cloak
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[200] flex items-center justify-center p-5 bg-gray-900/50 backdrop-blur-sm"
         @click="deleteKontrakId = null">
        <div x-show="deleteKontrakId" x-cloak
             x-transition:enter="transition-all ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             class="w-full max-w-sm bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-hidden">
            <div class="p-7 text-center">
                <div class="w-[52px] h-[52px] rounded-2xl bg-red-50 dark:bg-red-950 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-[26px] h-[26px] text-red-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                </div>
                <h4 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-1.5">Hapus Kontrak</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Apakah Anda yakin ingin menghapus kontrak ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="flex items-center justify-center gap-2.5 px-6 pb-7">
                <button @click="deleteKontrakId = null"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    Batal
                </button>
                <form :action="`/hris/employees/{{ $employee->id }}/contracts/${deleteKontrakId}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-all shadow-sm">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Jabatan --}}
    <div x-show="tambahJabatanModal" x-cloak
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[200] flex items-center justify-center p-5 bg-gray-900/50 backdrop-blur-sm"
         @click="tambahJabatanModal = false">
        <div x-show="tambahJabatanModal" x-cloak
             x-transition:enter="transition-all ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             class="w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-700 shrink-0">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Tambah Jabatan</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tambah riwayat jabatan baru untuk karyawan</p>
                </div>
                <button @click="tambahJabatanModal = false" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-500 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('hris.employees.store-position-history', $employee) }}" method="POST" class="overflow-y-auto p-6 space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" name="jabatan" placeholder="Contoh: IT Staff" required
                           class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Divisi <span class="text-red-500">*</span></label>
                    <select name="divisi" required
                            class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all appearance-none bg-[url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 20 20%27 fill=%27none%27 stroke=%27%236b7280%27 stroke-width=%272%27%3E%3Cpath d=%27M5 7l5 5 5-5%27/%3E%3C/svg%3E')] bg-no-repeat bg-[right_12px_center] pr-9">
                        <option value="">Pilih divisi</option>
                        <option value="IT">IT</option>
                        <option value="Creative">Creative</option>
                        <option value="HR">HR</option>
                        <option value="Finance">Finance</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Operational">Operational</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Atasan</label>
                    <input type="text" name="atasan" placeholder="Nama atasan langsung"
                           class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="mulai" required
                               class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Selesai</label>
                        <input type="date" name="selesai"
                               class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Status <span class="text-red-500">*</span></label>
                    <select name="status" required
                            class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all appearance-none bg-[url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 20 20%27 fill=%27none%27 stroke=%27%236b7280%27 stroke-width=%272%27%3E%3Cpath d=%27M5 7l5 5 5-5%27/%3E%3C/svg%3E')] bg-no-repeat bg-[right_12px_center] pr-9">
                        <option value="Aktif">Aktif</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" @click="tambahJabatanModal = false"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">
                        Simpan Jabatan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Promosi / Mutasi --}}
    <div x-show="promosiModal" x-cloak
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[200] flex items-center justify-center p-5 bg-gray-900/50 backdrop-blur-sm"
         @click="promosiModal = false">
        <div x-show="promosiModal" x-cloak
             x-transition:enter="transition-all ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             class="w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-700 shrink-0">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Promosi / Mutasi / Demosi</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Terapkan perubahan jabatan karyawan</p>
                </div>
                <button @click="promosiModal = false" class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-500 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('hris.employees.store-promotion', $employee) }}" method="POST" class="overflow-y-auto p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Jenis <span class="text-red-500">*</span></label>
                        <select name="jenis" required x-model="formPromosiJenis
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all appearance-none bg-[url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 20 20%27 fill=%27none%27 stroke=%27%236b7280%27 stroke-width=%272%27%3E%3Cpath d=%27M5 7l5 5 5-5%27/%3E%3C/svg%3E')] bg-no-repeat bg-[right_12px_center] pr-9">
                            <option value="promosi">Promosi</option>
                            <option value="mutasi">Mutasi</option>
                            <option value="demosi">Demosi</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Tanggal Efektif <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_efektif" required x-model="formPromosiTanggal"
                               class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Posisi Baru <span class="text-red-500">*</span></label>
                    <input type="text" name="posisi_baru" required x-model="formPromosiPosisi"
                           placeholder="Contoh: IT Manager"
                           class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">
                        Posisi saat ini: <b>{{ $employee->position ?? '—' }}</b>
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Divisi Baru</label>
                        <select name="divisi_baru"
                                class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all appearance-none bg-[url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 20 20%27 fill=%27none%27 stroke=%27%236b7280%27 stroke-width=%272%27%3E%3Cpath d=%27M5 7l5 5 5-5%27/%3E%3C/svg%3E')] bg-no-repeat bg-[right_12px_center] pr-9">
                            <option value="">Tetap ({{ $employee->division?->nama ?? '—' }})</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Atasan Baru</label>
                        <input type="text" name="atasan_baru" x-model="formPromosiAtasan"
                               placeholder="Nama atasan baru"
                               class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Nomor Surat</label>
                    <input type="text" name="nomor_surat" x-model="formPromosiNomor"
                           placeholder="Contoh: 001/SK-PROMOSI/JSA/VI/2026"
                           class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Alasan</label>
                    <textarea name="alasan" x-model="formPromosiAlasan" rows="3"
                              placeholder="Alasan promosi/mutasi/demosi..."
                              class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.25)] transition-all resize-none"></textarea>
                </div>

                <div class="rounded-xl bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                        <div class="text-xs text-amber-800 dark:text-amber-200 leading-relaxed">
                            <b class="font-bold">Yang akan terjadi:</b><br>
                            • Posisi karyawan akan diperbarui<br>
                            • Riwayat jabatan lama otomatis ditutup<br>
                            • Kontrak addendum baru dibuat (tgl berakhir ikut kontrak lama)<br>
                            • Surat keputusan promosi/mutasi/demosi akan digenerate dalam format PDF
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" @click="promosiModal = false"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-violet-600 hover:bg-violet-700 rounded-xl transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        Simpan Promosi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Hapus Promosi --}}
    <div x-show="hapusPromosiId" x-cloak
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[200] flex items-center justify-center p-5 bg-gray-900/50 backdrop-blur-sm"
         @click="hapusPromosiId = null">
        <div x-show="hapusPromosiId" x-cloak
             x-transition:enter="transition-all ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             class="w-full max-w-sm bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-hidden">
            <div class="p-7 pt-9 text-center">
                <div class="w-[52px] h-[52px] rounded-2xl bg-red-50 dark:bg-red-950 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-[26px] h-[26px] text-red-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                </div>
                <h4 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-1.5">Batalkan Promosi?</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    Data karyawan akan dikembalikan ke posisi semula. Riwayat jabatan dan kontrak addendum juga akan dikembalikan seperti sebelum promosi.
                </p>
            </div>
            <div class="flex items-center justify-center gap-2.5 px-6 pb-7">
                <button @click="hapusPromosiId = null"
                        class="flex-1 px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    Batal
                </button>
                <form method="POST" :action="hapusPromosiId ? '{{ route('hris.employees.destroy-promotion', [$employee, '__PROMOID__']) }}'.replace('__PROMOID__', hapusPromosiId) : '#'" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-5 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-all shadow-sm">
                        Ya, Batalkan
                    </button>
                </form>
            </div>
        </div>
    </div>




</div>

</x-app-layout>
