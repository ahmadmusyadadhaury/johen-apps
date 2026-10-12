<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    |
    | Nama tabel MySQL yang menyimpan salinan isi setiap file yang diunggah.
    |
    */

    'table' => 'stored_files',

    /*
    |--------------------------------------------------------------------------
    | Auto Restore
    |--------------------------------------------------------------------------
    |
    | Bila true, perintah `files:restore` dijalankan otomatis tiap jam oleh
    | scheduler untuk memulihkan file yang hilang di disk dari MySQL.
    |
    */

    'auto_restore' => env('FILEVAULT_AUTO_RESTORE', true),

    /*
    |--------------------------------------------------------------------------
    | Disk
    |--------------------------------------------------------------------------
    |
    | Disk yang dipantau. Setiap penulisan lewat FileVault akan disalin ke
    | tabel `stored_files`. Disk "web" menunjuk ke folder public (khusus games).
    |
    */

    'disks' => ['public', 'local', 'web'],

    /*
    |--------------------------------------------------------------------------
    | Sync (backfill) targets
    |--------------------------------------------------------------------------
    |
    | Daftar direktori yang dipindai oleh `files:sync`. Nilai kosong berarti
    | seluruh isi disk dipindai.
    |
    */

    'sync' => [
        'public' => '',
        'local' => '',
        'web' => 'games',
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded directories
    |--------------------------------------------------------------------------
    |
    | Direktori yang tidak ikut disalin (mis. file sementara Livewire).
    |
    */

    'exclude' => ['livewire-tmp'],

    /*
    |--------------------------------------------------------------------------
    | Maximum mirror size (bytes)
    |--------------------------------------------------------------------------
    |
    | Batas ukuran file yang disalin ke MySQL untuk mencegah lonjakan memori.
    | Default 100 MB.
    |
    */

    'max_bytes' => (int) env('FILEVAULT_MAX_BYTES', 104857600),

];
