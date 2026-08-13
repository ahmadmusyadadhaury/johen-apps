<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Toleransi Keterlambatan
    |--------------------------------------------------------------------------
    | Jeda menit yang dimaklumi setelah jam mulai sesi sebelum dianggap terlambat.
    |
    */

    'grace_minutes' => 30,

    /*
    |--------------------------------------------------------------------------
    | Sesi Host Live
    |--------------------------------------------------------------------------
    | Sesi 3 (Malam 19:00-23:59) disimpan 23:59, ditampilkan "24:00".
    | Sesi 4 (Subuh 01:00-06:00) tercatat di tanggal HARI SEBELUMNYA
    | (ikut malam sebelumnya), sehingga punch pada jam 00:00-06:59
    | akan dianggap sebagai Sesi 4 dari hari kemarin.
    |
    */

    'sessions' => [
        1 => [
            'label' => 'Sesi 1',
            'nama' => 'Pagi',
            'mulai' => '07:00',
            'selesai' => '12:00',
            'selesai_display' => '12:00',
        ],
        2 => [
            'label' => 'Sesi 2',
            'nama' => 'Siang',
            'mulai' => '13:00',
            'selesai' => '18:00',
            'selesai_display' => '18:00',
        ],
        3 => [
            'label' => 'Sesi 3',
            'nama' => 'Malam',
            'mulai' => '19:00',
            'selesai' => '23:59',
            'selesai_display' => '24:00',
        ],
        4 => [
            'label' => 'Sesi 4',
            'nama' => 'Subuh',
            'mulai' => '01:00',
            'selesai' => '06:00',
            'selesai_display' => '06:00',
        ],
    ],

];
