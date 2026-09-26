@php
    $statusLabel = [
        'karyawan_aktif' => $k('Karyawan Aktif'),
        'mantan_karyawan' => $k('Mantan Karyawan'),
    ][$employee->tipe] ?? ucfirst((string) $employee->tipe);
    $mobilePosition = $employee->mainPosition()?->nama ?: ($employee->position ?: '—');
    $mobileInitial = strtoupper(substr($employee->nama ?: auth()->user()->name ?: '?', 0, 1));
    $mobileDivision = $employee->divisionNames() ?: '—';
    $canEditPhoto = !$isOwnReadOnly && (auth()->user()->can('update-data') || auth()->user()->employee_id === $employee->id);
    $mobileDocuments = $employee->documents->map(fn ($document) => [
        'id' => $document->id,
        'nama_dokumen' => $document->nama_dokumen,
        'jenis_dokumen' => $document->jenis_dokumen,
        'file' => $document->file,
        'keterangan' => $document->keterangan,
    ])->values();
    $mobileContracts = $employee->contracts->map(fn ($contract) => [
        'id' => $contract->id,
        'jenis_kontrak' => $contract->jenis_kontrak ?: 'Kontrak Kerja',
        'posisi' => $contract->posisi ?: '—',
        'atasan' => $contract->atasan ?: '—',
        'tanggal_mulai' => $contract->tanggal_mulai?->format('Y-m-d'),
        'tanggal_berakhir' => $contract->tanggal_berakhir?->format('Y-m-d'),
        'status' => $contract->status,
        'keterangan' => $contract->keterangan,
        'is_addendum' => $contract->is_addendum ?? false,
        'file' => $contract->file,
    ])->values();
    $mobilePromotions = $employee->promotions->map(fn ($promotion) => [
        'id' => $promotion->id,
        'nomor_surat' => $promotion->nomor_surat,
        'posisi_lama' => $promotion->posisi_lama,
        'posisi_baru' => $promotion->posisi_baru,
        'divisi_lama' => $promotion->divisi_lama,
        'divisi_baru' => $promotion->divisi_baru,
        'atasan_lama' => $promotion->atasan_lama,
        'atasan_baru' => $promotion->atasan_baru,
        'tanggal_efektif' => $promotion->tanggal_efektif?->format('Y-m-d'),
        'jenis' => $promotion->jenis,
        'alasan' => $promotion->alasan,
        'pdf_path' => $promotion->pdf_path,
    ])->values();
@endphp

