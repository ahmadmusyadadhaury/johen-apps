<?php

namespace App\Console\Commands;

use GdImage;
use Illuminate\Console\Command;

/**
 * Generate seluruh aset PWA (ikon + splash screen) dari satu logo sumber.
 *
 * Sumber logo, urutan prioritas:
 *   1. resources/pwa/source/logo.png   (disarankan: HD, min 1024x1024)
 *   2. public/logo.png                 (fallback, akan di-upscale)
 *
 * Pakai GD langsung supaya tidak menambah dependency composer.
 */
class GeneratePwaAssets extends Command
{
    protected $signature = 'pwa:assets
                            {--source= : Path absolut/relatif ke logo sumber (default: resources/pwa/source/logo.png lalu public/logo.png)}';

    protected $description = 'Generate ikon & splash screen PWA dari logo sumber (butuh ekstensi GD)';

    /** Warna brand, disamakan dengan tailwind.config.js */
    private const BRAND_BLUE = [9, 135, 245];    // primary-500  #0987F5
    private const BRAND_VIOLET = [133, 78, 234];  // violet-500   #854DEA
    private const BASE_DARK = [7, 8, 15];         // #07080F, sama dengan guest.blade.php

    /**
     * [nama file, ukuran, gaya]
     * - plain      : background solid, logo sebesar $logoScale dari tinggi kanvas
     * - glow       : background + glow radial, untuk icon utama
     * - maskable   : background full-bleed, logo dikecilkan agar aman di circular safe zone Android
     * - transparent : tanpa background sama sekali, hanya logo. Untuk favicon.
     */
    private const ICONS = [
        ['icon-192.png', 192, 'glow', 0.70],
        ['icon-512.png', 512, 'glow', 0.70],
        ['icon-maskable-192.png', 192, 'maskable', 0.62],
        ['icon-maskable-512.png', 512, 'maskable', 0.62],
        ['apple-touch-icon.png', 180, 'glow', 0.74],
        ['favicon-32.png', 32, 'transparent', 0.88],
    ];

    /**
     * Ukuran apple-touch-startup-image.
     * Android tidak butuh file ini: Chrome membuat splash sendiri dari icon + background_color.
     */
    private const SPLASHES = [
        [640, 1136],   // iPhone SE (2nd/3rd), 8
        [750, 1334],   // iPhone 8, SE (2nd/3rd)
        [828, 1792],   // iPhone XR, 11
        [1125, 2436],  // iPhone X, XS, 11 Pro
        [1170, 2532],  // iPhone 12, 12 Pro, 13, 13 Pro, 14
        [1179, 2556],  // iPhone 14 Pro, 15, 15 Pro, 16
        [1242, 2688],  // iPhone XS Max, 11 Pro Max
        [1284, 2778],  // iPhone 12/13 Pro Max, 14 Plus
        [1290, 2796],  // iPhone 14 Plus, 15 Plus, 15 Pro Max, 16 Plus
    ];

    /** Rasio tinggi logo terhadap tinggi kanvas splash. */
    private const SPLASH_LOGO_SCALE = 0.26;

    public function handle(): int
    {
        if (! extension_loaded('gd')) {
            $this->components->error('Ekstensi PHP "gd" tidak aktif. Aktifkan extension=gd di php.ini.');

            return self::FAILURE;
        }

        $sourcePath = $this->resolveSource();
        if ($sourcePath === null) {
            $this->components->error('Logo sumber tidak ditemukan.');
            $this->line('  Letakkan file PNG di: <info>resources/pwa/source/logo.png</info>');
            $this->line('  Minimal 1024x1024, idealnya square, warna putih di atas background transparan.');

            return self::FAILURE;
        }

        $this->components->info('Sumber logo: '.$this->relative($sourcePath));

        $logo = $this->loadLogo($sourcePath);
        if ($logo === null) {
            return self::FAILURE;
        }

        $logoW = imagesx($logo);
        $logoH = imagesy($logo);

        $this->line("  Dimensi asli : {$logoW}x{$logoH}");

        $logo = $this->trimTransparent($logo);
        $logoW = imagesx($logo);
        $logoH = imagesy($logo);

        $this->line("  Setelah trim : {$logoW}x{$logoH} (rasio ".round($logoW / $logoH, 3).')');

        if ($logoW < 512 || $logoH < 512) {
            $this->components->warn(
                "Logo kecil ({$logoW}x{$logoH}). Ikon 512px & splash akan terlihat kurang tajam."
            );
            $this->line('  Drop logo HD (min 1024x1024) ke <info>resources/pwa/source/logo.png</info>, lalu jalankan ulang.');
        }

        $outDir = public_path('pwa');
        $splashDir = $outDir.DIRECTORY_SEPARATOR.'splash';

        foreach ([$outDir, $splashDir] as $dir) {
            if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
                $this->components->error("Gagal membuat direktori: {$dir}");

                return self::FAILURE;
            }
        }

