<?php

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules, WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $nim = '';

    public ?TemporaryUploadedFile $fotoProfil = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->nim = (string) (Auth::user()->nim ?? '');
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $rules = $this->profileRules($user->id);

        // NIM hanya wajib bagi mahasiswa; pengurus UKM dan Bagian Kemahasiswaan
        // tidak selalu memiliki NIM sehingga formulir tetap bisa disimpan.
        if ($user->role !== User::ROLE_MAHASISWA) {
            $rules['nim'] = ['nullable', 'string', 'max:20', Rule::unique(User::class, 'nim')->ignore($user->id)];
        }

        $validated = $this->validate([...$rules, ...$this->fotoProfilRules()]);

        unset($validated['fotoProfil']);

        $validated['nim'] = filled($validated['nim'] ?? null) ? trim((string) $validated['nim']) : null;

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($this->fotoProfil) {
            $path = $this->fotoProfil->storePublicly('foto-profil', 'public');

            if (! $path) {
                $this->addError('fotoProfil', 'Foto profil gagal disimpan. Silakan coba kembali.');

                return;
            }

            $previous = $user->foto_profil;
            $user->foto_profil = $path;

            if ($previous && $previous !== $path) {
                Storage::disk('public')->delete($previous);
            }
        }

        $user->save();

        $this->fotoProfil = null;

        Flux::toast(variant: 'success', text: __('Profil diperbarui.'));
    }

    /**
     * Hapus foto profil dan kembali memakai inisial nama.
     */
    public function hapusFoto(): void
    {
        $user = Auth::user();
        $previous = $user->foto_profil;

        $user->foto_profil = null;
        $user->save();

        if ($previous) {
            Storage::disk('public')->delete($previous);
        }

        $this->fotoProfil = null;

        Flux::toast(variant: 'success', text: __('Foto profil dihapus.'));
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function fotoProfilRules(): array
    {
        return [
            'fotoProfil' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Validasi berkas begitu dipilih agar pratinjau tidak menampilkan berkas yang tidak sah.
     */
    public function updatedFotoProfil(): void
    {
        $this->validate($this->fotoProfilRules());
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profil')" :subheading="__('Kelola foto, identitas, dan alamat email akunmu.')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="space-y-2">
                <label for="fotoProfil" class="text-sm font-medium text-slate-800 dark:text-zinc-200">Foto profil</label>

                <div class="flex flex-col gap-4 rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4 sm:flex-row sm:items-center dark:border-white/15 dark:bg-white/5">
                    <x-user-avatar
                        :user="auth()->user()"
                        :src="$fotoProfil && $fotoProfil->isPreviewable() ? $fotoProfil->temporaryUrl() : null"
                        size="size-20"
                        font="text-xl"
                    />

                    <div class="min-w-0 flex-1">
                        <input
                            id="fotoProfil"
                            type="file"
                            wire:model="fotoProfil"
                            accept="image/png,image/jpeg,image/webp"
                            class="block w-full cursor-pointer text-sm text-slate-600 file:me-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:font-medium file:text-blue-700 hover:file:bg-blue-100 dark:text-zinc-300 dark:file:bg-blue-950 dark:file:text-blue-300 dark:hover:file:bg-blue-900"
                        >

                        <p class="mt-2 text-xs leading-5 text-slate-500 dark:text-zinc-400">
                            PNG, JPG, atau WEBP maksimal 2 MB. Foto baru tersimpan setelah menekan Simpan perubahan. Tanpa foto, avatar memakai inisial namamu.
                        </p>

                        <div wire:loading wire:target="fotoProfil" class="mt-2 text-xs font-medium text-blue-700 dark:text-blue-300">
                            Memuat pratinjau...
                        </div>

                        @error('fotoProfil')
                            <p class="mt-2 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror

                        @if (auth()->user()->foto_profil && ! $fotoProfil)
                            <flux:button type="button" variant="subtle" size="sm" icon="trash" wire:click="hapusFoto" class="mt-3">
                                Hapus foto
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>

            <flux:input wire:model="name" label="Nama lengkap" type="text" required autofocus autocomplete="name" />

            <flux:input
                wire:model="nim"
                label="NIM"
                type="text"
                autocomplete="off"
                :description="auth()->user()->role === \App\Models\User::ROLE_MAHASISWA ? null : 'Opsional untuk pengurus UKM dan Bagian Kemahasiswaan.'"
            />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Alamat email belum diverifikasi.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Kirim ulang email verifikasi.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('Tautan verifikasi baru sudah dikirim ke alamat emailmu.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                    {{ __('Simpan perubahan') }}
                </flux:button>
            </div>
        </form>

        <section class="mt-8 rounded-xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-zinc-900" aria-label="Informasi akun">
            <h3 class="text-sm font-semibold text-slate-950 dark:text-white">Informasi akun</h3>
            <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-zinc-400">
                Role dan afiliasi UKM hanya dapat diubah oleh Bagian Kemahasiswaan.
            </p>

            <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                <div class="space-y-1">
                    <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Role</dt>
                    <dd class="font-medium text-slate-950 dark:text-white">{{ auth()->user()->roleLabel() }}</dd>
                </div>

                <div class="space-y-1">
                    <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Status akun</dt>
                    <dd class="font-medium text-slate-950 dark:text-white">{{ auth()->user()->isActive() ? 'Aktif' : 'Nonaktif' }}</dd>
                </div>

                <div class="space-y-1">
                    <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Afiliasi UKM</dt>
                    <dd class="font-medium text-slate-950 dark:text-white">
                        {{ auth()->user()->ukm?->nama ?? 'Tidak terafiliasi' }}
                    </dd>
                </div>

                <div class="space-y-1">
                    <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Divisi</dt>
                    <dd class="font-medium text-slate-950 dark:text-white">
                        {{ auth()->user()->divisiUkm?->nama ?? '-' }}
                    </dd>
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-zinc-400">Email terverifikasi</dt>
                    <dd class="font-medium text-slate-950 dark:text-white">
                        {{ auth()->user()->hasVerifiedEmail() ? 'Sudah diverifikasi' : 'Belum diverifikasi' }}
                    </dd>
                </div>
            </dl>
        </section>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>