<div
    x-data="{
        activeCategory: null,
        searchDocuments: '',
        documents: [],
        contracts: [],
        openContractId: null,
        jabatanList: [],
        promosiList: [],
        payrollList: [],
        payrollUnread: 0,
        markReadUrl: '',
        payrollMarked: false,
        init() {
            const data = document.getElementById('informasi-saya-mobile-data');
            if (!data) return;
            try { this.documents = JSON.parse(data.dataset.documents || '[]'); } catch (e) { this.documents = []; }
            try { this.contracts = JSON.parse(data.dataset.contracts || '[]'); } catch (e) { this.contracts = []; }
            try { this.jabatanList = JSON.parse(data.dataset.positionHistories || '[]'); } catch (e) { this.jabatanList = []; }
            try { this.promosiList = JSON.parse(data.dataset.promotions || '[]'); } catch (e) { this.promosiList = []; }
            try { this.payrollList = JSON.parse(data.dataset.payrollDetails || '[]'); } catch (e) { this.payrollList = []; }
            this.payrollUnread = parseInt(data.dataset.payrollUnread || '0', 10) || 0;
            this.markReadUrl = data.dataset.payrollMarkRead || '';
        },
        get categoryTitle() {
            return {
                personal: 'Informasi Pribadi',
                work: 'Data Pekerjaan',
                contact: 'Kontak dan Darurat',
                documents: 'Dokumen',
                contracts: 'Riwayat Kontrak',
                positions: 'Riwayat Jabatan',
                payroll: 'Riwayat Payroll',
            }[this.activeCategory] || 'Informasi Saya';
        },
        openCategory(category) {
            this.activeCategory = category;
            this.searchDocuments = '';
            if (category === 'payroll') this.markPayrollRead();
            this.$nextTick(() => {
                this.$refs.detailTitle?.focus();
                window.scrollTo({ top: 0, behavior: 'auto' });
            });
        },
        closeDetail() {
            this.activeCategory = null;
            this.openContractId = null;
            this.$nextTick(() => window.scrollTo({ top: 0, behavior: 'auto' }));
        },
        get filteredDocuments() {
            const query = this.searchDocuments.trim().toLowerCase();
            if (!query) return this.documents;
            return this.documents.filter((document) => [document.nama_dokumen, document.jenis_dokumen, document.keterangan]
                .some((value) => String(value || '').toLowerCase().includes(query)));
        },
        formatDate(value) {
            if (!value) return '-';
            const parts = String(value).split('-');
            if (parts.length !== 3) return value;
            const year = Number(parts[0]);
            const month = Number(parts[1]);
            const day = Number(parts[2]);
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            if (!year || !month || !day || month < 1 || month > 12) return value;
            return day + ' ' + months[month - 1] + ' ' + year;
        },
        formatMoney(value) {
            return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
        },
        contractIsDone(contract) {
            if (String(contract?.status || '').toLowerCase() === 'selesai') return true;
            if (contract?.tanggal_berakhir) {
                return new Date(contract.tanggal_berakhir + 'T23:59:59') < new Date();
            }
            return false;
        },
        contractIsOpen(id) {
            return this.openContractId === id;
        },
        toggleContract(id) {
            this.openContractId = this.openContractId === id ? null : id;
        },
        contractStatusLabel(contract) {
            if (this.contractIsDone(contract)) return 'Selesai';
            if (String(contract?.status || '').toLowerCase() === 'berlaku') return 'Berlaku';
            return contract?.status ? String(contract.status).charAt(0).toUpperCase() + String(contract.status).slice(1) : 'Aktif';
        },
        contractDaysLeft(contract) {
            if (!contract?.tanggal_berakhir || this.contractIsDone(contract)) return null;
            const end = new Date(contract.tanggal_berakhir + 'T23:59:59');
            const days = Math.ceil((end - new Date()) / (1000 * 60 * 60 * 24));
            return days >= 0 ? days : null;
        },
        contractDuration(contract) {
            if (!contract?.tanggal_mulai || !contract?.tanggal_berakhir) return '-';
            const start = new Date(contract.tanggal_mulai);
            const end = new Date(contract.tanggal_berakhir);
            const months = (end.getFullYear() - start.getFullYear()) * 12 + end.getMonth() - start.getMonth();
            if (months < 1) return 'Kurang dari 1 bulan';
            return months + ' bulan';
        },
        documentViewUrl(document) {
            return document?.id ? '{{ route('hris.employees.view-document', [$employee, '__DOC__']) }}'.replace('__DOC__', document.id) : '#';
        },
        documentDownloadUrl(document) {
            return document?.id ? '{{ route('hris.employees.download-document', [$employee, '__DOC__']) }}'.replace('__DOC__', document.id) : '#';
        },
        contractPreviewUrl(contract) {
            return contract?.id ? '{{ route('hris.employees.preview-contract', [$employee, '__CONTRACT__']) }}'.replace('__CONTRACT__', contract.id) : '#';
        },
        contractDownloadUrl(contract) {
            return contract?.id ? '{{ route('hris.employees.download-contract', [$employee, '__CONTRACT__']) }}'.replace('__CONTRACT__', contract.id) : '#';
        },
        promotionDownloadUrl(promotion) {
            return promotion?.id ? '{{ route('hris.employees.download-promotion-pdf', [$employee, '__PROMOTION__']) }}'.replace('__PROMOTION__', promotion.id) : '#';
        },
        allowanceTotal(payroll) {
            return Number(payroll.tambahan_upah || 0)
                + Number(payroll.tambahan_upah_sold || 0)
                + Number(payroll.bonus || 0)
                + Number(payroll.thr || 0)
                + Number(payroll.apresiasi || 0)
                + Number(payroll.tunjangan_jabatan || 0)
                + Number(payroll.premi_bpjs_kesehatan || 0);
        },
        deductionTotal(payroll) {
            return Number(payroll.thr_dibayarkan || 0)
                + Number(payroll.potongan_pinjaman || 0)
                + Number(payroll.potongan_absensi || 0)
                + Number(payroll.potongan_absensi_ketidakhadiran || 0)
                + Number(payroll.potongan_absensi_keterlambatan || 0)
                + Number(payroll.potongan_bpjs_kesehatan_4 || 0)
                + Number(payroll.potongan_bpjs_kesehatan_1 || 0);
        },
        markPayrollRead() {
            if (this.payrollMarked || !this.markReadUrl || this.payrollUnread <= 0) return;
            this.payrollMarked = true;
            fetch(this.markReadUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.marked > 0) this.payrollUnread = 0;
                })
                .catch(() => { this.payrollMarked = false; });
        },
    }"
    class="md:hidden"
