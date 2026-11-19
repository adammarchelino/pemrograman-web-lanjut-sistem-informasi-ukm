<?php

namespace App\Livewire\Ukm;

use App\Models\Ukm;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Tim dan Divisi UKM — SIP-UKM')]
class Team extends Component
{
    public string $divisionName = '';

    public ?int $editingDivisionId = null;

    public string $lookupStudent = '';

    public ?int $candidateId = null;

    public array $member = ['name' => '', 'nim' => '', 'email' => '', 'division_id' => '', 'password' => ''];

    public array $divisionAssignments = [];

    public function mount(): void
    {
        $this->authorize('manageTeam', User::class);
    }

    public function saveDivision(): void
    {
        $this->authorize('manageTeam', User::class);
        $ukm = $this->managedUkm();

        $uniqueName = Rule::unique('divisi_ukm', 'nama')->where('ukm_id', $ukm->id);
        if ($this->editingDivisionId) {
            $uniqueName->ignore($this->editingDivisionId);
        }

        $this->validate(['divisionName' => ['required', 'string', 'max:100', $uniqueName]]);

        if ($this->editingDivisionId) {
            $division = $ukm->divisi()->findOrFail($this->editingDivisionId);
            $division->update(['nama' => trim($this->divisionName)]);
            $message = 'Nama divisi diperbarui.';
        } else {
            $ukm->divisi()->create(['nama' => trim($this->divisionName)]);
            $message = 'Divisi berhasil ditambahkan.';
        }

        $this->reset('divisionName', 'editingDivisionId');
        $this->dispatch('toast-show', ['heading' => 'Struktur divisi tersimpan', 'text' => $message, 'variant' => 'success']);
    }

    public function editDivision(int $id): void
    {
        $this->authorize('manageTeam', User::class);
        $division = $this->managedUkm()->divisi()->findOrFail($id);
        $this->editingDivisionId = $division->id;
        $this->divisionName = $division->nama;
    }

    public function cancelDivisionEdit(): void
    {
        $this->authorize('manageTeam', User::class);
        $this->reset('divisionName', 'editingDivisionId');
    }

    public function deleteDivision(int $id): void
    {
        $this->authorize('manageTeam', User::class);
        $division = $this->managedUkm()->divisi()->withCount('pengurus')->findOrFail($id);

        if ($division->pengurus_count > 0) {
            $this->addError('divisionName', 'Divisi yang sudah pernah memiliki pengurus dipertahankan agar riwayat penempatan tetap utuh. Ubah namanya jika struktur berubah.');

            return;
        }

        $division->delete();
        $this->dispatch('toast-show', ['heading' => 'Divisi dihapus', 'text' => 'Divisi kosong berhasil dihapus.', 'variant' => 'info']);
    }

    public function findStudent(): void
    {
        $this->authorize('manageTeam', User::class);
        $this->validate(['lookupStudent' => ['required', 'string', 'max:255']]);

        $candidate = User::query()
            ->where('role', User::ROLE_MAHASISWA)
            ->where('akun_aktif', true)
            ->whereNull('ukm_id')
            ->where(function ($query) {
                $query->where('nim', trim($this->lookupStudent))
                    ->orWhere('email', trim($this->lookupStudent));
            })
            ->first();

        if (! $candidate) {
            $this->candidateId = null;
            $this->addError('lookupStudent', 'Akun mahasiswa tidak ditemukan atau sudah terikat ke UKM lain. Kamu dapat membuat akun baru jika mahasiswa belum terdaftar.');

            return;
        }

        $this->candidateId = $candidate->id;
    }

    public function assignExistingStudent(): void
    {
        $this->authorize('manageTeam', User::class);
        $ukm = $this->managedUkm();
        $this->validate([
            'candidateId' => ['required', 'integer', 'exists:users,id'],
            'member.division_id' => ['required', 'integer', Rule::exists('divisi_ukm', 'id')->where('ukm_id', $ukm->id)],
        ]);

        $student = User::query()
            ->whereKey($this->candidateId)
            ->where('role', User::ROLE_MAHASISWA)
            ->where('akun_aktif', true)
            ->whereNull('ukm_id')
            ->firstOrFail();

        $student->update([
            'ukm_id' => $ukm->id,
            'jabatan_ukm' => 'divisi',
            'divisi_ukm_id' => (int) $this->member['division_id'],
            'email_verified_at' => $student->email_verified_at ?? now(),
        ]);

        $this->reset('candidateId', 'lookupStudent');
        $this->member['division_id'] = '';
        $this->dispatch('toast-show', ['heading' => 'Pengurus ditambahkan', 'text' => "{$student->name} ditetapkan sebagai Pengurus Divisi.", 'variant' => 'success']);
    }

