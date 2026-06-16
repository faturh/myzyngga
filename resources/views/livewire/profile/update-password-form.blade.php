<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header>
        <x-zyngga-text variant="lg" weight="medium">{{ __('Update Password') }}</x-zyngga-text>
        <x-zyngga-text variant="sm" color="neutral-500" class="mt-1">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.') }}
        </x-zyngga-text>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-6">
        <x-zyngga-input 
            label="Current Password" 
            wire:model="current_password" 
            id="update_password_current_password" 
            name="current_password" 
            type="password" 
            autocomplete="current-password"
            :error="$errors->first('current_password')"
        />

        <x-zyngga-input 
            label="New Password" 
            wire:model="password" 
            id="update_password_password" 
            name="password" 
            type="password" 
            autocomplete="new-password"
            :error="$errors->first('password')"
        />

        <x-zyngga-input 
            label="Confirm Password" 
            wire:model="password_confirmation" 
            id="update_password_password_confirmation" 
            name="password_confirmation" 
            type="password" 
            autocomplete="new-password"
            :error="$errors->first('password_confirmation')"
        />

        <div class="flex items-center gap-4">
            <x-zyngga-button type="submit" label="Perbarui Password" size="m" />

            <x-action-message class="me-3" on="password-updated">
                <x-zyngga-text variant="xs" class="text-green-600">Berhasil diperbarui.</x-zyngga-text>
            </x-action-message>
        </div>
    </form>
</section>
