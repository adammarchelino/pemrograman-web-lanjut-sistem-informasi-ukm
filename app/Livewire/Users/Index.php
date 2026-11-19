<?php

namespace App\Livewire\Users;

use App\Models\Ukm;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kelola User & Role — SIP-UKM')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterRole = '';

    #[Url]
    public string $filterUkm = '';

    // Modal Visibility
    public bool $showFormModal = false;

    public bool $showDetailModal = false;

    public bool $showDeleteModal = false;

    // Active User
    public ?int $editingUserId = null;

    public ?int $viewingUserId = null;

    public ?int $selectedUserId = null;

    // Form Data
    public array $form = [
        'name' => '',
        'email' => '',
        'nim' => '',
        'role' => User::ROLE_MAHASISWA,
        'ukm_id' => '',
        'jabatan_ukm' => '',
        'divisi_ukm_id' => '',
        'password' => '',
    ];

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRole(): void
    {
        $this->resetPage();
    }

    public function updatingFilterUkm(): void
    {
        $this->resetPage();
    }

    public function updatedFormRole(string $role): void
    {
        if ($role === User::ROLE_PENGURUS_UKM && blank($this->form['jabatan_ukm'])) {
            $this->form['jabatan_ukm'] = 'inti';
        }

        if ($role === User::ROLE_KEMAHASISWAAN) {
            $this->form['ukm_id'] = '';
            $this->form['jabatan_ukm'] = '';
            $this->form['divisi_ukm_id'] = '';
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterRole', 'filterUkm']);
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->editingUserId),
            ],
            'form.nim' => [
                Rule::requiredIf(fn () => ! $this->editingUserId && $this->form['role'] !== User::ROLE_KEMAHASISWAAN),
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'nim')->ignore($this->editingUserId),
            ],
            'form.role' => [
                'required',
                Rule::in([User::ROLE_KEMAHASISWAAN, User::ROLE_PENGURUS_UKM, User::ROLE_MAHASISWA]),
            ],
            'form.ukm_id' => [
                'nullable',
                Rule::requiredIf(fn () => $this->form['role'] === User::ROLE_PENGURUS_UKM || filled($this->form['jabatan_ukm'] ?? null)),
                'exists:ukm,id',
            ],
            'form.jabatan_ukm' => [
                Rule::requiredIf(fn () => $this->form['role'] === User::ROLE_PENGURUS_UKM || filled($this->form['ukm_id'] ?? null)),
                'nullable',
                Rule::in(['ketua', 'inti', 'divisi']),
            ],
            'form.divisi_ukm_id' => [
                Rule::requiredIf(fn () => $this->form['jabatan_ukm'] === 'divisi' && filled($this->form['ukm_id'] ?? null)),
                'nullable',
                Rule::exists('divisi_ukm', 'id')->where('ukm_id', $this->form['ukm_id'] ?: 0),
            ],
            'form.password' => [
                $this->editingUserId ? 'nullable' : 'required',
                'string',
                'min:8',
            ],
        ];
    }

    protected array $messages = [
        'form.name.required' => 'Nama pengguna wajib diisi.',
        'form.email.required' => 'Email wajib diisi.',
        'form.email.email' => 'Format email tidak valid.',
        'form.email.unique' => 'Email sudah digunakan pengguna lain.',
        'form.nim.unique' => 'NIM sudah terdaftar pada pengguna lain.',
        'form.role.required' => 'Role pengguna wajib dipilih.',
        'form.ukm_id.required' => 'UKM wajib dipilih untuk pengurus UKM.',
        'form.password.required' => 'Kata sandi awal wajib diisi.',
        'form.password.min' => 'Kata sandi minimal 8 karakter.',
    ];

    public function openCreateModal(): void
    {
        $this->authorize('create', User::class);

        $this->resetValidation();
        $this->editingUserId = null;
        $this->form = [
            'name' => '',
            'email' => '',
            'nim' => '',
            'role' => User::ROLE_MAHASISWA,
            'ukm_id' => '',
            'jabatan_ukm' => '',
            'divisi_ukm_id' => '',
            'password' => '',
        ];

        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);

        $this->resetValidation();
        $this->editingUserId = $user->id;
        $this->form = [
            'name' => $user->name,
            'email' => $user->email,
            'nim' => (string) ($user->nim ?? ''),
            'role' => $user->role,
            'ukm_id' => (string) ($user->ukm_id ?? ''),
            'jabatan_ukm' => $user->jabatan_ukm ?? '',
            'divisi_ukm_id' => (string) ($user->divisi_ukm_id ?? ''),
            'password' => '',
        ];

        $this->showFormModal = true;
    }

    public function openDetailModal(int $id): void
    {
        $user = User::findOrFail($id);
        $this->authorize('view', $user);

        $this->viewingUserId = $user->id;
        $this->showDetailModal = true;
    }

    public function save(): void
    {
        if ($this->form['role'] === User::ROLE_KEMAHASISWAAN) {
            $this->form['ukm_id'] = null;
            $this->form['jabatan_ukm'] = null;
            $this->form['divisi_ukm_id'] = null;
        } elseif ($this->form['jabatan_ukm'] !== 'divisi') {
            $this->form['divisi_ukm_id'] = null;
        }
        if (blank($this->form['ukm_id'] ?? null)) {
            $this->form['ukm_id'] = null;
        }
        if (blank($this->form['jabatan_ukm'] ?? null)) {
            $this->form['jabatan_ukm'] = null;
        }
        if (blank($this->form['divisi_ukm_id'] ?? null)) {
            $this->form['divisi_ukm_id'] = null;
        }

        $this->validate();

        $ukmId = ($this->form['role'] !== User::ROLE_KEMAHASISWAAN && filled($this->form['ukm_id'] ?? null))
            ? (int) $this->form['ukm_id']
            : null;
        $jabatan = $ukmId ? $this->form['jabatan_ukm'] : null;
        $divisionId = $jabatan === 'divisi' && filled($this->form['divisi_ukm_id'] ?? null)
            ? (int) $this->form['divisi_ukm_id']
            : null;

        $nim = trim($this->form['nim']) !== '' ? trim($this->form['nim']) : null;

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $this->authorize('update', $user);

            $payload = [
                'name' => $this->form['name'],
                'email' => $this->form['email'],
                'nim' => $nim,
                'role' => $this->form['role'],
                'ukm_id' => $ukmId,
                'jabatan_ukm' => $jabatan,
                'divisi_ukm_id' => $divisionId,
            ];

            if (! empty($this->form['password'])) {
                $payload['password'] = Hash::make($this->form['password']);
                $payload['wajib_ganti_password'] = $ukmId !== null;
            }

            DB::transaction(function () use ($payload, $ukmId, $jabatan, $user) {
                $this->ensureAvailableChair($ukmId, $jabatan, $user->id);
                $user->update($payload);
            });

            $this->dispatch('toast-show', [
                'heading' => 'Perubahan Tersimpan',
                'text' => "Data pengguna {$user->name} berhasil diperbarui.",
                'variant' => 'success',
            ]);
        } else {
            $this->authorize('create', User::class);

            $user = DB::transaction(function () use ($ukmId, $jabatan, $divisionId, $nim) {
                $this->ensureAvailableChair($ukmId, $jabatan);

                return User::create([
                    'name' => $this->form['name'],
                    'email' => strtolower(trim($this->form['email'])),
                    'nim' => $nim,
                    'role' => $this->form['role'],
                    'ukm_id' => $ukmId,
                    'jabatan_ukm' => $jabatan,
                    'divisi_ukm_id' => $divisionId,
                    'password' => Hash::make($this->form['password']),
                    'akun_aktif' => true,
                    'wajib_ganti_password' => $ukmId !== null,
                    'email_verified_at' => now(),
                ]);
            });

            $this->dispatch('toast-show', [
                'heading' => 'Pengguna Ditambahkan',
                'text' => "Akun pengguna {$user->name} berhasil dibuat.",
                'variant' => 'success',
            ]);
        }

        $this->showFormModal = false;
        $this->editingUserId = null;
    }

    private function ensureAvailableChair(?int $ukmId, ?string $jabatan, ?int $ignoreUserId = null): void
    {
        if (! $ukmId || $jabatan !== 'ketua') {
            return;
        }

        Ukm::query()->whereKey($ukmId)->lockForUpdate()->firstOrFail();
        $hasAnotherChair = User::query()
            ->where('ukm_id', $ukmId)
            ->where('jabatan_ukm', 'ketua')
            ->where('akun_aktif', true)
            ->when($ignoreUserId, fn ($query) => $query->where('id', '<>', $ignoreUserId))
            ->exists();

        if ($hasAnotherChair) {
            throw ValidationException::withMessages([
                'form.jabatan_ukm' => 'UKM ini sudah memiliki Ketua aktif. Nonaktifkan atau ubah penugasan Ketua sebelumnya terlebih dahulu.',
            ]);
        }
    }

    public function toggleAccountStatus(int $id): void
    {
        $this->authorize('viewAny', User::class);
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            $this->dispatch('toast-show', ['heading' => 'Aksi ditolak', 'text' => 'Kamu tidak dapat menonaktifkan akun sendiri.', 'variant' => 'danger']);

            return;
        }

        $user->update(['akun_aktif' => ! $user->isActive()]);
        $this->dispatch('toast-show', [
            'heading' => $user->isActive() ? 'Akun diaktifkan' : 'Akun dinonaktifkan',
            'text' => $user->isActive() ? 'Akun dapat masuk kembali.' : 'Akses masuk dihentikan dan semua riwayat tetap tersimpan.',
            'variant' => $user->isActive() ? 'success' : 'info',
        ]);
    }

    public function confirmDelete(int $id): void
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        if ($this->hasHistoricalRecords($user)) {
            $this->dispatchDeleteBlocked();

            return;
        }

        $this->selectedUserId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteUser(): void
    {
        if (! $this->selectedUserId) {
            return;
        }

        $user = User::findOrFail($this->selectedUserId);
        $this->authorize('delete', $user);

        if ($this->hasHistoricalRecords($user)) {
            $this->dispatchDeleteBlocked();
            $this->showDeleteModal = false;
            $this->selectedUserId = null;

            return;
        }

        if ($user->id === Auth::id()) {
            $this->dispatch('toast-show', [
                'heading' => 'Gagal Menghapus',
                'text' => 'Anda tidak dapat menghapus akun Anda sendiri.',
                'variant' => 'danger',
            ]);
            $this->showDeleteModal = false;

            return;
        }

        try {
            $name = $user->name;
            $user->delete();

            $this->dispatch('toast-show', [
                'heading' => 'Pengguna Dihapus',
                'text' => "Akun {$name} berhasil dihapus dari sistem.",
                'variant' => 'info',
            ]);
        } catch (QueryException $exception) {
            $this->dispatch('toast-show', [
                'heading' => 'Akun Tidak Dapat Dihapus',
                'text' => 'Akun masih terhubung dengan data lain. Riwayat terkait harus tetap dipertahankan.',
                'variant' => 'danger',
            ]);
        }

        $this->showDeleteModal = false;
        $this->selectedUserId = null;
    }

    private function hasHistoricalRecords(User $user): bool
    {
        return $user->pendaftaranAnggota()->exists()
            || $user->keputusanPendaftaranAnggota()->exists()
            || $user->keanggotaan()->exists()
            || $user->registrasiKegiatan()->exists()
            || $user->presensiDicatat()->exists();
    }

    private function dispatchDeleteBlocked(): void
    {
        $this->dispatch('toast-show', [
            'heading' => 'Akun Tidak Dapat Dihapus',
            'text' => 'Akun ini memiliki riwayat pengajuan, keanggotaan, registrasi kegiatan, atau presensi. Riwayat tersebut harus tetap tersimpan.',
            'variant' => 'danger',
        ]);
    }

    public function render(): View
    {
        $userCounts = User::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as mahasiswa', [User::ROLE_MAHASISWA])
            ->selectRaw('SUM(CASE WHEN role = ? OR jabatan_ukm IS NOT NULL THEN 1 ELSE 0 END) as pengurus_ukm', [User::ROLE_PENGURUS_UKM])
            ->selectRaw('SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as kemahasiswaan', [User::ROLE_KEMAHASISWAAN])
            ->first();

        $query = User::query()
            ->with(['ukm', 'divisiUkm'])
            ->when($this->search !== '', function ($q) {
                $term = '%'.trim($this->search).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('nim', 'like', $term);
                });
            })
            ->when($this->filterRole !== '', function ($q) {
                $q->where('role', $this->filterRole);
            })
            ->when($this->filterUkm !== '', function ($q) {
                $q->where('ukm_id', $this->filterUkm);
            })
            ->orderBy('name', 'asc');

        $users = $query->paginate(10);
        $ukmList = Ukm::orderBy('nama')->get(['id', 'nama']);
        $divisions = $this->form['ukm_id'] !== ''
            ? Ukm::find($this->form['ukm_id'])?->divisi()->orderBy('nama')->get() ?? collect()
            : collect();

        $selectedUser = $this->viewingUserId
            ? User::with(['ukm', 'divisiUkm'])->find($this->viewingUserId)
            : null;

        return view('livewire.users.index', [
            'users' => $users,
            'ukmList' => $ukmList,
            'divisions' => $divisions,
            'selectedUser' => $selectedUser,
            'userStats' => [
                'total' => (int) $userCounts->total,
                'mahasiswa' => (int) $userCounts->mahasiswa,
                'pengurus_ukm' => (int) $userCounts->pengurus_ukm,
                'kemahasiswaan' => (int) $userCounts->kemahasiswaan,
            ],
        ]);
    }
}
