<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <x-zyngga-text variant="lg" weight="medium">{{ __('Informasi Profil') }}</x-zyngga-text>
        <x-zyngga-text variant="sm" color="neutral-500" class="mt-1">
            {{ __("Perbarui informasi profil akun dan alamat email Anda.") }}
        </x-zyngga-text>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <x-zyngga-input 
            label="Name" 
            wire:model="name" 
            id="name" 
            name="name" 
            type="text" 
            required 
            autofocus 
            autocomplete="name"
            :error="$errors->first('name')"
        />

        <x-zyngga-input 
            label="Email" 
            wire:model="email" 
            id="email" 
            name="email" 
            type="email" 
            required 
            autocomplete="username"
            :error="$errors->first('email')"
        />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-normal text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-zyngga-button type="submit" label="Simpan Perubahan" size="m" />

            <x-action-message class="me-3" on="profile-updated">
                <x-zyngga-text variant="xs" class="text-green-600">Tersimpan.</x-zyngga-text>
            </x-action-message>
        </div>
    </form>
</section>
