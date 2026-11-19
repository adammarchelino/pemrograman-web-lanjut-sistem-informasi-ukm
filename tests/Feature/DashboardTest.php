<?php

use App\Models\Ukm;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('kemahasiswaan sees administrative summaries and management shortcuts', function () {
    $admin = User::factory()->create(['role' => 'kemahasiswaan']);
    Ukm::factory()->create(['status' => 'aktif', 'status_pendaftaran_anggota' => 'dibuka']);
    Ukm::factory()->create(['status' => 'nonaktif', 'status_pendaftaran_anggota' => 'ditutup']);

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('PANEL KEMAHASISWAAN')
        ->assertSee('Total UKM')
        ->assertSee('Pendaftaran Dibuka')
        ->assertSee('Komposisi pengguna')
        ->assertSee('Kelola Data UKM')
        ->assertSee('Administrasi')
        ->assertSee('Kelola User &amp; Role', false);
});

test('other roles do not see administrative summaries on dashboard', function () {
    $student = User::factory()->create(['role' => 'mahasiswa']);

    $this->actingAs($student)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Selamat datang')
        ->assertDontSee('PANEL KEMAHASISWAAN')
        ->assertDontSee('Komposisi pengguna')
        ->assertDontSee('Administrasi')
        ->assertDontSee('Kelola User &amp; Role', false);
});

test('pengurus gets the sidebar layout for their role', function () {
    $ukm = Ukm::factory()->create();
    $pengurus = User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukm->id, 'jabatan_ukm' => 'inti']);

    $this->actingAs($pengurus)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Ruang Pengurus')
        ->assertSee('UKM')
        ->assertSee('Kelola divisi')
        ->assertDontSee('User &amp; Role', false);
});
