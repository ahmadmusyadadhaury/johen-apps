<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string store(\Illuminate\Http\UploadedFile $file, string $dir = '', ?string $disk = null)
 * @method static string storeAs(\Illuminate\Http\UploadedFile $file, string $dir, string $name, ?string $disk = null)
 * @method static string put(string $disk, string $path, string $content, ?string $originalName = null, ?string $mimeType = null)
 * @method static void mirror(string $disk, string $path, ?string $originalName = null, ?string $mimeType = null)
 * @method static void mirrorContent(string $disk, string $path, string $content, ?string $originalName = null, ?string $mimeType = null)
 * @method static string|null get(string $disk, string $path)
 * @method static bool exists(string $disk, string $path)
 * @method static void delete(string $disk, string $path)
 * @method static int restore(?string $disk = null, ?string $path = null)
 * @method static array syncDisk(string $disk, string $prefix = '')
 * @method static bool writeToDisk(string $disk, string $path, ?string $content)
 *
 * @see \App\Services\FileVault
 */
class FileVault extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\FileVault::class;
    }
}
