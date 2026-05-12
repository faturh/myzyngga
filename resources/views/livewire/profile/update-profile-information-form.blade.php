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
    public string $originalName = '';
    public string $originalPhone = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        
        $this->originalName = $this->name;
        $this->originalPhone = $this->phone;
    }

    /**
     * Revert changes if user cancels.
     */
    public function cancelChanges(): void
    {
        $this->name = $this->originalName;
        $this->phone = $this->originalPhone;
        $this->resetErrorBag();
    }

    /**
     * Check if there are any changes to the profile information.
     */
    public function hasChanges(): bool
    {
        return $this->name !== $this->originalName || $this->phone !== $this->originalPhone;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        if (!$this->hasChanges()) return;

        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->fill($validated);
        $user->save();

        // Update originals after successful save
        $this->originalName = $this->name;
        $this->originalPhone = $this->phone;

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

<section x-data="{}">
    {{-- Card 1: Profile Picture --}}
    <x-zyngga-card class="mb-3">
        <div class="flex flex-col items-center justify-center py-4">
            <div class="w-24 h-24 rounded-full bg-zyngga-blue-300 flex items-center justify-center text-white text-4xl font-medium mb-4">
                {{ substr($name, 0, 1) }}
            </div>
            <x-zyngga-button 
                type="button" 
                variant="secondary" 
                size="s" 
                class="rounded-full px-6"
            >
                <div class="flex items-center gap-2">
                    <i data-feather="edit-3" class="w-3.5 h-3.5"></i>
                    <span>Ubah Foto</span>
                </div>
            </x-zyngga-button>
        </div>
    </x-zyngga-card>

    {{-- Card 2: Informasi Akun --}}
    <x-zyngga-card title="Informasi Akun" wire:key="info-card-{{ $name }}-{{ $phone }}">
        <div class="flex flex-col">
            {{-- Name Item --}}
            <button @click="window.dispatchEvent(new CustomEvent('open-name-modal'))" class="flex items-center justify-between py-4 border-b border-zyngga-neutral-50 text-left group">
                <x-zyngga-text variant="sm" color="neutral-900">Nama Lengkap</x-zyngga-text>
                <div class="flex items-center gap-2">
                    <x-zyngga-text wire:key="name-display" variant="sm" weight="medium" class="text-zyngga-neutral-900">{{ $name }}</x-zyngga-text>
                    <i data-feather="chevron-right" class="w-4 h-4 text-zyngga-blue-300"></i>
                </div>
            </button>

            {{-- Phone Item --}}
            <button @click="window.dispatchEvent(new CustomEvent('open-phone-modal'))" class="flex items-center justify-between py-4 border-b border-zyngga-neutral-50 text-left group">
                <x-zyngga-text variant="sm" color="neutral-900">Nomor WhatsApp</x-zyngga-text>
                <div class="flex items-center gap-2">
                    <x-zyngga-text wire:key="phone-display" variant="sm" weight="medium" class="text-zyngga-neutral-900">{{ $phone ?: '-' }}</x-zyngga-text>
                    <i data-feather="chevron-right" class="w-4 h-4 text-zyngga-blue-300"></i>
                </div>
            </button>

            {{-- Email Item (Non-editable) --}}
            <div class="flex items-center justify-between py-4 text-left">
                <x-zyngga-text variant="sm" color="neutral-900">Email</x-zyngga-text>
                <div class="flex items-center gap-2">
                    @php
                        $emailParts = explode('@', $email);
                        $namePart = $emailParts[0];
                        $domainPart = $emailParts[1];
                        $maskedName = strlen($namePart) > 2 
                            ? substr($namePart, 0, 1) . str_repeat('*', 3) . substr($namePart, -1) 
                            : $namePart;
                        $maskedEmail = $maskedName . '@' . $domainPart;
                    @endphp
                    <x-zyngga-text variant="sm" weight="medium" class="text-zyngga-neutral-900">{{ $maskedEmail }}</x-zyngga-text>
                    <i data-feather="chevron-right" class="w-4 h-4 text-zyngga-blue-300"></i>
                </div>
            </div>
        </div>
    </x-zyngga-card>

    {{-- Sticky Footer --}}
    <div class="fixed bottom-0 left-0 right-0 p-5 bg-white border-t border-zyngga-neutral-50 shadow-[0_-4px_16px_rgba(0,0,0,0.08)] z-50 rounded-t-[16px]">
        <div class="max-w-5xl mx-auto flex gap-4">
            <x-zyngga-button 
                type="button" 
                @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'confirm-user-deletion' }))"
                label="Hapus Akun" 
                variant="secondary" 
                size="l" 
                class="flex-1 !text-red-500 !border-red-500 hover:!bg-red-50" 
            />
            <x-zyngga-button 
                type="button" 
                wire:click="updateProfileInformation"
                label="Simpan" 
                variant="primary"
                size="l" 
                class="flex-1 {{ !$this->hasChanges() ? 'opacity-40 pointer-events-none' : '' }}" 
                :disabled="!$this->hasChanges()"
            />
        </div>
    </div>

    {{-- Modals --}}
    <x-zyngga-selection-modal id="modal-name" title="Ubah Nama Lengkap" openEvent="open-name-modal">
        <div class="flex flex-col gap-4">
            <x-zyngga-input 
                label="Nama Lengkap" 
                wire:model.live="name" 
                name="name"
                id="name-input" 
                placeholder="Masukkan nama lengkap"
                :error="$errors->first('name')"
            />
            <div class="mt-2 flex justify-end gap-3">
                <x-zyngga-button type="button" @click="isOpen = false" wire:click="cancelChanges" variant="secondary" label="Batal" />
                <x-zyngga-button type="button" @click="isOpen = false" variant="primary" label="Terapkan" />
            </div>
        </div>
    </x-zyngga-selection-modal>

    <x-zyngga-selection-modal id="modal-phone" title="Ubah Nomor WhatsApp" openEvent="open-phone-modal">
        <div class="flex flex-col gap-4">
            <x-zyngga-input 
                label="Nomor WhatsApp" 
                wire:model.live="phone" 
                name="phone"
                id="phone-input" 
                type="tel"
                placeholder="0812xxxxxxx"
                :error="$errors->first('phone')"
            />
            <div class="mt-2 flex justify-end gap-3">
                <x-zyngga-button type="button" @click="isOpen = false" wire:click="cancelChanges" variant="secondary" label="Batal" />
                <x-zyngga-button type="button" @click="isOpen = false" variant="primary" label="Terapkan" />
            </div>
        </div>
    </x-zyngga-selection-modal>
</section>
