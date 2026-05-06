<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen relative flex flex-col justify-center items-center px-4 py-12">
    <!-- Back Button -->
    <a href="{{ route('landing') }}" wire:navigate class="absolute top-6 sm:top-10 left-6 sm:left-10 inline-flex items-center text-zyngga-neutral-400 hover:text-zyngga-blue-300 transition-colors font-medium">
        <i data-feather="arrow-left" class="w-5 h-5 mr-2"></i>
        <x-zyngga-text variant="sm" weight="semibold" color="neutral-900" class="hover:text-zyngga-blue-300">Kembali</x-zyngga-text>
    </a>

    <!-- Main Card -->
    <x-zyngga-card padding="p-8 sm:p-10" class="w-full max-w-[420px]">
        <!-- Illustration -->
        <div class="flex justify-center mb-6">
            <img src="{{ asset('images/auth/login_illustration.png') }}" alt="Login Illustration" class="w-48 h-auto">
        </div>
        
        <!-- Header -->
        <div class="text-center mb-8">
            <x-zyngga-text as="h1" variant="xl" weight="bold" color="neutral-900" class="mb-2 text-zyngga-neutral-500">Selamat Datang</x-zyngga-text>
            <x-zyngga-text variant="sm" weight="regular" color="neutral-500">Silakan masuk untuk melanjutkan</x-zyngga-text>
        </div>

        <form wire:submit="login" class="space-y-5">
            <!-- Email -->
            <x-zyngga-input 
                label="Email" 
                wire:model="form.email" 
                id="email" 
                type="email" 
                name="email" 
                required 
                autofocus 
                autocomplete="email"
                placeholder="Masukkan email Anda"
                :error="$errors->first('form.email')"
            >
                <x-slot:iconLeft>
                    <i data-feather="mail" class="w-[18px] h-[18px]"></i>
                </x-slot:iconLeft>
            </x-zyngga-input>

            <!-- Password -->
            <x-zyngga-input 
                label="Kata Sandi" 
                wire:model="form.password" 
                id="password" 
                type="password" 
                name="password" 
                required 
                autocomplete="current-password"
                placeholder="Masukkan kata sandi"
                :error="$errors->first('form.password')"
            >
                <x-slot:iconLeft>
                    <i data-feather="lock" class="w-[18px] h-[18px]"></i>
                </x-slot:iconLeft>
            </x-zyngga-input>

            <!-- Options -->
            <div class="flex items-center justify-between mt-4">
                <label for="remember" class="inline-flex items-center cursor-pointer">
                    <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-zyngga-blue-50 text-zyngga-blue-300 shadow-sm focus:ring-zyngga-blue-300 w-4 h-4 cursor-pointer">
                    <x-zyngga-text variant="sm" weight="regular" color="neutral-500" class="ms-2">Ingat saya</x-zyngga-text>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate>
                        <x-zyngga-text variant="sm" weight="semibold" color="blue-300" class="hover:text-zyngga-blue-400">Lupa kata sandi?</x-zyngga-text>
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <x-zyngga-button 
                type="submit"
                variant="primary"
                size="l"
                class="w-full"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="login">Masuk</span>
                <span wire:loading wire:target="login" class="flex items-center">
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
                Belum punya akun? 
                <a href="{{ route('register') }}" wire:navigate>
                    <x-zyngga-text variant="sm" weight="semibold" color="blue-300" as="span" class="hover:text-zyngga-blue-400">Daftar sekarang</x-zyngga-text>
                </a>
            </x-zyngga-text>
        </div>
    </x-zyngga-card>
</div>