>
    <div
        id="informasi-saya-mobile-data"
        data-documents="{{ json_encode($mobileDocuments) }}"
        data-contracts="{{ json_encode($mobileContracts) }}"
        data-position-histories="{{ json_encode($positionHistoryList) }}"
        data-promotions="{{ json_encode($mobilePromotions) }}"
        data-payroll-details="{{ json_encode($payrollDetails) }}"
        data-payroll-unread="{{ (int) $viewedUnreadPayroll }}"
        data-payroll-mark-read="{{ auth()->user()?->employee_id === $employee->id ? route('payroll.mark-read') : '' }}"
        class="hidden"
    ></div>

    <section x-show="activeCategory === null" x-cloak aria-labelledby="mobile-informasi-title" class="-mt-3 space-y-5 md:-mt-0">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-600 via-primary-700 to-violet-700 p-4 shadow-lg">
            <div class="pointer-events-none absolute right-0 top-0 h-64 w-64 opacity-10" aria-hidden="true">
                <svg class="h-full w-full" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="100" cy="100" r="100" fill="white"/></svg>
            </div>
            <div class="pointer-events-none absolute -bottom-8 -left-8 h-40 w-40 rounded-full bg-white/5 blur-2xl" aria-hidden="true"></div>
            <div class="relative flex items-center gap-3.5">
                @if($canEditPhoto)
                    <form method="POST" action="{{ route('hris.employees.upload-photo', $employee) }}" enctype="multipart/form-data" id="mobile-photo-form-{{ $employee->id }}" class="relative shrink-0">
                        @csrf
                        <label for="mobile-photo-input-{{ $employee->id }}" class="group relative block h-16 w-16 cursor-pointer overflow-hidden rounded-2xl bg-white/20 backdrop-blur-sm shadow-lg ring-2 ring-white/20 focus-within:ring-2 focus-within:ring-white focus-within:ring-offset-2 focus-within:ring-offset-transparent">
                            @if($employee->foto_url)
                                <img src="{{ $employee->foto_url }}" alt="{{ $employee->nama }}" class="h-full w-full object-cover">
                            @else
                                <span class="flex h-full w-full items-center justify-center bg-white/20 text-xl font-bold text-white" aria-hidden="true">{{ $mobileInitial }}</span>
                            @endif
                            <span class="absolute inset-0 flex items-center justify-center bg-black/45 text-white opacity-0 transition-opacity group-hover:opacity-100 group-focus-within:opacity-100" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h3.72a2 2 0 0 0 2-2 .996.996 0 0 1 1-.88h2.66M15 13a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path d="M21 16v4"/><path d="M19 18h4"/></svg>
                            </span>
                            <span class="sr-only">Ubah foto profil</span>
                        </label>
                        <input id="mobile-photo-input-{{ $employee->id }}" type="file" name="foto" accept="image/*" class="sr-only" onchange="this.form.submit()">
                    </form>
                @else
                    <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl bg-white/20 backdrop-blur-sm shadow-lg ring-2 ring-white/20" aria-label="Foto profil {{ $employee->nama }}">
                        @if($employee->foto_url)
                            <img src="{{ $employee->foto_url }}" alt="{{ $employee->nama }}" class="h-full w-full object-cover">
                        @else
                            <span class="flex h-full w-full items-center justify-center bg-white/20 text-xl font-bold text-white" aria-hidden="true">{{ $mobileInitial }}</span>
                        @endif
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <h2 id="mobile-informasi-title" class="truncate text-base font-bold text-white">{{ $employee->nama }}</h2>
                    <p class="mt-0.5 truncate text-sm text-white/80">{{ $mobilePosition }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-1.5">
                        @if(!empty($employee->nik))
                            <span class="inline-flex items-center gap-1 rounded-full bg-white/10 px-2.5 py-1 text-[11px] font-semibold text-white/90 backdrop-blur-sm">
                                <span class="text-white/60">NIP</span>
                                <span class="truncate">{{ $employee->nik }}</span>
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1 rounded-full bg-white/10 px-2.5 py-1 text-[11px] font-semibold text-white/90 backdrop-blur-sm">
                            <span class="text-white/60">Divisi</span>
                            <span class="truncate">{{ $mobileDivision }}</span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="relative mt-3 space-y-2 border-t border-white/20 pt-3 text-xs text-white/70">
                @if($employee->no_hp)
                <div class="flex items-center gap-2">
                    <span class="font-medium">Telepon</span>
                    <a href="tel:{{ $employee->no_hp }}" class="truncate font-semibold text-white hover:text-white/80">{{ $employee->no_hp }}</a>
                </div>
                @endif
                @if($employee->email)
                <div class="flex items-center gap-2">
                    <span class="font-medium">Email</span>
                    <a href="mailto:{{ $employee->email }}" class="truncate font-semibold text-white hover:text-white/80">{{ $employee->email }}</a>
                </div>
                @endif
                @unless($employee->no_hp || $employee->email)
                <p class="font-medium text-white/60">Kontak belum dilengkapi.</p>
                @endunless
            </div>
            @error('foto')
                <p class="relative mt-2 text-xs font-medium text-red-200" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <p class="mb-3 px-1 text-xs font-bold uppercase tracking-[0.16em] text-gray-400 dark:text-gray-500">Kategori informasi</p>
            <nav class="space-y-2.5" aria-label="Kategori informasi">
                <button type="button" @click="openCategory('personal')" class="group flex min-h-[72px] w-full items-center gap-3 rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm transition-all hover:border-primary-200 hover:bg-primary-50/40 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 active:scale-[0.99] dark:border-gray-800 dark:bg-gray-900 dark:hover:border-primary-800 dark:hover:bg-primary-950/20 dark:focus-visible:ring-offset-gray-950">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-300" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-gray-900 dark:text-gray-100">Informasi Pribadi</span>
                        <span class="mt-0.5 block truncate text-xs text-gray-500 dark:text-gray-400">Data diri, NIK, status, dll</span>
                    </span>
                    <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform group-hover:translate-x-0.5 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>

                <button type="button" @click="openCategory('work')" class="group flex min-h-[72px] w-full items-center gap-3 rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm transition-all hover:border-primary-200 hover:bg-primary-50/40 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 active:scale-[0.99] dark:border-gray-800 dark:bg-gray-900 dark:hover:border-primary-800 dark:hover:bg-primary-950/20 dark:focus-visible:ring-offset-gray-950">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-300" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M6 7V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v3"/></svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-gray-900 dark:text-gray-100">Data Pekerjaan</span>
                        <span class="mt-0.5 block truncate text-xs text-gray-500 dark:text-gray-400">Jabatan, divisi, atasan, dll</span>
                    </span>
                    <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform group-hover:translate-x-0.5 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>

                <button type="button" @click="openCategory('contact')" class="group flex min-h-[72px] w-full items-center gap-3 rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm transition-all hover:border-primary-200 hover:bg-primary-50/40 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 active:scale-[0.99] dark:border-gray-800 dark:bg-gray-900 dark:hover:border-primary-800 dark:hover:bg-primary-950/20 dark:focus-visible:ring-offset-gray-950">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-300" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-gray-900 dark:text-gray-100">Kontak dan Darurat</span>
                        <span class="mt-0.5 block truncate text-xs text-gray-500 dark:text-gray-400">Nomor telepon, email, kontak darurat</span>
                    </span>
                    <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform group-hover:translate-x-0.5 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>

                <button type="button" @click="openCategory('documents')" class="group flex min-h-[72px] w-full items-center gap-3 rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm transition-all hover:border-primary-200 hover:bg-primary-50/40 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 active:scale-[0.99] dark:border-gray-800 dark:bg-gray-900 dark:hover:border-primary-800 dark:hover:bg-primary-950/20 dark:focus-visible:ring-offset-gray-950">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-300" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-gray-900 dark:text-gray-100">Dokumen</span>
                        <span class="mt-0.5 block truncate text-xs text-gray-500 dark:text-gray-400">KTP, KK, NPWP, dan dokumen lainnya</span>
                    </span>
                    <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform group-hover:translate-x-0.5 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>

                <button type="button" @click="openCategory('contracts')" class="group flex min-h-[72px] w-full items-center gap-3 rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm transition-all hover:border-primary-200 hover:bg-primary-50/40 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 active:scale-[0.99] dark:border-gray-800 dark:bg-gray-900 dark:hover:border-primary-800 dark:hover:bg-primary-950/20 dark:focus-visible:ring-offset-gray-950">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-300" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-gray-900 dark:text-gray-100">Riwayat Kontrak</span>
                        <span class="mt-0.5 block truncate text-xs text-gray-500 dark:text-gray-400">History kontrak kerja</span>
                    </span>
                    <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform group-hover:translate-x-0.5 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>

                <button type="button" @click="openCategory('positions')" class="group flex min-h-[72px] w-full items-center gap-3 rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm transition-all hover:border-primary-200 hover:bg-primary-50/40 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 active:scale-[0.99] dark:border-gray-800 dark:bg-gray-900 dark:hover:border-primary-800 dark:hover:bg-primary-950/20 dark:focus-visible:ring-offset-gray-950">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-300" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="m3 17 6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-gray-900 dark:text-gray-100">Riwayat Jabatan</span>
                        <span class="mt-0.5 block truncate text-xs text-gray-500 dark:text-gray-400">Riwayat posisi &amp; pengalaman kerja</span>
                    </span>
                    <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform group-hover:translate-x-0.5 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>

                @if($canSeePayroll)
                    <button type="button" @click="openCategory('payroll')" class="group flex min-h-[72px] w-full items-center gap-3 rounded-2xl border border-gray-200 bg-white p-4 text-left shadow-sm transition-all hover:border-primary-200 hover:bg-primary-50/40 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 active:scale-[0.99] dark:border-gray-800 dark:bg-gray-900 dark:hover:border-primary-800 dark:hover:bg-primary-950/20 dark:focus-visible:ring-offset-gray-950">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-300" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h.01"/></svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="flex items-center gap-2 truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Riwayat Payroll
                                <span x-show="payrollUnread > 0" x-cloak class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold leading-none text-white" x-text="payrollUnread"></span>
                            </span>
                            <span class="mt-0.5 block truncate text-xs text-gray-500 dark:text-gray-400">Gaji, tunjangan, potongan</span>
                        </span>
                        <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform group-hover:translate-x-0.5 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                @endif
            </nav>
        </div>
    </section>

    <section x-show="activeCategory !== null" x-cloak aria-live="polite" class="-mt-7 space-y-4 md:-mt-0">
        <header class="flex items-center gap-3 border-b border-gray-200 pb-4 dark:border-gray-800">
            <button type="button" @click="closeDetail()" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 active:scale-95 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:focus-visible:ring-offset-gray-950" aria-label="Kembali ke Informasi Saya">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
            </button>
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-gray-400 dark:text-gray-500">Informasi Saya</p>
                <h2 x-ref="detailTitle" tabindex="-1" class="mt-0.5 truncate text-lg font-bold text-gray-900 outline-none dark:text-gray-100" x-text="categoryTitle"></h2>
            </div>
        </header>

        <section x-show="activeCategory === 'personal'" x-cloak class="space-y-4" aria-labelledby="mobile-informasi-title">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-2.5 border-b border-gray-100 bg-gray-50 px-4 py-3.5 dark:border-gray-800 dark:bg-gray-800/60">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-300" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100">Informasi Pribadi</h3>
                </div>
                <dl class="divide-y divide-gray-100 dark:divide-gray-800">
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Nama Lengkap</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->nama }}</dd></div>
                    @if(!empty($employee->nik))
                        <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">NIP</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->nik }}</dd></div>
                    @endif
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Tipe {{ $k('Karyawan') }}</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $statusLabel }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Status Pernikahan</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->status_pernikahan ? ucfirst($employee->status_pernikahan) : '-' }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Tempat Lahir</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->tempat_lahir ?? '-' }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal Lahir</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->tanggal_lahir?->isoFormat('D MMMM Y') ?? '-' }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Jenis Kelamin</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->jenis_kelamin == 'L' ? 'Laki-laki' : ($employee->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Ukuran Baju</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->ukuran_baju ?? '-' }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Agama</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->agama ? ucfirst($employee->agama) : '-' }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Pendidikan Terakhir</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->pendidikan_terakhir ? ucfirst($employee->pendidikan_terakhir) : '-' }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Asal Sekolah</dt><dd class="whitespace-pre-line text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->asal_sekolah ?: '-' }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Alamat Lengkap</dt><dd class="whitespace-pre-line text-sm font-semibold leading-relaxed text-gray-900 dark:text-gray-100">{{ $employee->fullAddress() ?: '-' }}</dd></div>
                </dl>
            </div>
        </section>

        <section x-show="activeCategory === 'work'" x-cloak class="space-y-4" aria-labelledby="mobile-informasi-title">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-2.5 border-b border-gray-100 bg-gray-50 px-4 py-3.5 dark:border-gray-800 dark:bg-gray-800/60">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-300" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M6 7V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v3"/></svg>
                    </span>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100">Data Pekerjaan</h3>
                </div>
                <dl class="divide-y divide-gray-100 dark:divide-gray-800">
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Jabatan</dt><dd class="space-y-1 text-sm font-semibold text-gray-900 dark:text-gray-100">@forelse($employee->positions as $position)<span class="flex flex-wrap items-center gap-1.5">{{ $position->nama }}@if($position->pivot?->is_main)<span class="rounded bg-violet-50 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-violet-700 dark:bg-violet-950/50 dark:text-violet-300">Utama</span>@endif</span>@empty<span>-</span>@endforelse</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Divisi</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">@forelse($employee->divisions as $division){{ $division->nama }}@if(!$loop->last)<br>@endif @empty - @endforelse</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Atasan 1</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->atasan && $employee->atasan !== 'Other' ? $employee->atasan : '-' }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Atasan 2</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->atasan2 && $employee->atasan2 !== 'Other' ? $employee->atasan2 : '-' }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal Bergabung</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->tanggal_masuk?->isoFormat('D MMMM Y') ?? '-' }}</dd></div>
                    @if($employee->tanggal_resign)
                        <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal Resign</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->tanggal_resign->isoFormat('D MMMM Y') }}</dd></div>
                    @endif
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Jenis {{ $k('Karyawan') }}</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->jenis_karyawan ?? '-' }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Lokasi Kerja</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->lokasi_kerja ?? '-' }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Jenis Kerja</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->jenis_kerja ?? '-' }}@if($employee->jenis_kerja)<span class="mt-0.5 block text-xs font-normal text-gray-500 dark:text-gray-400">{{ \App\Models\Employee::JENIS_KERJA_OPTIONS[$employee->jenis_kerja] ?? '' }}</span>@endif</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Jam Kerja</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->jam_kerja ?? '-' }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Jobdesk</dt><dd class="whitespace-pre-line text-justify text-sm font-semibold leading-relaxed text-gray-900 dark:text-gray-100">{{ $employee->jobdesk ?? '-' }}</dd></div>
                </dl>
            </div>
        </section>

        <section x-show="activeCategory === 'contact'" x-cloak class="space-y-4" aria-labelledby="mobile-informasi-title">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-2.5 border-b border-gray-100 bg-gray-50 px-4 py-3.5 dark:border-gray-800 dark:bg-gray-800/60">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-300" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </span>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100">Kontak dan Darurat</h3>
                </div>
                <dl class="divide-y divide-gray-100 dark:divide-gray-800">
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Nomor Telepon</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->no_hp ?? '-' }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Email</dt><dd class="break-all text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->email ?? '-' }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Kontak Darurat 1</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">@if($employee->no_kontak_darurat1 && $employee->hubungan_darurat1){{ $employee->no_kontak_darurat1 }} ({{ $employee->hubungan_darurat1 }})@elseif($employee->no_kontak_darurat1){{ $employee->no_kontak_darurat1 }}@else - @endif</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Kontak Darurat 2</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">@if($employee->no_kontak_darurat2 && $employee->hubungan_darurat2){{ $employee->no_kontak_darurat2 }} ({{ $employee->hubungan_darurat2 }})@elseif($employee->no_kontak_darurat2){{ $employee->no_kontak_darurat2 }}@else - @endif</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">BPJS Kesehatan</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->no_bpjs ?? '-' }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Status BPJS</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->status_bpjs === 'aktif' ? 'Aktif' : ($employee->status_bpjs === 'tidak aktif' ? 'Tidak Aktif' : '-') }}</dd></div>
                    <div class="space-y-1 px-4 py-3.5"><dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Informasi Lowongan</dt><dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $employee->informasi_lowongan ? ucfirst($employee->informasi_lowongan) : '-' }}</dd></div>
                </dl>
            </div>
        </section>

        <section x-show="activeCategory === 'documents'" x-cloak class="space-y-4" aria-labelledby="mobile-informasi-title">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <label for="mobile-document-search" class="sr-only">Cari dokumen</label>
                <div class="flex h-11 items-center gap-2.5 rounded-xl border border-gray-200 bg-gray-50 px-3.5 focus-within:border-primary-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-primary-100 dark:border-gray-700 dark:bg-gray-800/60 dark:focus-within:border-primary-500 dark:focus-within:bg-gray-800 dark:focus-within:ring-primary-950">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input id="mobile-document-search" type="search" x-model="searchDocuments" placeholder="Cari dokumen" class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-gray-900 outline-none placeholder:text-gray-400 focus:ring-0 dark:text-gray-100">
                </div>
            </div>
            <div class="space-y-3">
                <template x-for="document in filteredDocuments" :key="document.id">
                    <article class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="flex items-start gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-300" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="break-words text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="document.nama_dokumen"></h3>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" x-text="document.jenis_dokumen || 'Dokumen'"></p>
                                <p x-show="document.keterangan" x-cloak class="mt-2 text-xs leading-relaxed text-gray-500 dark:text-gray-400" x-text="document.keterangan"></p>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a x-show="document.file" x-cloak :href="documentViewUrl(document)" target="_blank" rel="noopener" class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-primary-600 px-3 py-2.5 text-xs font-semibold text-white transition-colors hover:bg-primary-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900" :aria-label="'Lihat dokumen ' + document.nama_dokumen">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                                Lihat
                            </a>
                            <a x-show="document.file" x-cloak :href="documentDownloadUrl(document)" class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl border border-gray-200 px-3 py-2.5 text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:focus-visible:ring-offset-gray-900" :aria-label="'Unduh dokumen ' + document.nama_dokumen">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/></svg>
                                Unduh
                            </a>
                        </div>
                    </article>
                </template>
                <div x-show="documents.length === 0" x-cloak class="rounded-2xl border border-gray-200 bg-white px-4 py-12 text-center dark:border-gray-800 dark:bg-gray-900">
                    <svg class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <h3 class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Belum Ada Dokumen</h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Belum ada dokumen yang tercatat.</p>
                </div>
                <div x-show="documents.length > 0 && filteredDocuments.length === 0" x-cloak class="rounded-2xl border border-gray-200 bg-white px-4 py-12 text-center dark:border-gray-800 dark:bg-gray-900">
                    <svg class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <h3 class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Dokumen tidak ditemukan</h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Coba gunakan kata kunci lain.</p>
                </div>
            </div>
        </section>

        <section x-show="activeCategory === 'contracts'" x-cloak class="space-y-4" aria-labelledby="mobile-informasi-title">
            @if($firstContractStart)
                <div class="flex items-center gap-3 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3.5 dark:border-gray-800 dark:bg-gray-800/50">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-300" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </span>
                    <div class="min-w-0"><p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Masa Kerja</p><p class="mt-0.5 text-sm font-bold text-gray-900 dark:text-gray-100">{{ $masaKerjaText }} <span class="text-xs font-medium text-gray-500 dark:text-gray-400">sejak {{ $firstContractStart->isoFormat('D MMM YYYY') }}</span></p></div>
                </div>
            @else
                <div class="flex items-center gap-3 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3.5 dark:border-gray-800 dark:bg-gray-800/50">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-400 dark:bg-gray-800" aria-hidden="true"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg></span>
                    <div><p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Masa Kerja</p><p class="mt-0.5 text-sm font-bold text-gray-500 dark:text-gray-400">Belum ada kontrak tercatat</p></div>
                </div>
            @endif
            <div class="space-y-3">
                <div class="relative space-y-3 pl-10" x-show="contracts.length > 0">
                    <span class="pointer-events-none absolute inset-y-1 left-[15px] w-0.5 rounded-full bg-gray-300 dark:bg-gray-700" aria-hidden="true"></span>
                    <template x-for="contract in contracts" :key="contract.id">
                        <article class="relative">
                            <span class="absolute -left-10 top-4 flex h-8 w-8 items-center justify-center rounded-full ring-4 ring-gray-50 dark:ring-gray-950" :class="contractIsDone(contract) ? 'bg-emerald-500 text-white' : 'bg-primary-600 text-white'" aria-hidden="true">
                                <svg x-show="contractIsDone(contract)" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                                <svg x-show="!contractIsDone(contract)" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7.5V12l3 1.8"/></svg>
                            </span>
                            <button type="button" @click="toggleContract(contract.id)" class="flex w-full items-center gap-2.5 rounded-2xl border border-gray-200 bg-white p-3.5 text-left shadow-sm transition-colors hover:border-primary-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 active:scale-[0.99] dark:border-gray-800 dark:bg-gray-900 dark:hover:border-primary-800 dark:focus-visible:ring-offset-gray-950" :aria-expanded="contractIsOpen(contract.id) ? 'true' : 'false'">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5">
                                        <h3 class="truncate text-sm font-bold text-gray-900 dark:text-gray-100" x-text="contract.jenis_kontrak"></h3>
                                        <span x-show="contract.is_addendum" x-cloak class="shrink-0 rounded-full border border-amber-200 bg-amber-50 px-1.5 py-px text-[10px] font-bold text-amber-700 dark:border-amber-800 dark:bg-amber-950/50 dark:text-amber-300">Addendum</span>
                                    </div>
                                    <p class="mt-1 flex items-center gap-1 text-[11px] font-medium text-gray-500 dark:text-gray-400">
                                        <span x-text="formatDate(contract.tanggal_mulai)"></span>
                                        <span class="text-gray-300 dark:text-gray-600" aria-hidden="true">&rarr;</span>
                                        <span x-text="formatDate(contract.tanggal_berakhir)"></span>
                                    </p>
                                </div>
                                <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold" :class="contractIsDone(contract) ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300' : 'bg-primary-50 text-primary-700 dark:bg-primary-950/50 dark:text-primary-300'" x-text="contractStatusLabel(contract)"></span>
                                <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform duration-200 dark:text-gray-500" :class="contractIsOpen(contract.id) && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div x-show="contractIsOpen(contract.id)" x-cloak class="mt-2 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                                <dl class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3"><div><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Mulai</dt><dd class="mt-1 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="formatDate(contract.tanggal_mulai)"></dd></div><div><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Berakhir</dt><dd class="mt-1 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="formatDate(contract.tanggal_berakhir)"></dd></div></div>
                                    <div><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Durasi</dt><dd class="mt-1 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="contractDuration(contract)"></dd></div>
                                    <div><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Posisi</dt><dd class="mt-1 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="contract.posisi"></dd></div>
                                    <div><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Atasan</dt><dd class="mt-1 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="contract.atasan"></dd></div>
                                    <div x-show="contract.keterangan" x-cloak><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Keterangan</dt><dd class="mt-1 text-xs leading-relaxed text-gray-700 dark:text-gray-300" x-text="contract.keterangan"></dd></div>
                                </dl>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <a x-show="contract.file" x-cloak :href="contractPreviewUrl(contract)" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-primary-50 px-3 py-2.5 text-xs font-semibold text-primary-700 transition-colors hover:bg-primary-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:bg-primary-950/50 dark:text-primary-300 dark:hover:bg-primary-900/50 dark:focus-visible:ring-offset-gray-900" :aria-label="'Lihat surat kontrak ' + contract.jenis_kontrak">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                                        Lihat Surat
                                    </a>
                                    <a x-show="contract.file" x-cloak :href="contractDownloadUrl(contract)" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-gray-200 px-3 py-2.5 text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:focus-visible:ring-offset-gray-900" :aria-label="'Unduh surat kontrak ' + contract.jenis_kontrak">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/></svg>
                                        Unduh PDF
                                    </a>
                                </div>
                            </div>
                        </article>
                    </template>
                </div>
                <div x-show="contracts.length === 0" x-cloak class="rounded-2xl border border-gray-200 bg-white px-4 py-12 text-center dark:border-gray-800 dark:bg-gray-900">
                    <svg class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/></svg>
                    <h3 class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Belum Ada Riwayat Kontrak</h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Belum ada riwayat kontrak yang tercatat.</p>
                </div>
            </div>
        </section>

        <section x-show="activeCategory === 'positions'" x-cloak class="space-y-4" aria-labelledby="mobile-informasi-title">
            <div class="space-y-3">
                <template x-for="position in jabatanList" :key="position.id">
                    <article class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="flex items-start justify-between gap-3"><div class="min-w-0"><h3 class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="position.jabatan"></h3><span x-show="position.is_main" x-cloak class="mt-1 inline-flex rounded-full bg-violet-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-violet-700 dark:bg-violet-950/50 dark:text-violet-300">Utama</span></div><span class="rounded-full bg-primary-50 px-2.5 py-1 text-[11px] font-bold text-primary-700 dark:bg-primary-950/50 dark:text-primary-300" x-text="position.status"></span></div>
                        <dl class="mt-4 space-y-3"><div><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Divisi</dt><dd class="mt-1 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="position.divisi || '—'"></dd></div><div><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Atasan</dt><dd class="mt-1 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="position.atasan || '—'"></dd></div><div class="grid grid-cols-2 gap-3"><div><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Mulai</dt><dd class="mt-1 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="formatDate(position.mulai)"></dd></div><div><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Selesai</dt><dd class="mt-1 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="formatDate(position.selesai)"></dd></div></div><div x-show="position.kontrak_berakhir" x-cloak><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Kontrak berakhir</dt><dd class="mt-1 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="formatDate(position.kontrak_berakhir)"></dd></div></dl>
                    </article>
                </template>
                <div x-show="jabatanList.length === 0" x-cloak class="rounded-2xl border border-gray-200 bg-white px-4 py-12 text-center dark:border-gray-800 dark:bg-gray-900">
                    <svg class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 17 6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg>
                    <h3 class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Belum Ada Riwayat Jabatan</h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Belum ada riwayat jabatan yang tercatat.</p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 pt-2"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-600 dark:bg-violet-950/50 dark:text-violet-300" aria-hidden="true"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 15V3"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg></span><h3 class="text-sm font-bold text-gray-800 dark:text-gray-100">Riwayat Promosi / Mutasi</h3></div>
            <div class="space-y-3">
                <template x-for="promotion in promosiList" :key="promotion.id">
                    <article class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div class="flex items-start justify-between gap-3"><div><p class="text-xs font-semibold text-gray-500 dark:text-gray-400" x-text="formatDate(promotion.tanggal_efektif)"></p><h4 class="mt-1 text-sm font-bold text-gray-900 dark:text-gray-100" x-text="promotion.jenis ? (promotion.jenis.charAt(0).toUpperCase() + promotion.jenis.slice(1)) : 'Perubahan Jabatan'"></h4></div><span class="rounded-full bg-violet-50 px-2.5 py-1 text-[11px] font-bold capitalize text-violet-700 dark:bg-violet-950/50 dark:text-violet-300" x-text="promotion.jenis || '—'"></span></div>
                        <dl class="mt-4 space-y-3"><div><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Nomor Surat</dt><dd class="mt-1 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="promotion.nomor_surat || '—'"></dd></div><div><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Jabatan</dt><dd class="mt-1 text-xs font-semibold text-gray-800 dark:text-gray-200"><span x-text="promotion.posisi_lama || '—'"></span><span class="mx-1 text-gray-400">→</span><span class="text-primary-600 dark:text-primary-300" x-text="promotion.posisi_baru || '—'"></span></dd></div><div><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Divisi</dt><dd class="mt-1 text-xs font-semibold text-gray-800 dark:text-gray-200"><span x-text="promotion.divisi_lama || '—'"></span><span class="mx-1 text-gray-400">→</span><span x-text="promotion.divisi_baru || '—'"></span></dd></div><div><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Atasan</dt><dd class="mt-1 text-xs font-semibold text-gray-800 dark:text-gray-200"><span x-text="promotion.atasan_lama || '—'"></span><span class="mx-1 text-gray-400">→</span><span x-text="promotion.atasan_baru || '—'"></span></dd></div><div x-show="promotion.alasan" x-cloak><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Alasan</dt><dd class="mt-1 text-xs leading-relaxed text-gray-700 dark:text-gray-300" x-text="promotion.alasan"></dd></div></dl>
                        <a x-show="promotion.pdf_path" x-cloak :href="promotionDownloadUrl(promotion)" class="mt-4 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-violet-50 px-3 py-2.5 text-xs font-semibold text-violet-700 transition-colors hover:bg-violet-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2 dark:bg-violet-950/50 dark:text-violet-300 dark:hover:bg-violet-900/50 dark:focus-visible:ring-offset-gray-900" :aria-label="'Unduh surat perubahan jabatan'">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/></svg>
                            Unduh Surat
                        </a>
                    </article>
                </template>
                <div x-show="promosiList.length === 0" x-cloak class="rounded-2xl border border-gray-200 bg-white px-4 py-10 text-center dark:border-gray-800 dark:bg-gray-900"><p class="text-xs text-gray-500 dark:text-gray-400">Belum ada riwayat promosi atau mutasi.</p></div>
            </div>
        </section>

        @if($canSeePayroll)
            <section x-show="activeCategory === 'payroll'" x-cloak class="space-y-4" aria-labelledby="mobile-informasi-title">
                <div class="space-y-3">
                    <template x-for="payroll in payrollList" :key="payroll.id">
                        <article class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <div class="flex items-start justify-between gap-3"><div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Periode</p><h3 class="mt-1 text-base font-bold text-gray-900 dark:text-gray-100" x-text="payroll.periode"></h3></div><span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold" :class="payroll.status === 'sent' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300' : (payroll.status === 'pending' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300' : 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300')"><span class="h-1.5 w-1.5 rounded-full" :class="payroll.status === 'sent' ? 'bg-emerald-500' : (payroll.status === 'pending' ? 'bg-amber-500' : 'bg-red-500')"></span><span x-text="payroll.status === 'sent' ? 'Terkirim' : (payroll.status === 'pending' ? 'Tertunda' : 'Gagal')"></span></span></div>
                            <dl class="mt-4 grid grid-cols-2 gap-3"><div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-800/60"><dt class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Gaji Pokok</dt><dd class="mt-1 text-xs font-bold text-gray-900 dark:text-gray-100" x-text="formatMoney(payroll.gaji_pokok)"></dd></div><div class="rounded-xl bg-emerald-50/70 p-3 dark:bg-emerald-950/20"><dt class="text-[10px] font-semibold uppercase tracking-wide text-emerald-600/80 dark:text-emerald-300/80">Total Tunjangan</dt><dd class="mt-1 text-xs font-bold text-emerald-700 dark:text-emerald-300" x-text="formatMoney(allowanceTotal(payroll))"></dd></div><div class="rounded-xl bg-red-50/70 p-3 dark:bg-red-950/20"><dt class="text-[10px] font-semibold uppercase tracking-wide text-red-600/80 dark:text-red-300/80">Total Potongan</dt><dd class="mt-1 text-xs font-bold text-red-700 dark:text-red-300" x-text="formatMoney(deductionTotal(payroll))"></dd></div><div class="rounded-xl bg-primary-50 p-3 dark:bg-primary-950/30"><dt class="text-[10px] font-semibold uppercase tracking-wide text-primary-600/80 dark:text-primary-300/80">Gaji Bersih</dt><dd class="mt-1 text-xs font-bold text-primary-700 dark:text-primary-300" x-text="formatMoney(payroll.take_home_pay)"></dd></div></dl>
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <a :href="'/payroll/detail/' + payroll.id + '/view'" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-primary-50 px-3 py-2.5 text-xs font-semibold text-primary-700 transition-colors hover:bg-primary-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:bg-primary-950/50 dark:text-primary-300 dark:hover:bg-primary-900/50 dark:focus-visible:ring-offset-gray-900" :aria-label="'Lihat slip gaji periode ' + payroll.periode">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Lihat PDF
                                </a>
                                <a :href="'/payroll/detail/' + payroll.id + '/download'" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-gray-200 px-3 py-2.5 text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:focus-visible:ring-offset-gray-900" :aria-label="'Unduh slip gaji periode ' + payroll.periode">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/></svg>
                                    Download PDF
                                </a>
                            </div>
                        </article>
                    </template>
                    <div x-show="payrollList.length === 0" x-cloak class="rounded-2xl border border-gray-200 bg-white px-4 py-12 text-center dark:border-gray-800 dark:bg-gray-900">
                        <svg class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                        <h3 class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Belum Ada Riwayat Payroll</h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Riwayat payroll akan tersedia setelah fitur aktif.</p>
                    </div>
                </div>
            </section>
        @endif
    </section>
</div>
