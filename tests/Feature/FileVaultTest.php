<?php

namespace Tests\Feature;

use App\Models\StoredFile;
use App\Services\FileVault;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileVaultTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Storage::fake('local');
        Storage::fake('web');
        $this->artisan('migrate');
    }

    public function test_mirror_writes_file_to_db_on_store(): void
    {
        $file = UploadedFile::fake()->create('doc.pdf', 200, 'application/pdf');

        $vault = app(FileVault::class);
        $path = $vault->store($file, 'documents', 'public');

        $this->assertTrue(Storage::disk('public')->exists($path));
        $this->assertTrue(StoredFile::where('disk', 'public')->where('path', $path)->exists());

        $row = StoredFile::where('disk', 'public')->where('path', $path)->first();
        $this->assertNotNull($row);
        $this->assertEquals($file->getSize(), $row->size);
        $this->assertEquals($file->getSize(), strlen($row->content));
        $this->assertStringContainsString('pdf', $row->mime_type);
    }

    public function test_get_falls_back_to_db_when_disk_file_missing(): void
    {
        $file = UploadedFile::fake()->create('note.txt', 100, 'text/plain');

        $vault = app(FileVault::class);
        $path = $vault->store($file, 'documents', 'public');

        Storage::disk('public')->delete($path);

        $content = $vault->get('public', $path);
        $this->assertNotNull($content);
        $this->assertEquals($file->getSize(), strlen($content));

        Storage::disk('public')->assertExists($path);
    }

    public function test_restore_rebuilds_missing_disk_files(): void
    {
        $vault = app(FileVault::class);
        $path = $vault->put('public', 'missing/file.pdf', '%PDF-1.4 fake content for test', 'file.pdf', 'application/pdf');

        Storage::disk('public')->delete($path);

        $restored = $vault->restore('public', $path);
        $this->assertGreaterThan(0, $restored);
        Storage::disk('public')->assertExists($path);
    }
}
