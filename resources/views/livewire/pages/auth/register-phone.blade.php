<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public string $whatsapp = '';

    public function mount()
    {
        if (!Auth::check() || !empty(Auth::user()->phone)) {
            $this->redirect(route('home', absolute: false));
        }
    }

    public function savePhone(): void
    {
        $validated = $this->validate([
            'whatsapp' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:9', 'max:15'],
        ], [
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
            'whatsapp.min' => 'Nomor WhatsApp minimal 9 angka.',
            'whatsapp.max' => 'Nomor WhatsApp maksimal 15 angka.',
        ]);

        $user = User::find(Auth::id());
        
        // Ensure the phone number isn't already used by someone else
        $existing = User::where('phone', $validated['whatsapp'])->where('id', '!=', $user->id)->first();
        if ($existing) {
            $this->addError('whatsapp', 'Nomor WhatsApp ini sudah terdaftar.');
            return;
        }

        $user->phone = $validated['whatsapp'];
        // Also update username if we're using whatsapp as username and it's a google user
        if (str_starts_with($user->username, 'google_')) {
            $user->username = $validated['whatsapp'];
        }
        $user->save();

        $this->redirect(route('dashboard', absolute: false));
    }
}; ?>

<div class="min-h-screen relative flex flex-col md:flex-row bg-gradient-to-r from-[#A5C0EE] to-[#E8F0FE] animate-gradient-x overflow-hidden">
    
    <!-- Spacer for mobile gradient area -->
    <div class="relative flex-1 min-h-[120px] md:hidden w-full"></div>

    <!-- Right Side (White Container) -->
    <div class="bg-white rounded-t-[2rem] md:rounded-none md:w-[50%] md:flex-none w-full flex flex-col z-10 px-8 pt-8 pb-12 md:p-12 relative md:ml-auto md:min-h-screen">
        
        <!-- Form Container -->
        <div class="w-full max-w-[420px] mx-auto flex flex-col justify-center flex-1">
            
            <!-- Header -->
            <div class="mb-6">
                <x-zyngga-text as="h1" variant="2xl" weight="medium" color="neutral-900" class="mb-2">Lengkapi Data Anda</x-zyngga-text>
                <x-zyngga-text variant="sm" weight="regular" color="neutral-500">Silakan masukkan nomor WhatsApp Anda agar kami dapat mengirimkan notifikasi saat pesanan Anda selesai.</x-zyngga-text>
            </div>

            <form wire:submit="savePhone" class="space-y-4">
                <!-- WhatsApp Number -->
                <x-zyngga-input 
                    label="Nomor WhatsApp" 
                    wire:model="whatsapp" 
                    id="whatsapp" 
                    type="text" 
                    name="whatsapp" 
                    required 
                    autofocus
                    placeholder="Masukkan nomor WhatsApp"
                    :error="$errors->first('whatsapp')"
                />

                <div class="pt-4">
                    <x-zyngga-button 
                        type="submit"
                        variant="primary"
                        size="l"
                        class="w-full"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="savePhone">Simpan dan Lanjutkan</span>
                        <span wire:loading wire:target="savePhone" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </x-zyngga-button>
                </div>
            </form>
        </div>
        
    </div>
</div>
