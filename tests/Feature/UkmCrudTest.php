<?php

use App\Livewire\Ukm\Index as UkmIndex;
use App\Livewire\Ukm\Profile as UkmProfile;
use App\Models\Ukm;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('requires authentication to access ukm index', function () {
    $response = $this->get(route('ukm.index'));

    $response->assertRedirect(route('login'));
});

it('allows authenticated users to view ukm index page', function () {
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);
    $ukm = Ukm::factory()->create(['nama' => 'UKM Robotika Poliwangi']);

    $this->actingAs($kemahasiswaan)
        ->get(route('ukm.index'))
        ->assertOk()
        ->assertSee('Kelola Data UKM')
        ->assertSee('UKM Robotika Poliwangi');
});

it('shows ukm totals grouped by organization and registration status', function () {
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);
    Ukm::factory()->create(['status' => 'aktif', 'status_pendaftaran_anggota' => 'dibuka']);
    Ukm::factory()->create(['status' => 'nonaktif', 'status_pendaftaran_anggota' => 'ditutup']);

    Livewire::actingAs($kemahasiswaan)
        ->test(UkmIndex::class)
        ->assertSee('Ringkasan data UKM')
        ->assertSeeInOrder(['Total UKM', '2', 'UKM Aktif', '1', 'UKM Nonaktif', '1', 'Pendaftaran Dibuka', '1']);
});

it('filters ukm by search term and status', function () {
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);
    Ukm::factory()->create(['nama' => 'UKM Musik Nada', 'status' => 'aktif']);
    Ukm::factory()->create(['nama' => 'UKM Olahraga Bugar', 'status' => 'nonaktif']);

    Livewire::actingAs($kemahasiswaan)
        ->test(UkmIndex::class)
        ->assertSee('UKM Musik Nada')
        ->assertSee('UKM Olahraga Bugar')
        ->set('search', 'Musik')
        ->assertSee('UKM Musik Nada')
        ->assertDontSee('UKM Olahraga Bugar')
        ->set('search', '')
        ->set('filterStatus', 'nonaktif')
        ->assertSee('UKM Olahraga Bugar')
        ->assertDontSee('UKM Musik Nada');
});

it('allows kemahasiswaan to create a new ukm', function () {
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);

    Livewire::actingAs($kemahasiswaan)
        ->test(UkmIndex::class)
        ->call('openCreateModal')
        ->assertSet('showFormModal', true)
        ->set('form.nama', 'UKM Riset dan Teknologi')
        ->set('form.deskripsi', 'Pengembangan karya ilmiah dan teknologi.')
        ->set('form.visi', 'Menjadi UKM riset unggulan.')
        ->set('form.misi', '1. Mengadakan workshop riset.')
        ->set('form.email_kontak', 'ristek@poliwangi.ac.id')
        ->set('form.nomor_kontak', '081234567890')
        ->set('form.status', 'aktif')
        ->set('form.status_pendaftaran_anggota', 'dibuka')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showFormModal', false);

    expect(Ukm::where('nama', 'UKM Riset dan Teknologi')->exists())->toBeTrue();
    $created = Ukm::where('nama', 'UKM Riset dan Teknologi')->first();
    expect($created->status)->toBe('aktif')
        ->and($created->status_pendaftaran_anggota)->toBe('dibuka');
});

it('uploads and previews a ukm logo before saving it', function () {
    Storage::fake('public');
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);

    Livewire::actingAs($kemahasiswaan)
        ->test(UkmIndex::class)
        ->call('openCreateModal')
        ->set('form.nama', 'UKM Desain')
        ->set('form.deskripsi', 'Wadah kreativitas desain mahasiswa.')
        ->set('logoUpload', UploadedFile::fake()->image('logo.png', 100, 100))
        ->assertHasNoErrors()
        ->assertSee('Pratinjau logo UKM')
        ->call('save')
        ->assertHasNoErrors();

    $ukm = Ukm::query()->where('nama', 'UKM Desain')->firstOrFail();

    expect($ukm->logo)->toStartWith('ukm-logos/');
    Storage::disk('public')->assertExists($ukm->logo);
});

