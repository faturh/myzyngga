<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $username = trim($validated['name']);
        $baseSlug = Str::slug($username) ?: Str::before($validated['email'], '@');
        $slug = $baseSlug;
        $suffix = 1;

        while (User::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        $payload = [
            'username' => $username,
            'slug' => $slug,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
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

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register">
        <!-- Name -->
        <x-zyngga-input 
            label="Name" 
            wire:model="name" 
            id="name" 
            type="text" 
            name="name" 
            required 
            autofocus 
            autocomplete="name"
            :error="$errors->first('name')"
        >
            <x-slot:iconLeft>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </x-slot:iconLeft>
        </x-zyngga-input>

        <!-- Email Address -->
        <x-zyngga-input 
            label="Email" 
            wire:model="email" 
            id="email" 
            class="mt-4"
            type="email" 
            name="email" 
            required 
            autocomplete="username"
            :error="$errors->first('email')"
        >
            <x-slot:iconLeft>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
            </x-slot:iconLeft>
        </x-zyngga-input>

        <!-- Password -->
        <x-zyngga-input 
            label="Password" 
            wire:model="password" 
            id="password" 
            class="mt-4"
            type="password" 
            name="password" 
            required 
            autocomplete="new-password"
            :error="$errors->first('password')"
        >
            <x-slot:iconLeft>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </x-slot:iconLeft>
        </x-zyngga-input>

        <!-- Confirm Password -->
        <x-zyngga-input 
            label="Confirm Password" 
            wire:model="password_confirmation" 
            id="password_confirmation" 
            class="mt-4"
            type="password" 
            name="password_confirmation" 
            required 
            autocomplete="new-password"
            :error="$errors->first('password_confirmation')"
        >
            <x-slot:iconLeft>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </x-slot:iconLeft>
        </x-zyngga-input>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-zyngga-button 
                type="submit"
                variant="primary"
                size="m"
                label="Register"
                class="ms-4"
            />
        </div>
    </form>
</div>
