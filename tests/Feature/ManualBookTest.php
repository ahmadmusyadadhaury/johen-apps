<?php

namespace Tests\Feature;

use App\Livewire\ManualBookTable;
use App\Models\ManualBook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManualBookTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::create([
            'name' => 'Super Admin',
            'username' => 'super',
            'email' => 'super@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
    }

    public function test_kategori_filter_filters_books(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        ManualBook::create(['nama' => 'Buku Teknologi', 'kategori' => 'Teknologi', 'file_pdf' => 'a.pdf']);
        ManualBook::create(['nama' => 'Buku Operasional', 'kategori' => 'Operasional', 'file_pdf' => 'b.pdf']);
        ManualBook::create(['nama' => 'Buku Public Speaking', 'kategori' => 'Public Speaking', 'file_pdf' => 'c.pdf']);

        $component = Livewire::test(ManualBookTable::class)
            ->set('filterKategori', 'Operasional');

        $component->assertViewHas('books', function ($books) {
            return $books->count() === 1 && $books->first()->kategori === 'Operasional';
        });
    }

    public function test_kategori_filter_and_form_dropdown_present(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $component = Livewire::test(ManualBookTable::class);

        $html = $component->html();
        $this->assertStringContainsString('wire:click="$set(\'filterKategori\', \'\')"', $html);
        $this->assertStringContainsString('Semua', $html);
        $this->assertStringContainsString('Teknologi', $html);
        $this->assertStringContainsString('Operasional', $html);
        $this->assertStringContainsString('Public Speaking', $html);
        $this->assertStringNotContainsString('Semua Kategori', $html);

        $component->call('openNew');

        $html = $component->html();
        $this->assertStringContainsString('-- Pilih Kategori --', $html);
    }

    public function test_save_requires_kategori(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $component = Livewire::test(ManualBookTable::class)
            ->set('nama', 'Buku Tanpa Kategori');

        $component->call('save')
            ->assertHasErrors(['kategori' => 'required']);

        $this->assertDatabaseMissing('manual_books', ['nama' => 'Buku Tanpa Kategori']);
    }

    public function test_save_stores_kategori_and_shows_success_modal(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $component = Livewire::test(ManualBookTable::class)
            ->set('nama', 'Buku Teknologi')
            ->set('kategori', 'Teknologi')
            ->set('file_pdf', \Illuminate\Http\UploadedFile::fake()->create('test.pdf', 10, 'application/pdf'))
            ->call('save');

        $this->assertDatabaseHas('manual_books', [
            'nama' => 'Buku Teknologi',
            'kategori' => 'Teknologi',
        ]);

        $component->assertSet('showSuccessModal', true)
            ->assertSet('successMessage', 'Manual book berhasil ditambahkan.');
    }

    public function test_delete_uses_confirm_and_success_modal(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $book = ManualBook::create(['nama' => 'Buku Dihapus', 'kategori' => 'Operasional', 'file_pdf' => 'd.pdf']);

        $component = Livewire::test(ManualBookTable::class);

        $component->call('confirmDelete', $book->id)
            ->assertSet('showDeleteConfirmModal', true)
            ->assertSet('deleteId', $book->id);

        $component->call('executeDelete')
            ->assertSet('showDeleteConfirmModal', false)
            ->assertSet('showSuccessModal', true)
            ->assertSet('successMessage', 'Manual book berhasil dihapus.');

        $this->assertDatabaseMissing('manual_books', ['id' => $book->id]);
    }

    public function test_success_modal_can_be_closed(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $component = Livewire::test(ManualBookTable::class);

        $component->set('showSuccessModal', true)
            ->set('successMessage', 'Pesan')
            ->call('closeSuccessModal')
            ->assertSet('showSuccessModal', false)
            ->assertSet('successMessage', '');
    }
}