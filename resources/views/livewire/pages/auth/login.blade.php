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

        $this->redirectIntended(default: route('home', absolute: false));
    }
}; ?>

<div class="min-h-screen relative flex flex-col md:flex-row bg-gradient-to-r from-[#A5C0EE] to-[#E8F0FE] animate-gradient-x overflow-hidden">
    
    <!-- Spacer for mobile gradient area -->
    <div class="relative flex-1 min-h-[120px] md:hidden w-full"></div>

    <!-- Right Side (White Container) - Desktop: Right Half, Mobile: Bottom Sheet -->
    <div class="bg-white rounded-t-[2rem] md:rounded-none md:w-[50%] md:flex-none w-full flex flex-col z-10 px-8 pt-8 pb-12 md:p-12 relative md:ml-auto md:min-h-screen">
        
        <!-- Back Button -->
        <x-zyngga-button 
            type="a"
            href="{{ route('landing') }}"
            wire:navigate
            variant="tertiary"
            size="m"
            icon="arrow-left"
            iconPosition="left"
            label="Kembali"
            class="!text-zyngga-neutral-500 hover:!bg-zyngga-neutral-200 mb-5 md:mb-12 self-start -ml-4"
        />

        <!-- Form Container -->
        <div class="w-full max-w-[420px] mx-auto flex flex-col justify-center flex-1">
            
            <!-- Header -->
            <div class="mb-6">
                <x-zyngga-text as="h1" variant="2xl" weight="medium" color="neutral-900" class="mb-2">Selamat Datang Kembali!</x-zyngga-text>
                <x-zyngga-text variant="sm" weight="regular" color="neutral-500">Silakan masuk untuk melanjutkan pesanan dan melihat riwayat transaksimu.</x-zyngga-text>
            </div>

            <form wire:submit="login" class="space-y-4">
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
                    placeholder="Masukkan email kamu"
                    :error="$errors->first('form.email')"
                />

                <!-- Password -->
                <div x-data="{ showPassword: false }">
                    <x-zyngga-input 
                        label="Password" 
                        wire:model="form.password" 
                        id="password" 
                        type="password"
                        name="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="Masukkan kata sandi"
                        :error="$errors->first('form.password')"
                    >
                        <x-slot:iconRight>
                            <button type="button" @click="let el = document.getElementById('password'); el.type = el.type === 'password' ? 'text' : 'password'; showPassword = !showPassword" class="focus:outline-none flex items-center justify-center p-1 hover:text-zyngga-neutral-500 transition-colors">
                                <div x-show="!showPassword">
                                    <i data-feather="eye-off" class="w-4 h-4 text-zyngga-neutral-500"></i>
                                </div>
                                <div x-show="showPassword" x-cloak>
                                    <i data-feather="eye" class="w-4 h-4 text-zyngga-neutral-500"></i>
                                </div>
                            </button>
                        </x-slot:iconRight>
                    </x-zyngga-input>
                </div>

                <!-- Options -->
                <div class="flex items-center justify-between mt-4 pb-2">
                    <label for="remember" class="inline-flex items-center cursor-pointer">
                        <input wire:model="form.remember" id="remember" type="checkbox" class="rounded-[4px] border-zyngga-blue-300 text-zyngga-blue-300 shadow-sm focus:ring-zyngga-blue-300 w-4 h-4 cursor-pointer mt-0.5">
                        <x-zyngga-text variant="sm" weight="regular" color="neutral-900" class="ms-2">Ingat saya</x-zyngga-text>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" wire:navigate>
                            <x-zyngga-text variant="sm" weight="medium" color="primary" class="hover:text-zyngga-blue-400">Lupa kata sandi?</x-zyngga-text>
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

            <!-- Google Sign-In Button -->
            <div class="relative flex py-3 items-center">
                <div class="flex-grow border-t border-zyngga-neutral-200"></div>
                <span class="flex-shrink mx-4 text-zyngga-neutral-400 text-xs uppercase">Atau</span>
                <div class="flex-grow border-t border-zyngga-neutral-200"></div>
            </div>

            <x-zyngga-button 
                type="a"
                href="/api/v1/auth/google"
                variant="secondary"
                size="l"
                class="w-full flex items-center justify-center gap-2 hover:!bg-zyngga-neutral-100 !text-zyngga-neutral-800 border border-zyngga-neutral-300"
            >
                <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
                    <g transform="matrix(1, 0, 0, 1, 0, 0)">
                        <path d="M21.35,11.1H12v2.7h5.38C17.15,14.8 16,15.7 14.5,16.5l2.3,1.8c2.9-2.7 4.6-6.6 4.6-11.2C21.35,11.7 21.35,11.4 21.35,11.1Z" fill="#4285F4" />
                        <path d="M12,20.7c2.4,0 4.5-0.8 6-2.2L15.7,16.7c-0.8,0.5-1.9,0.9-3.7,0.9-2.8,0-5.2-1.9-6-4.5L3.6,15C5.4,18.4 8.5,20.7 12,20.7Z" fill="#34A853" />
                        <path d="M6,13.1c-0.2-0.6-0.3-1.3-0.3-2s0.1-1.4 0.3-2L3.6,7C2.9,8.4 2.5,9.9 2.5,11.5s0.4,3.1 1.1,4.5L6,13.1Z" fill="#FBBC05" />
                        <path d="M12,5.3c1.3,0 2.5,0.5 3.4,1.3l2.6-2.6C16.4,2.5 14.4,1.8 12,1.8c-3.5,0-6.6,2.3-8.4,5.7L6,9.6c0.8-2.6 3.2-4.5 6-4.5Z" fill="#EA4335" />
                    </g>
                </svg>
                Masuk dengan Google
            </x-zyngga-button>

            <div class="mt-4 text-center">
                <x-zyngga-text variant="sm" weight="regular" color="neutral-500">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" wire:navigate>
                        <x-zyngga-text variant="sm" weight="medium" color="primary" as="span" class="hover:text-zyngga-blue-400">Daftar di sini</x-zyngga-text>
                    </a>
                </x-zyngga-text>
            </div>
        </div>
    </div>
</div>

