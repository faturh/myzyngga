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
    public string $phone = '';
    public bool $isEditing = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->phone = Auth::user()->phone ?? '';
    }

    /**
     * Toggle edit mode.
     */
    public function toggleEdit(): void
    {
        $this->isEditing = !$this->isEditing;
        if (!$this->isEditing) {
            $this->mount(); // Reset data if canceled
        }
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->fill($validated);
        $user->save();

        $this->isEditing = false;
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

    @if($isEditing)
        <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
            <x-zyngga-input 
                label="Nama Lengkap" 
                wire:model="name" 
                id="name" 
                name="name" 
                type="text" 
                required 
                autofocus 
                autocomplete="name"
                :error="$errors->first('name')"
            />

            <div class="relative">
                <x-zyngga-input 
                    label="Email" 
                    wire:model="email" 
                    id="email" 
                    name="email" 
                    type="email" 
                    disabled
                    autocomplete="username"
                    :error="$errors->first('email')"
                />
                <x-zyngga-text variant="2xs" color="neutral-400" class="mt-1.5 ml-1">
                    <i data-feather="info" class="w-3 h-3 inline mr-1 -mt-0.5"></i>
                    Email tidak dapat diubah untuk alasan keamanan
                </x-zyngga-text>
            </div>

            <x-zyngga-input 
                label="Nomor Telepon" 
                wire:model="phone" 
                id="phone" 
                name="phone" 
                type="tel" 
                placeholder="0812xxxxxxx"
                :error="$errors->first('phone')"
            />

            <div class="flex items-center gap-3 pt-2">
                <x-zyngga-button type="submit" label="Simpan Perubahan" size="m" class="flex-1" />
                <x-zyngga-button type="button" wire:click="toggleEdit" label="Batal" variant="secondary" size="m" class="flex-1" />
            </div>

            <x-action-message class="mt-2" on="profile-updated">
                <x-zyngga-text variant="xs" class="text-green-600">Tersimpan.</x-zyngga-text>
            </x-action-message>
        </form>
    @else
        <div class="mt-8 space-y-6">
            {{-- Name Detail --}}
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-zyngga-blue-50 flex items-center justify-center shrink-0">
                    <i data-feather="user" class="w-5 h-5 text-zyngga-blue-300"></i>
                </div>
                <div class="flex-1">
                    <x-zyngga-text variant="xs" color="neutral-400" weight="medium" class="uppercase tracking-widest mb-0.5">Nama Lengkap</x-zyngga-text>
                    <x-zyngga-text variant="base" weight="bold">{{ $name }}</x-zyngga-text>
                </div>
            </div>

            {{-- Email Detail --}}
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-zyngga-blue-50 flex items-center justify-center shrink-0">
                    <i data-feather="mail" class="w-5 h-5 text-zyngga-blue-300"></i>
                </div>
                <div class="flex-1">
                    <x-zyngga-text variant="xs" color="neutral-400" weight="medium" class="uppercase tracking-widest mb-0.5">Alamat Email</x-zyngga-text>
                    <x-zyngga-text variant="base" weight="bold">{{ $email }}</x-zyngga-text>
                </div>
            </div>

            {{-- Phone Detail --}}
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-zyngga-blue-50 flex items-center justify-center shrink-0">
                    <i data-feather="phone" class="w-5 h-5 text-zyngga-blue-300"></i>
                </div>
                <div class="flex-1">
                    <x-zyngga-text variant="xs" color="neutral-400" weight="medium" class="uppercase tracking-widest mb-0.5">Nomor Telepon</x-zyngga-text>
                    <x-zyngga-text variant="base" weight="bold">{{ $phone ?: '-' }}</x-zyngga-text>
                </div>
            </div>

            <div class="pt-4">
                <x-zyngga-button type="button" wire:click="toggleEdit" label="Ubah Profil" variant="secondary" size="m" class="w-full" />
            </div>
        </div>
    @endif
</section>
