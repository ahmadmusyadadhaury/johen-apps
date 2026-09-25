@php
    $meta = [
        'tepat waktu' => ['label' => 'Tepat Waktu', 'class' => 'badge-success'],
        'terlambat' => ['label' => 'Terlambat', 'class' => 'badge-warning'],
        'tidak hadir' => ['label' => 'Tidak Hadir', 'class' => 'badge-danger'],
        'izin' => ['label' => 'Izin', 'class' => 'badge-info'],
        'sakit' => ['label' => 'Sakit', 'class' => 'badge-warning'],
        'cuti' => ['label' => 'Cuti', 'class' => 'badge-info'],
        'libur' => ['label' => 'Libur', 'class' => 'badge-info'],
    ][$status] ?? ['label' => $status, 'class' => 'badge-secondary'];
@endphp

<span class="{{ $meta['class'] }}">{{ $meta['label'] }}</span>
