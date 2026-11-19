<?php

use App\Livewire\Users\Index as UserIndex;
use App\Models\PendaftaranAnggota;
use App\Models\Ukm;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

it('requires authentication to access user index', function () {
    $this->get(route('users.index'))
        ->assertRedirect(route('login'));
});

it('denies access to mahasiswa and pengurus ukm', function () {
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    $this->actingAs($mahasiswa)
        ->get(route('users.index'))
        ->assertForbidden();

    $ukm = Ukm::factory()->create();
    $pengurus = User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukm->id]);
    $this->actingAs($pengurus)
        ->get(route('users.index'))
        ->assertForbidden();
});

it('allows kemahasiswaan to view users list', function () {
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan', 'name' => 'Staf Kemahasiswaan']);
    $userA = User::factory()->create(['name' => 'Budi Santoso', 'role' => 'mahasiswa']);

    $this->actingAs($kemahasiswaan)
        ->get(route('users.index'))
        ->assertOk()
        ->assertSee('Kelola User &amp; Role', false)
        ->assertSee('Budi Santoso');
});

it('shows account totals grouped by role', function () {
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);
    $ukm = Ukm::factory()->create();
    User::factory()->count(2)->create(['role' => 'mahasiswa']);
    User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukm->id]);

    Livewire::actingAs($kemahasiswaan)
        ->test(UserIndex::class)
        ->assertSee('Ringkasan akun pengguna')
        ->assertSee('Total Pengguna')
        ->assertSee('Mahasiswa')
        ->assertSee('Pengurus UKM')
        ->assertSee('Kemahasiswaan')
        ->assertSeeInOrder(['Total Pengguna', '4', 'Mahasiswa', '2', 'Pengurus UKM', '1', 'Kemahasiswaan', '1']);
});

it('allows kemahasiswaan to view a user detail without exposing credentials', function () {
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);
    $ukm = Ukm::factory()->create(['nama' => 'UKM Robotika']);
    $user = User::factory()->create([
        'name' => 'Doni Pratama',
        'email' => 'doni@poliwangi.ac.id',
        'nim' => '362558302099',
        'role' => 'pengurus_ukm',
        'ukm_id' => $ukm->id,
    ]);

    Livewire::actingAs($kemahasiswaan)
        ->test(UserIndex::class)
        ->call('openDetailModal', $user->id)
        ->assertSet('showDetailModal', true)
        ->assertSee('Detail Pengguna')
        ->assertSee('Doni Pratama')
        ->assertSee('doni@poliwangi.ac.id')
        ->assertSee('362558302099')
        ->assertSee('Pengurus UKM')
        ->assertSee('UKM Robotika')
        ->assertDontSee($user->password);
});

it('filters users by search, role, and ukm', function () {
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);
    $ukm1 = Ukm::factory()->create(['nama' => 'UKM Tari']);
    $ukm2 = Ukm::factory()->create(['nama' => 'UKM Musik']);

    $user1 = User::factory()->create(['name' => 'Andi Wijaya', 'role' => 'pengurus_ukm', 'ukm_id' => $ukm1->id]);
    $user2 = User::factory()->create(['name' => 'Siti Nurhaliza', 'role' => 'pengurus_ukm', 'ukm_id' => $ukm2->id]);
    $user3 = User::factory()->create(['name' => 'Ahmad Fajar', 'role' => 'mahasiswa']);

    Livewire::actingAs($kemahasiswaan)
        ->test(UserIndex::class)
        ->assertSee('Andi Wijaya')
        ->assertSee('Siti Nurhaliza')
        ->assertSee('Ahmad Fajar')
        ->set('search', 'Nurhaliza')
        ->assertSee('Siti Nurhaliza')
        ->assertDontSee('Andi Wijaya')
        ->set('search', '')
        ->set('filterRole', 'mahasiswa')
        ->assertSee('Ahmad Fajar')
        ->assertDontSee('Andi Wijaya')
        ->set('filterRole', '')
        ->set('filterUkm', (string) $ukm1->id)
        ->assertSee('Andi Wijaya')
        ->assertDontSee('Siti Nurhaliza');
});