        $bar = $this->output->createProgressBar(count(self::ICONS) + count(self::SPLASHES));
        $bar->start();

        foreach (self::ICONS as [$name, $size, $style, $logoScale]) {
            $canvas = $this->makeCanvas($size, $size);

            if ($style === 'glow') {
                $this->paintBase($canvas, $size, $size);
                $this->paintGlow($canvas, (int) ($size / 2), (int) ($size * 0.44), (int) ($size * 0.62), 0.42);
            } elseif ($style !== 'transparent') {
                $this->paintBase($canvas, $size, $size);
            }

            $targetH = (int) round($size * $logoScale);
            $this->drawLogo($canvas, $logo, (int) ($size / 2), (int) ($size / 2), $targetH);

            $this->writePng($canvas, $outDir.DIRECTORY_SEPARATOR.$name);
            imagedestroy($canvas);
            $bar->advance();
        }

        foreach (self::SPLASHES as [$w, $h]) {
            $canvas = $this->makeCanvas($w, $h);
            $this->paintBase($canvas, $w, $h);

            // Dua glow: biru di atas, violet di bawah. Menghasilkan kesan mesh gradient
            // yang sama dengan guest.blade.php.
            $this->paintGlow($canvas, (int) ($w * 0.5), (int) ($h * 0.34), (int) ($w * 1.15), 0.50);
            $this->paintGlow($canvas, (int) ($w * 0.42), (int) ($h * 0.78), (int) ($w * 1.05), 0.34, self::BRAND_VIOLET);

            $targetH = (int) round(min($h * self::SPLASH_LOGO_SCALE, $w * 0.52));
            $this->drawLogo($canvas, $logo, (int) ($w / 2), (int) ($h * 0.42), $targetH);

            $this->writePng($canvas, $splashDir.DIRECTORY_SEPARATOR."splash-{$w}x{$h}.png");
            imagedestroy($canvas);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->components->info('Aset PWA berhasil dibuat.');
        $this->line('  Ikon    : '.$this->relative($outDir));
        $this->line('  Splash  : '.$this->relative($splashDir));
        $this->newLine();
        $this->line('  Wordmark tidak di-render di gambar (butuh font TTF lokal).');
        $this->line('  Wordmark "JOHEN APPS" dirender sebagai teks HTML di overlay splash — tajam di semua DPI.');

        return self::SUCCESS;
    }

    private function resolveSource(): ?string
    {
        $candidates = [];

        if ($this->option('source')) {
            $candidates[] = $this->option('source');
        } else {
            $candidates[] = resource_path('pwa/source/logo.png');
            $candidates[] = public_path('logo.png');
        }

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    private function loadLogo(string $path): ?GdImage
    {
        $info = @getimagesize($path);
        if ($info === false) {
            $this->components->error("Bukan file gambar yang valid: {$path}");

            return null;
        }

        $image = match ($info['mime']) {
            'image/png' => @imagecreatefrompng($path),
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/webp' => @imagecreatefromwebp($path),
            default => null,
        };

        if ($image === false) {
            $this->components->error("GD tidak bisa membaca: {$info['mime']}");

            return null;
        }

        return $image;
    }

    /**
     * Pangkas piksel yang sepenuhnya transparan di sekeliling logo, supaya
     * logo mengisi kanvas squarely tanpa ada padding tak terlihat.
     */
    private function trimTransparent(GdImage $im): GdImage
    {
        $w = imagesx($im);
        $h = imagesy($im);

        $minX = $w;
        $minY = $h;
        $maxX = -1;
        $maxY = -1;

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $alpha = (imagecolorat($im, $x, $y) >> 24) & 0x7F;
                if ($alpha <= 8) {
                    if ($x < $minX) {
                        $minX = $x;
                    }
                    if ($x > $maxX) {
                        $maxX = $x;
                    }
                    if ($y < $minY) {
                        $minY = $y;
                    }
                    if ($y > $maxY) {
                        $maxY = $y;
                    }
                }
            }
        }

        if ($maxX < 0) {
            return $im;
        }

        $pad = 1;
        $minX = max(0, $minX - $pad);
        $minY = max(0, $minY - $pad);
        $maxX = min($w - 1, $maxX + $pad);
        $maxY = min($h - 1, $maxY + $pad);

