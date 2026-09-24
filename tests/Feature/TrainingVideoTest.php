<?php

namespace Tests\Feature;

use App\Livewire\ManualBookTable;
use App\Models\TrainingVideo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TrainingVideoTest extends TestCase
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

    public function test_variable_url_converted_to_embed(): void
    {
        $this->assertSame(
            'https://www.youtube.com/embed/abc123',
            TrainingVideo::toEmbedUrl('https://www.youtube.com/watch?v=abc123')
        );
        $this->assertSame(
            'https://www.youtube.com/embed/abc123',
            TrainingVideo::toEmbedUrl('https://youtu.be/abc123?si=xyz')
        );
        $this->assertSame(
            'https://www.youtube.com/embed/abc123',
            TrainingVideo::toEmbedUrl('https://www.youtube.com/shorts/abc123')
        );
        $this->assertSame(
            'https://vimeo.com/123',
            TrainingVideo::toEmbedUrl('https://vimeo.com/123')
        );
    }

    public function test_videos_rendered_from_database(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        TrainingVideo::create(['nama' => 'Video Login', 'kategori' => 'Operasional', 'url' => 'https://www.youtube.com/embed/aaa']);

        $component = Livewire::test(ManualBookTable::class);

        $component->assertViewHas('videos', function ($videos) {
            return $videos->count() === 1 && $videos->first()->nama === 'Video Login';
        });
    }

    public function test_save_video_requires_url(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $component = Livewire::test(ManualBookTable::class)
            ->set('videoNama', 'Video Tanpa Link');

        $component->call('saveVideo')
            ->assertHasErrors(['videoUrl' => 'required']);

        $this->assertDatabaseMissing('training_videos', ['nama' => 'Video Tanpa Link']);
    }

    public function test_save_video_stores_embed_url_and_shows_success_modal(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $component = Livewire::test(ManualBookTable::class)
            ->set('videoNama', 'Cara Login')
            ->set('videoKategori', 'Operasional')
            ->set('videoUrl', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ')
            ->call('saveVideo');

        $this->assertDatabaseHas('training_videos', [
            'nama' => 'Cara Login',
            'kategori' => 'Operasional',
            'url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
        ]);

        $component->assertSet('showSuccessModal', true)
            ->assertSet('successMessage', 'Video pelatihan berhasil ditambahkan.');
    }

    public function test_delete_video_uses_confirm_and_success_modal(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $video = TrainingVideo::create(['nama' => 'Video Dihapus', 'kategori' => 'Operasional', 'url' => 'https://www.youtube.com/embed/xyz']);

        $component = Livewire::test(ManualBookTable::class);

        $component->call('confirmVideoDelete', $video->id)
            ->assertSet('showVideoDeleteConfirmModal', true)
            ->assertSet('videoDeleteId', $video->id);

        $component->call('executeVideoDelete')
            ->assertSet('showVideoDeleteConfirmModal', false)
            ->assertSet('showSuccessModal', true)
            ->assertSet('successMessage', 'Video pelatihan berhasil dihapus.');

        $this->assertDatabaseMissing('training_videos', ['id' => $video->id]);
    }
}