it('allows kemahasiswaan to create new user', function () {
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);
    $ukm = Ukm::factory()->create(['nama' => 'UKM Robotika']);

    Livewire::actingAs($kemahasiswaan)
        ->test(UserIndex::class)
        ->call('openCreateModal')
        ->assertSet('showFormModal', true)
        ->set('form.name', 'Doni Pratama')
        ->set('form.email', 'doni@poliwangi.ac.id')
        ->set('form.nim', '362558302099')
        ->set('form.role', 'pengurus_ukm')
        ->set('form.ukm_id', (string) $ukm->id)
        ->set('form.password', 'password123')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showFormModal', false);

    $created = User::where('email', 'doni@poliwangi.ac.id')->first();
    expect($created)->not->toBeNull()
        ->and($created->name)->toBe('Doni Pratama')
        ->and($created->nim)->toBe('362558302099')
        ->and($created->role)->toBe('pengurus_ukm')
        ->and($created->ukm_id)->toBe($ukm->id)
        ->and(Hash::check('password123', $created->password))->toBeTrue();
});

it('validates duplicate email and nim on user create', function () {
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);
    User::factory()->create([
        'email' => 'existing@poliwangi.ac.id',
        'nim' => '362558302001',
    ]);

    Livewire::actingAs($kemahasiswaan)
        ->test(UserIndex::class)
        ->call('openCreateModal')
        ->set('form.name', 'New User')
        ->set('form.email', 'existing@poliwangi.ac.id')
        ->set('form.nim', '362558302001')
        ->set('form.role', 'mahasiswa')
        ->set('form.password', 'password123')
        ->call('save')
        ->assertHasErrors(['form.email' => 'unique', 'form.nim' => 'unique']);
});

it('allows kemahasiswaan to update an existing user', function () {
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);
    $target = User::factory()->create([
        'name' => 'Nama Lama',
        'email' => 'lama@poliwangi.ac.id',
        'role' => 'mahasiswa',
    ]);

    Livewire::actingAs($kemahasiswaan)
        ->test(UserIndex::class)
        ->call('openEditModal', $target->id)
        ->assertSet('editingUserId', $target->id)
        ->assertSet('form.name', 'Nama Lama')
        ->set('form.name', 'Nama Baru Terverifikasi')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showFormModal', false);

    expect($target->fresh()->name)->toBe('Nama Baru Terverifikasi');
});

it('allows kemahasiswaan to delete another user but not self', function () {
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);
    $targetUser = User::factory()->create(['role' => 'mahasiswa']);

    // Deleting other user succeeds
    Livewire::actingAs($kemahasiswaan)
        ->test(UserIndex::class)
        ->call('confirmDelete', $targetUser->id)
        ->assertSet('showDeleteModal', true)
        ->assertSet('selectedUserId', $targetUser->id)
        ->call('deleteUser')
        ->assertSet('showDeleteModal', false);

    expect(User::where('id', $targetUser->id)->exists())->toBeFalse();

    // Deleting self is blocked by policy
    Livewire::actingAs($kemahasiswaan)
        ->test(UserIndex::class)
        ->call('confirmDelete', $kemahasiswaan->id)
        ->assertForbidden();

    expect(User::where('id', $kemahasiswaan->id)->exists())->toBeTrue();
});

it('prevents deleting a user with application history and explains why', function () {
    $kemahasiswaan = User::factory()->create(['role' => 'kemahasiswaan']);
    $student = User::factory()->create(['role' => 'mahasiswa']);
    PendaftaranAnggota::factory()->create(['user_id' => $student->id]);

    Livewire::actingAs($kemahasiswaan)
        ->test(UserIndex::class)
        ->call('confirmDelete', $student->id)
        ->assertSet('showDeleteModal', false)
        ->assertDispatched('toast-show');

    expect(User::whereKey($student->id)->exists())->toBeTrue();
});
