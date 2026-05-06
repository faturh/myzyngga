<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public string $name = '';
    public string $whatsapp = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:20', 'unique:'.User::class.',username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['username'] = $validated['whatsapp'];
        unset($validated['whatsapp']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen relative flex flex-col justify-center items-center px-4 py-12">
    <!-- Back Button -->
    <a href="{{ route('landing') }}" wire:navigate class="absolute top-6 sm:top-10 left-6 sm:left-10 inline-flex items-center text-zyngga-neutral-400 hover:text-zyngga-blue-300 transition-colors font-medium">
        <i data-feather="arrow-left" class="w-5 h-5 mr-2"></i>
        <x-zyngga-text variant="sm" weight="semibold" color="neutral-900" class="hover:text-zyngga-blue-300">Kembali</x-zyngga-text>
    </a>

    <!-- Main Card -->
    <x-zyngga-card padding="p-8 sm:p-10" class="w-full max-w-[460px] my-8">
        <!-- Illustration -->
        <div class="flex justify-center mb-6">
            <img src="{{ asset('images/auth/register_illustration.png') }}" alt="Register Illustration" class="w-48 h-auto">
        </div>

        <!-- Header -->
        <div class="text-center mb-8">
            <x-zyngga-text as="h1" variant="xl" weight="bold" color="neutral-900" class="mb-2 text-zyngga-neutral-500">Buat Akun Baru</x-zyngga-text>
            <x-zyngga-text variant="sm" weight="regular" color="neutral-500">Daftar untuk menikmati layanan kami</x-zyngga-text>
        </div>

        <form wire:submit="register" class="space-y-4">
            <!-- Name -->
            <x-zyngga-input 
                label="Nama Lengkap" 
                wire:model="name" 
                id="name" 
                type="text" 
                name="name" 
                required 
                autofocus 
                autocomplete="name"
                placeholder="Masukkan nama lengkap"
                :error="$errors->first('name')"
            >
                <x-slot:iconLeft>
                    <i data-feather="user" class="w-[18px] h-[18px]"></i>
                </x-slot:iconLeft>
            </x-zyngga-input>

            <!-- WhatsApp Number -->
            <x-zyngga-input 
                label="Nomor WhatsApp" 
                wire:model="whatsapp" 
                id="whatsapp" 
                type="text" 
                name="whatsapp" 
                required 
                placeholder="Contoh: 081234567890"
                :error="$errors->first('whatsapp')"
            >
                <x-slot:iconLeft>
                    <i data-feather="phone" class="w-[18px] h-[18px]"></i>
                </x-slot:iconLeft>
            </x-zyngga-input>

            <!-- Email Address -->
            <x-zyngga-input 
                label="Email" 
                wire:model="email" 
                id="email" 
                type="email" 
                name="email" 
                required 
                autocomplete="username"
                placeholder="Masukkan email aktif"
                :error="$errors->first('email')"
            >
                <x-slot:iconLeft>
                    <i data-feather="mail" class="w-[18px] h-[18px]"></i>
                </x-slot:iconLeft>
            </x-zyngga-input>

            <!-- Password -->
            <x-zyngga-input 
                label="Kata Sandi" 
                wire:model="password" 
                id="password" 
                type="password" 
                name="password" 
                required 
                autocomplete="new-password"
                placeholder="Minimal 8 karakter"
                :error="$errors->first('password')"
            >
                <x-slot:iconLeft>
                    <i data-feather="lock" class="w-[18px] h-[18px]"></i>
                </x-slot:iconLeft>
            </x-zyngga-input>

            <!-- Confirm Password -->
            <x-zyngga-input 
                label="Konfirmasi Kata Sandi" 
                wire:model="password_confirmation" 
                id="password_confirmation" 
                type="password" 
                name="password_confirmation" 
                required 
                autocomplete="new-password"
                placeholder="Ketik ulang kata sandi"
                :error="$errors->first('password_confirmation')"
            >
                <x-slot:iconLeft>
                    <i data-feather="check-circle" class="w-[18px] h-[18px]"></i>
                </x-slot:iconLeft>
            </x-zyngga-input>

            <!-- Submit Button -->
            <x-zyngga-button 
                type="submit"
                variant="primary"
                size="l"
                class="w-full mt-6"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="register">Daftar</span>
                <span wire:loading wire:target="register" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </span>
            </x-zyngga-button>
        </form>

        <div class="mt-8 text-center">
            <x-zyngga-text variant="sm" weight="regular" color="neutral-500">
                Sudah punya akun? 
                <a href="{{ route('login') }}" wire:navigate>
                    <x-zyngga-text variant="sm" weight="semibold" color="blue-300" as="span" class="hover:text-zyngga-blue-400">Masuk di sini</x-zyngga-text>
                </a>
            </x-zyngga-text>
        </div>
    </x-zyngga-card>
</div>

