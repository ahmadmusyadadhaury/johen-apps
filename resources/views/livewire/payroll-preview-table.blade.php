<div>
    <div class="card overflow-hidden">
        <div class="p-4 border-b border-gray-50 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="relative flex-1 max-w-md">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input
                        type="text"
                        placeholder="Cari karyawan..."
                        wire:model.live.debounce.300ms="search"
                        class="input-field pl-10"
                    >
                </div>
                <span class="text-xs text-gray-400 dark:text-gray-500">Data karyawan</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
            <thead>
                <tr class="table-header">
                    <th class="px-3 py-3 cursor-pointer hover:text-gray-700 dark:text-gray-200 whitespace-nowrap select-none" wire:click="sortBy('nik')">
                        <div class="flex items-center gap-1">
                            NIK
                            @if($sortField === 'nik')
                                <svg class="w-3 h-3 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-3 py-3 cursor-pointer hover:text-gray-700 dark:text-gray-200 whitespace-nowrap select-none" wire:click="sortBy('nama')">
                        <div class="flex items-center gap-1">
                            Nama
                            @if($sortField === 'nama')
                                <svg class="w-3 h-3 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-3 py-3 whitespace-nowrap">Divisi</th>
                    <th class="px-3 py-3 whitespace-nowrap">Jabatan</th>
                    <th class="px-3 py-3 text-right whitespace-nowrap">Gaji Pokok</th>
                    <th class="px-3 py-3 text-right whitespace-nowrap">Tamb. Upah</th>
                    <th class="px-3 py-3 text-right whitespace-nowrap">Bonus</th>
                    <th class="px-3 py-3 text-right whitespace-nowrap">Total Diterima</th>
                    <th class="px-3 py-3 text-right whitespace-nowrap">Apresiasi</th>
                    <th class="px-3 py-3 text-right whitespace-nowrap">Tunj. Jabatan</th>
                    <th class="px-3 py-3 text-right whitespace-nowrap">Premi BPJS (4%)</th>
                    <th class="px-3 py-3 text-right whitespace-nowrap">THR Dibayar</th>
                    <th class="px-3 py-3 text-right whitespace-nowrap">Pot. Pinjaman</th>
                    <th class="px-3 py-3 text-right whitespace-nowrap">Pot. Absensi</th>
                    <th class="px-3 py-3 text-right whitespace-nowrap">Pot. BPJS (4%)</th>
                    <th class="px-3 py-3 text-right whitespace-nowrap">Pot. BPJS (1%)</th>
                    <th class="px-3 py-3 text-right whitespace-nowrap">THR</th>
                    <th class="px-3 py-3 whitespace-nowrap">Password</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                @forelse($details as $detail)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-800 transition-colors">
                        <td class="px-3 py-3 font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $detail->nik }}</td>
                        <td class="px-3 py-3 text-gray-900 dark:text-gray-100 whitespace-nowrap font-medium">{{ $detail->nama }}</td>
                        <td class="px-3 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->divisi }}</td>
                        <td class="px-3 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->jabatan }}</td>
                        <td class="px-3 py-3 text-right text-gray-900 dark:text-gray-100 whitespace-nowrap">Rp {{ number_format($detail->gaji_pokok, 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right whitespace-nowrap {{ $detail->tambahan_upah > 0 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-300 dark:text-gray-600' }}">{{ $detail->tambahan_upah > 0 ? 'Rp ' . number_format($detail->tambahan_upah, 0, ',', '.') : '-' }}</td>
                        <td class="px-3 py-3 text-right whitespace-nowrap {{ $detail->bonus > 0 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-300 dark:text-gray-600' }}">{{ $detail->bonus > 0 ? 'Rp ' . number_format($detail->bonus, 0, ',', '.') : '-' }}</td>
                        <td class="px-3 py-3 text-right font-semibold text-primary-700 whitespace-nowrap">Rp {{ number_format($detail->take_home_pay, 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right whitespace-nowrap {{ $detail->apresiasi > 0 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-300 dark:text-gray-600' }}">{{ $detail->apresiasi > 0 ? 'Rp ' . number_format($detail->apresiasi, 0, ',', '.') : '-' }}</td>
                        <td class="px-3 py-3 text-right whitespace-nowrap {{ $detail->tunjangan_jabatan > 0 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-300 dark:text-gray-600' }}">{{ $detail->tunjangan_jabatan > 0 ? 'Rp ' . number_format($detail->tunjangan_jabatan, 0, ',', '.') : '-' }}</td>
                        <td class="px-3 py-3 text-right whitespace-nowrap {{ $detail->premi_bpjs_kesehatan > 0 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-300 dark:text-gray-600' }}">{{ $detail->premi_bpjs_kesehatan > 0 ? 'Rp ' . number_format($detail->premi_bpjs_kesehatan, 0, ',', '.') : '-' }}</td>
                        <td class="px-3 py-3 text-right text-red-600 whitespace-nowrap">{{ $detail->thr_dibayarkan > 0 ? 'Rp ' . number_format($detail->thr_dibayarkan, 0, ',', '.') : '-' }}</td>
                        <td class="px-3 py-3 text-right text-red-600 whitespace-nowrap">{{ $detail->potongan_pinjaman > 0 ? 'Rp ' . number_format($detail->potongan_pinjaman, 0, ',', '.') : '-' }}</td>
                        <td class="px-3 py-3 text-right text-red-600 whitespace-nowrap">{{ $detail->potongan_absensi > 0 ? 'Rp ' . number_format($detail->potongan_absensi, 0, ',', '.') : '-' }}</td>
                        <td class="px-3 py-3 text-right text-red-600 whitespace-nowrap">{{ $detail->potongan_bpjs_kesehatan_4 > 0 ? 'Rp ' . number_format($detail->potongan_bpjs_kesehatan_4, 0, ',', '.') : '-' }}</td>
                        <td class="px-3 py-3 text-right text-red-600 whitespace-nowrap">{{ $detail->potongan_bpjs_kesehatan_1 > 0 ? 'Rp ' . number_format($detail->potongan_bpjs_kesehatan_1, 0, ',', '.') : '-' }}</td>
                        <td class="px-3 py-3 text-right whitespace-nowrap {{ $detail->thr > 0 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-300 dark:text-gray-600' }}">{{ $detail->thr > 0 ? 'Rp ' . number_format($detail->thr, 0, ',', '.') : '-' }}</td>
                        <td class="px-3 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs whitespace-nowrap">{{ $detail->pdf_password }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="18" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            Tidak ada data karyawan ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($details->hasPages())
            <div class="px-6 py-3 border-t border-gray-50 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                {{ $details->links() }}
            </div>
        @endif
    </div>
</div>





