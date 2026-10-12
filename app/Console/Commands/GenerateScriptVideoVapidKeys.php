<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;
use Throwable;

class GenerateScriptVideoVapidKeys extends Command
{
    protected $signature = 'script-video:generate-vapid {--write : Simpan kunci baru ke file .env lokal}';

    protected $description = 'Buat pasangan kunci VAPID untuk notifikasi push Script Video';

    public function handle(): int
    {
        try {
            $keys = VAPID::createVapidKeys();
        } catch (Throwable $exception) {
            // Some Windows PHP/OpenSSL builds cannot create EC keys through
            // JOSE, although Node (already required for the frontend build)
            // can create the same P-256 key pair.
            $nodeScript = "const c=require('crypto');const e=c.createECDH('prime256v1');e.generateKeys();console.log(JSON.stringify({publicKey:e.getPublicKey().toString('base64url'),privateKey:e.getPrivateKey().toString('base64url')}))";
            $output = [];
            $exitCode = 1;
            @exec('node -e ' . escapeshellarg($nodeScript), $output, $exitCode);
            $keys = $exitCode === 0 ? json_decode(implode("\n", $output), true) : null;

            if (! is_array($keys) || empty($keys['publicKey']) || empty($keys['privateKey'])) {
                throw $exception;
            }
        }

        if ($this->option('write')) {
            $path = base_path('.env');
            if (! is_file($path) || ! is_writable($path)) {
                $this->error('File .env tidak tersedia atau tidak bisa ditulis.');

                return self::FAILURE;
            }

            $contents = file_get_contents($path);
            foreach ([
                'SCRIPT_VIDEO_VAPID_PUBLIC_KEY' => $keys['publicKey'],
                'SCRIPT_VIDEO_VAPID_PRIVATE_KEY' => $keys['privateKey'],
                'SCRIPT_VIDEO_VAPID_SUBJECT' => (string) config('webpush.subject'),
            ] as $name => $value) {
                $line = $name . '=' . $value;
                $pattern = '/^' . preg_quote($name, '/') . '=.*$/m';
                if (preg_match($pattern, $contents)) {
                    $contents = preg_replace($pattern, $line, $contents, 1);
                } else {
                    $contents .= (str_ends_with($contents, "\n") ? '' : "\n") . $line . "\n";
                }
            }

            file_put_contents($path, $contents, LOCK_EX);
            $this->info('Kunci VAPID baru tersimpan di .env. Private key tidak ditampilkan.');

            return self::SUCCESS;
        }

        $this->line('Tambahkan nilai berikut ke .env (jaga private key tetap rahasia):');
        $this->line('SCRIPT_VIDEO_VAPID_PUBLIC_KEY=' . $keys['publicKey']);
        $this->line('SCRIPT_VIDEO_VAPID_PRIVATE_KEY=' . $keys['privateKey']);

        return self::SUCCESS;
    }
}
