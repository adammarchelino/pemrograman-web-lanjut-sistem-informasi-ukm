<?php

namespace App\Livewire\Account;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Ganti Kata Sandi — SIP-UKM')]
class ChangePassword extends Component
{
    public string $password = '';

    public string $password_confirmation = '';

    public function save(): void
    {
        abort_unless(Auth::user()->isActive(), 403);

        $this->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
            'password_confirmation' => ['required', 'string'],
        ]);

        Auth::user()->update([
            'password' => $this->password,
            'wajib_ganti_password' => false,
        ]);

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.account.change-password');
    }
}