        $cropW = $maxX - $minX + 1;
        $cropH = $maxY - $minY + 1;

        if ($minX === 0 && $minY === 0 && $cropW === $w && $cropH === $h) {
            return $im;
        }

        $out = $this->makeCanvas($cropW, $cropH);
        imagecopy($out, $im, 0, 0, $minX, $minY, $cropW, $cropH);
        imagedestroy($im);

        return $out;
    }

    private function makeCanvas(int $w, int $h): GdImage
    {
        $im = imagecreatetruecolor($w, $h);

        // Alpha 7-bit di GD: 0 = opaque, 127 = transparan._fill harus dilakukan
        // dengan blending dimatikan, kalau tidak fill-nya ter-composit dan hasilnya
        // opaque hitam. Icon yang memakai background solid menimpa seluruh kanvas
        // lewat paintBase(), jadi filler ini hanya berpengaruh pada style 'transparent'.
        imagealphablending($im, false);
        imagesavealpha($im, true);
        imagefilledrectangle($im, 0, 0, $w - 1, $h - 1, imagecolorallocatealpha($im, 0, 0, 0, 127));
        imagealphablending($im, true);

        return $im;
    }

    /**
     * Background gelap polos sebagai warna dasar splash & icon.
     * Penting: manifest background_color dan tema harus memakai nilai yang sama,
     * supaya transisi dari native splash ke overlay HTML tidak kedip.
     */
    private function paintBase(GdImage $im, int $w, int $h): void
    {
        [$r, $g, $b] = self::BASE_DARK;
        imagefilledrectangle($im, 0, 0, $w - 1, $h - 1, imagecolorallocate($im, $r, $g, $b));
    }

    private function paintGlow(
        GdImage $im,
        int $cx,
        int $cy,
        int $radius,
        float $strength,
        ?array $rgb = null
    ): void {
        $rgb ??= self::BRAND_BLUE;
        $res = 96;
        $layer = imagecreatetruecolor($res, $res);
        imagealphablending($layer, false);
        imagesavealpha($layer, true);

        for ($y = 0; $y < $res; $y++) {
            for ($x = 0; $x < $res; $x++) {
                $dx = ($x / ($res - 1)) * 2 - 1;
                $dy = ($y / ($res - 1)) * 2 - 1;
                $dist = sqrt($dx * $dx + $dy * $dy);

                if ($dist >= 1.0) {
                    imagesetpixel($layer, $x, $y, imagecolorallocatealpha($layer, 0, 0, 0, 127));

                    continue;
                }

                // Kurva falloff halus: kuat di tengah, approaching 0 di tepi.
                $falloff = (1.0 - $dist) ** 2.2;
                $alpha = (int) round(127 * (1.0 - min(1.0, $falloff * $strength * 2.0)));
                $alpha = max(0, min(127, $alpha));

                imagesetpixel(
                    $layer,
                    $x,
                    $y,
                    imagecolorallocatealpha($layer, $rgb[0], $rgb[1], $rgb[2], $alpha)
                );
            }
        }

        imagealphablending($layer, true);

        $destW = $radius * 2;
        $destH = $radius * 2;
        $destX = $cx - $radius;
        $destY = $cy - $radius;

        imagecopyresampled(
            $im,
            $layer,
            $destX,
            $destY,
            0,
            0,
            $destW,
            $destH,
            $res,
            $res
        );

        imagedestroy($layer);
    }

    /**
     * Resize logo sehingga tingginya $targetH, lalu tempel dengan titik tengah
     * vertikal di $centerY. Mempertahankan rasio aspek asli.
     */
    private function drawLogo(GdImage $canvas, GdImage $logo, int $centerX, int $centerY, int $targetH): void
    {
        if ($targetH < 1) {
            return;
        }

        $srcW = imagesx($logo);
        $srcH = imagesy($logo);
        $targetW = max(1, (int) round($targetH * ($srcW / $srcH)));

        $scaled = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($scaled, false);
        imagesavealpha($scaled, true);
        imagecopyresampled($scaled, $logo, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);
        imagealphablending($scaled, true);

        imagecopy(
            $canvas,
            $scaled,
            (int) round($centerX - $targetW / 2),
            (int) round($centerY - $targetH / 2),
            0,
            0,
            $targetW,
            $targetH
        );

        imagedestroy($scaled);
    }

    private function writePng(GdImage $im, string $path): void
    {
        imagepng($im, $path, 9);
    }

    private function relative(string $path): string
    {
        $base = base_path().DIRECTORY_SEPARATOR;

        return str_starts_with($path, $base) ? substr($path, strlen($base)) : $path;
    }
}