    public function createDivisionMember(): void
    {
        $this->authorize('manageTeam', User::class);
        $ukm = $this->managedUkm();
        $this->validate([
            'member.name' => ['required', 'string', 'max:255'],
            'member.nim' => ['required', 'string', 'max:20', Rule::unique('users', 'nim')],
            'member.email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'member.division_id' => ['required', 'integer', Rule::exists('divisi_ukm', 'id')->where('ukm_id', $ukm->id)],
            'member.password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => trim($this->member['name']),
            'nim' => trim($this->member['nim']),
            'email' => strtolower(trim($this->member['email'])),
            'password' => Hash::make($this->member['password']),
            'role' => User::ROLE_MAHASISWA,
            'ukm_id' => $ukm->id,
            'jabatan_ukm' => 'divisi',
            'divisi_ukm_id' => (int) $this->member['division_id'],
            'akun_aktif' => true,
            'wajib_ganti_password' => true,
            'email_verified_at' => now(),
        ]);

        $this->reset('member');
        $this->member = ['name' => '', 'nim' => '', 'email' => '', 'division_id' => '', 'password' => ''];
        $this->dispatch('toast-show', ['heading' => 'Akun Pengurus dibuat', 'text' => "Akun {$user->name} harus mengganti kata sandi sementara saat masuk pertama.", 'variant' => 'success']);
    }

    public function deactivateMember(int $id): void
    {
        $this->authorize('manageTeam', User::class);
        $member = $this->managedUkm()->pengurus()->where('jabatan_ukm', 'divisi')->findOrFail($id);

        if ($member->id === Auth::id()) {
            $this->addError('member', 'Kamu tidak dapat menonaktifkan akun sendiri.');

            return;
        }

        $member->update(['akun_aktif' => false]);
        $this->dispatch('toast-show', ['heading' => 'Akun dinonaktifkan', 'text' => 'Akun dan riwayatnya tetap tersimpan; akses masuk sudah dihentikan.', 'variant' => 'info']);
    }

    public function moveMemberToDivision(int $memberId): void
    {
        $this->authorize('manageTeam', User::class);
        $ukm = $this->managedUkm();
        $divisionId = $this->divisionAssignments[$memberId] ?? null;
        validator(['division_id' => $divisionId], [
            'division_id' => ['required', 'integer', Rule::exists('divisi_ukm', 'id')->where('ukm_id', $ukm->id)],
        ])->validate();

        $member = $ukm->pengurus()->where('jabatan_ukm', 'divisi')->where('akun_aktif', true)->findOrFail($memberId);
        $member->update(['divisi_ukm_id' => (int) $divisionId]);
        $this->dispatch('toast-show', ['heading' => 'Penempatan diperbarui', 'text' => "{$member->name} dipindahkan ke divisi baru.", 'variant' => 'success']);
    }

    private function managedUkm(): Ukm
    {
        $this->authorize('manageTeam', User::class);

        return Ukm::findOrFail(Auth::user()->ukm_id);
    }

    public function render(): View
    {
        $ukm = $this->managedUkm();
        $members = $ukm->pengurus()->with('divisiUkm')->where('jabatan_ukm', 'divisi')->orderBy('name')->get();

        foreach ($members as $member) {
            $this->divisionAssignments[$member->id] ??= (string) ($member->divisi_ukm_id ?? '');
        }

        return view('livewire.ukm.team', [
            'ukm' => $ukm,
            'divisions' => $ukm->divisi()->withCount('pengurus')->orderBy('nama')->get(),
            'members' => $members,
            'candidate' => $this->candidateId ? User::find($this->candidateId) : null,
        ]);
    }
}