it('rejects a non-image file as a ukm logo', function () {
    Storage::fake('public');
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);

    Livewire::actingAs($kemahasiswaan)
        ->test(UkmIndex::class)
        ->call('openCreateModal')
        ->set('form.nama', 'UKM File Salah')
        ->set('form.deskripsi', 'Deskripsi UKM.')
        ->set('logoUpload', UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'))
        ->call('save')
        ->assertHasErrors(['logoUpload' => 'image']);
});

it('validates duplicate ukm name when creating', function () {
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);
    Ukm::factory()->create(['nama' => 'UKM Tari Tradisional']);

    Livewire::actingAs($kemahasiswaan)
        ->test(UkmIndex::class)
        ->call('openCreateModal')
        ->set('form.nama', 'UKM Tari Tradisional')
        ->set('form.status', 'aktif')
        ->set('form.status_pendaftaran_anggota', 'ditutup')
        ->call('save')
        ->assertHasErrors(['form.nama' => 'unique']);
});

it('requires a description when creating a ukm', function () {
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);

    Livewire::actingAs($kemahasiswaan)
        ->test(UkmIndex::class)
        ->call('openCreateModal')
        ->set('form.nama', 'UKM Tanpa Deskripsi')
        ->set('form.deskripsi', '')
        ->call('save')
        ->assertHasErrors(['form.deskripsi' => 'required']);
});

it('allows kemahasiswaan and the assigned chair to update the ukm profile', function () {
    $ukm = Ukm::factory()->create([
        'nama' => 'UKM Fotografi',
        'deskripsi' => 'Lama',
        'status' => 'aktif',
    ]);
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);

    Livewire::actingAs($kemahasiswaan)
        ->test(UkmIndex::class)
        ->call('openEditModal', $ukm->id)
        ->assertSet('editingId', $ukm->id)
        ->assertSet('form.nama', 'UKM Fotografi')
        ->set('form.deskripsi', 'Deskripsi fotografi diperbarui.')
        ->call('save')
        ->assertHasNoErrors();

    expect($ukm->fresh()->deskripsi)->toBe('Deskripsi fotografi diperbarui.');

    $ketua = User::factory()->create([
        'role' => 'pengurus_ukm',
        'jabatan_ukm' => 'ketua',
        'ukm_id' => $ukm->id,
    ]);
    Livewire::actingAs($ketua)
        ->test(UkmProfile::class)
        ->set('form.deskripsi', 'Diperbarui oleh ketua.')
        ->call('save')
        ->assertHasNoErrors();

    expect($ukm->fresh()->deskripsi)->toBe('Diperbarui oleh ketua.');
});

it('prevents pengurus ukm from creating new ukm or updating other ukm', function () {
    $ukmA = Ukm::factory()->create(['nama' => 'UKM A']);
    $ukmB = Ukm::factory()->create(['nama' => 'UKM B']);
    $pengurusA = User::factory()->create(['role' => 'pengurus_ukm', 'jabatan_ukm' => 'inti', 'ukm_id' => $ukmA->id]);

    expect(Gate::forUser($pengurusA)->allows('create', Ukm::class))->toBeFalse()
        ->and(Gate::forUser($pengurusA)->allows('update', $ukmA))->toBeFalse()
        ->and(Gate::forUser($pengurusA)->allows('update', $ukmB))->toBeFalse();
});

it('allows kemahasiswaan to deactivate and activate ukm without deleting data', function () {
    $ukm = Ukm::factory()->create(['status' => 'aktif']);
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);

    Livewire::actingAs($kemahasiswaan)
        ->test(UkmIndex::class)
        ->call('confirmDeactivate', $ukm->id)
        ->assertSet('showDeactivateModal', true)
        ->assertSet('selectedUkmId', $ukm->id)
        ->call('deactivate')
        ->assertSet('showDeactivateModal', false);

    expect($ukm->fresh()->status)->toBe('nonaktif')
        ->and(Ukm::where('id', $ukm->id)->exists())->toBeTrue();

    // Re-activate
    Livewire::actingAs($kemahasiswaan)
        ->test(UkmIndex::class)
        ->call('confirmActivate', $ukm->id)
        ->assertSet('showActivateModal', true)
        ->call('activate')
        ->assertSet('showActivateModal', false);

    expect($ukm->fresh()->status)->toBe('aktif');
});
