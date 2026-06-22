<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public string $name = '';
    public string $whatsapp = '';
    public string $email = '';
    public string $password = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'whatsapp' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:9', 'max:15', 'unique:'.User::class.',username'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama lengkap minimal 3 karakter.',
            'name.max' => 'Nama lengkap terlalu panjang (maksimal 255 karakter).',
            
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
            'whatsapp.min' => 'Nomor WhatsApp minimal 9 angka.',
            'whatsapp.max' => 'Nomor WhatsApp maksimal 15 angka.',
            'whatsapp.unique' => 'Nomor WhatsApp ini sudah terdaftar.',
            
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid (pastikan format benar, contoh: nama@email.com).',
            'email.max' => 'Email terlalu panjang.',
            'email.unique' => 'Email ini sudah terdaftar.',
            
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Buat password minimal 8 karakter.',
        ]);

        $username = $validated['whatsapp'];
        $baseSlug = Str::slug($validated['name']) ?: Str::before($validated['email'], '@');
        $slug = $baseSlug;
        $suffix = 1;

        while (User::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        $payload = [
            'name' => $validated['name'],
            'username' => $username,
            'slug' => $slug,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
        ];

        $user = User::create($payload);

        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $customerRole = \Spatie\Permission\Models\Role::query()->firstOrCreate([
                'name' => 'customer',
                'guard_name' => 'web',
            ]);

            $user->assignRole($customerRole);
        }

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('home', absolute: false));
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
                <x-zyngga-text as="h1" variant="2xl" weight="medium" color="neutral-900" class="mb-2">Buat Akun Baru</x-zyngga-text>
                <x-zyngga-text variant="sm" weight="regular" color="neutral-500">Lengkapi data berikut untuk mulai melakukan pesanan dengan mudah.</x-zyngga-text>
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
                />

                <!-- WhatsApp Number -->
                <x-zyngga-input 
                    label="Nomor WhatsApp" 
                    wire:model="whatsapp" 
                    id="whatsapp" 
                    type="text" 
                    name="whatsapp" 
                    required 
                    placeholder="Masukkan nomor WhatsApp"
                    :error="$errors->first('whatsapp')"
                />

                <!-- Email Address -->
                <x-zyngga-input 
                    label="Email" 
                    wire:model="email" 
                    id="email" 
                    type="email" 
                    name="email" 
                    required 
                    autocomplete="username"
                    placeholder="Masukkan alamat email aktif"
                    :error="$errors->first('email')"
                />

                <!-- Password -->
                <div x-data="{ showPassword: false }">
                    <x-zyngga-input 
                        label="Password" 
                        wire:model="password" 
                        id="reg_password" 
                        type="password"
                        name="password" 
                        required 
                        autocomplete="new-password"
                        placeholder="Buat kata sandi"
                        :error="$errors->first('password')"
                    >
                        <x-slot:iconRight>
                            <button type="button" @click="let el = document.getElementById('reg_password'); el.type = el.type === 'password' ? 'text' : 'password'; showPassword = !showPassword" class="focus:outline-none flex items-center justify-center p-1 hover:text-zyngga-neutral-500 transition-colors">
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

                <!-- Submit Button -->
                <x-zyngga-button 
                    type="submit"
                    variant="primary"
                    size="l"
                    class="w-full mt-2"
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

            <div class="mt-4 text-center">
                <x-zyngga-text variant="sm" weight="regular" color="neutral-500">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" wire:navigate>
                        <x-zyngga-text variant="sm" weight="medium" color="primary" as="span" class="hover:text-zyngga-blue-400">Masuk di sini</x-zyngga-text>
                    </a>
                </x-zyngga-text>
            </div>
        </div>
    </div>
</div>
