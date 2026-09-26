# Logo sumber untuk aset PWA

Letakkan file logo HD di folder ini dengan nama **`logo.png`**.

## Kriteria teknis

| Atribut | Kebutuhan |
|---|---|
| Format | PNG (JPG/WebP juga bisa, tapi PNG dengan alpha paling aman) |
| Resolusi | **minimal 1024x1024**, ideal 2048x2048 |
| Rasio | **square (1:1)** - lebih penting dari resolusi besar |
| Warna | Logo putih di atas background **transparan** (seperti `public/logo.png`) |
| Bentuk | Shield/perisai, sama seperti logo di sidebar |

## Cara pakai

```bash
php artisan pwa:assets
```

Kalau file `logo.png` di sini tidak ada, command otomatis memakai `public/logo.png`
sebagai fallback (dengan peringatan kalau resolusinya kecil).

## Kenapa harus HD?

Generator membuat ikon 512x512 dan splash sampai 1284x2778 dari satu file sumber.
Kalau sumbernya cuma 132x164 (ukuran `public/logo.png` sekarang), hasil upscaling
akan terlihat pecah di layar retina.

## Kenapa harus square?

Ikon Android (`purpose: maskable`) dipotong dalam lingkaran. Logo di dalam lingkaran
itu harus muat di *safe zone* 80% - kalau aslinya portrait seperti 132x164, logo akan
terpotong di atas dan bawah. Rasio square membuat generator bisa memuskilkan logo
dengan padding yang simetris di semua sisi.
