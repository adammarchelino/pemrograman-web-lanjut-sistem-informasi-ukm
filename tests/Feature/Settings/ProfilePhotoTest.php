<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('user can upload a profile photo', function () {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'mahasiswa', 'nim' => '362558302010']);
    $this->actingAs($user);

    Livewire::test('pages::settings.profile')
        ->set('fotoProfil', UploadedFile::fake()->image('foto.jpg', 200, 200))
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->foto_profil)->toStartWith('foto-profil/');
    Storage::disk('public')->assertExists($user->foto_profil);
    expect($user->fotoProfilUrl())->toContain('/storage/'.$user->foto_profil);
});

test('uploading a new photo replaces the previous file', function () {
    Storage::fake('public');
    Storage::disk('public')->put('foto-profil/lama.jpg', 'foto lama');

    $user = User::factory()->create([
        'role' => 'mahasiswa',
        'nim' => '362558302011',
        'foto_profil' => 'foto-profil/lama.jpg',
    ]);
    $this->actingAs($user);

    Livewire::test('pages::settings.profile')
        ->set('fotoProfil', UploadedFile::fake()->image('baru.jpg', 200, 200))
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->foto_profil)->not->toBe('foto-profil/lama.jpg');
    Storage::disk('public')->assertMissing('foto-profil/lama.jpg');
    Storage::disk('public')->assertExists($user->foto_profil);
});

test('user can remove their profile photo', function () {
    Storage::fake('public');
    Storage::disk('public')->put('foto-profil/lama.jpg', 'foto lama');

    $user = User::factory()->create([
        'role' => 'mahasiswa',
        'nim' => '362558302012',
        'foto_profil' => 'foto-profil/lama.jpg',
    ]);
    $this->actingAs($user);

    Livewire::test('pages::settings.profile')
        ->call('hapusFoto')
        ->assertHasNoErrors();

    expect($user->refresh()->foto_profil)->toBeNull();
    Storage::disk('public')->assertMissing('foto-profil/lama.jpg');
});

test('profile photo must be an image', function () {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'mahasiswa', 'nim' => '362558302013']);
    $this->actingAs($user);

    Livewire::test('pages::settings.profile')
        ->set('fotoProfil', UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'))
        ->assertHasErrors(['fotoProfil' => 'image']);
});

test('kemahasiswaan tanpa NIM tetap dapat menyimpan profil', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'role' => 'kemahasiswaan',
        'nim' => null,
        'name' => 'Admin Lama',
    ]);
    $this->actingAs($user);

    Livewire::test('pages::settings.profile')
        ->set('name', 'Admin Baru')
        ->set('nim', '')
        ->set('email', $user->email)
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    expect($user->refresh()->name)->toBe('Admin Baru')
        ->and($user->nim)->toBeNull();
});
