<?php

namespace App\Http\Controllers;

use App\Facades\FileVault;
use App\Models\ScriptVideoUpload;
use App\Models\StoredFile;
use App\Models\User;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\HeaderUtils;

class ScriptVideoFileController extends Controller
{
    public function show(ScriptVideoUpload $upload, string $disposition)
    {
        $user = auth()->user();
        $canViewAll = $user && (
            $user->isStaffCreative()
            || in_array($user->role, [
                User::ROLE_MANAGER,
                User::ROLE_GM_CEO,
                User::ROLE_SUPER_ADMIN,
                User::ROLE_ADMIN_MASTER,
            ], true)
        );
        $hostGame = $user?->scriptVideoGameCategory();
        $canViewOwnUpload = $user
            && (int) $upload->user_id === (int) $user->id
            && ($hostGame === null || $upload->kategori_game === $hostGame);

        abort_unless($user && $user->canAccessScriptVideo() && ($canViewAll || $canViewOwnUpload), 403);

        $contents = FileVault::get('public', $upload->file_path);
        abort_if($contents === null, 404, 'File script tidak ditemukan.');

        $storedFile = StoredFile::query()
            ->where('disk', 'public')
            ->where('path', $upload->file_path)
            ->first();
        $mimeType = $storedFile?->mime_type;

        if (! $mimeType || $mimeType === 'application/octet-stream') {
            $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contents) ?: 'application/octet-stream';
        }

        $filename = basename($upload->file_name ?: $upload->file_path);
        $headerDisposition = $disposition === 'download'
            ? ResponseHeaderBag::DISPOSITION_ATTACHMENT
            : ResponseHeaderBag::DISPOSITION_INLINE;

        return response($contents, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => HeaderUtils::makeDisposition(
                $headerDisposition,
                $filename,
                preg_replace('/[^A-Za-z0-9._-]/', '_', $filename) ?: 'script-file'
            ),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}

