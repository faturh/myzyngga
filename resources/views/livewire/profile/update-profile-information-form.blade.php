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
        // dd($this->name, $this->phone);
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        // Direct update to ensure it bypasses any model accessor issues during save
        User::where('id', Auth::id())->update([
            'name' => $this->name,
            'phone' => $this->phone,
        ]);

        // Pelanggan.nama/telepon adalah field terpisah dari User — dipakai di
        // riwayat pesanan, nota, dan komplain. Tanpa ini, perubahan nama/telepon
        // tidak akan pernah terlihat di manapun kecuali di form ini sendiri.
        \App\Models\Pelanggan::where('user_id', Auth::id())->update([
            'nama' => $this->name,
            'telepon' => $this->phone,
        ]);

        $this->dispatch('profile-updated', name: $this->name);
        
        $this->redirect(route('profile'), navigate: true);
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

<div x-data="{ 
    currentName: @entangle('name'),
    currentPhone: @entangle('phone'),
    draftName: '', 
    draftPhone: '',
    nameError: '',
    phoneError: '',
    init() {
        this.draftName = this.currentName;
        this.draftPhone = this.currentPhone;
        this.$watch('currentName', value => this.draftName = value);
        this.$watch('currentPhone', value => this.draftPhone = value);
    },
    get isDirty() {
        let origName = ({{ json_encode($originalName) }} || '').toString().trim();
        let origPhone = ({{ json_encode($originalPhone) }} || '').toString().trim();
        let currName = (this.currentName || '').toString().trim();
        let currPhone = (this.currentPhone || '').toString().trim();
        return currName !== origName || currPhone !== origPhone;
    }
}">
    <form wire:submit="updateProfileInformation">
    {{-- Card 1: Profile Picture --}}
    <x-zyngga-card>
        <div class="flex flex-col items-center justify-center">
            <div class="w-24 h-24 rounded-full bg-zyngga-blue-300 flex items-center justify-center text-white text-4xl font-medium">
                <span x-text="currentName ? currentName.charAt(0).toUpperCase() : '?'"></span>
            </div>
        </div>
    </x-zyngga-card>

    {{-- Card 2: Informasi Akun --}}
    <x-zyngga-card title="Informasi Akun" wire:key="profile-info-card">
        <div class="flex flex-col">
            {{-- Name Item --}}
            <button type="button" @click="window.dispatchEvent(new CustomEvent('open-name-modal'))" class="flex items-center justify-between h-[48px] text-left group">
                <div class="flex items-center gap-3">
                    <i data-feather="user" class="w-[18px] h-[18px] text-zyngga-neutral-500"></i>
                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Nama Lengkap</x-zyngga-text>
                </div>
                <div class="flex items-center gap-2">
                    <x-zyngga-text variant="sm" weight="medium" class="text-zyngga-neutral-900" x-text="currentName"></x-zyngga-text>
                    <i data-feather="chevron-right" class="w-4 h-4 text-zyngga-blue-300"></i>
                </div>
            </button>

            <x-zyngga-divider class="my-2" />

            {{-- Phone Item --}}
            <button type="button" @click="window.dispatchEvent(new CustomEvent('open-phone-modal'))" class="flex items-center justify-between h-[48px] text-left group">
                <div class="flex items-center gap-3">
                    <i data-feather="phone" class="w-[18px] h-[18px] text-zyngga-neutral-500"></i>
                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Nomor WhatsApp</x-zyngga-text>
                </div>
                <div class="flex items-center gap-2">
                    <x-zyngga-text variant="sm" weight="medium" class="text-zyngga-neutral-900" x-text="currentPhone || '-'"></x-zyngga-text>
                    <i data-feather="chevron-right" class="w-4 h-4 text-zyngga-blue-300"></i>
                </div>
            </button>

            <x-zyngga-divider class="my-2" />

            {{-- Email Item (Non-editable) --}}
            <div class="flex items-center justify-between h-[48px] text-left">
                <div class="flex items-center gap-3">
                    <i data-feather="mail" class="w-[18px] h-[18px] text-zyngga-neutral-500"></i>
                    <x-zyngga-text variant="sm" weight="medium" color="neutral-900">Email</x-zyngga-text>
                </div>
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
                </div>
            </div>
        </div>
    </x-zyngga-card>

    {{-- Sticky Footer --}}
    <div class="fixed bottom-0 left-0 right-0 py-4 bg-white border-t border-zyngga-neutral-50 shadow-[0_-4px_16px_rgba(0,0,0,0.08)] z-50 rounded-t-[16px]">
        <div class="max-w-5xl mx-auto w-full px-5">
            <x-zyngga-button 
                type="submit" 
                label="Simpan" 
                variant="primary"
                size="l" 
                class="w-full" 
                :disabled="!$this->hasChanges()"
                x-bind:disabled="!isDirty"
                wire:loading.attr="disabled"
                wire:target="updateProfileInformation"
            />
        </div>
    </div>
</form>

    {{-- Modals --}}
    <x-zyngga-selection-modal id="modal-name" title="Ubah Nama Lengkap" openEvent="open-name-modal">
        <div class="flex flex-col">
            <div>
                <x-zyngga-text variant="sm" weight="regular" class="mb-1.5 block">Nama Lengkap</x-zyngga-text>
                <x-zyngga-input 
                    x-model="draftName" 
                    name="name"
                    id="name-input" 
                    placeholder="Masukkan nama lengkap"
                    class="!rounded-full"
                />
                <span class="text-xs text-red-500 mt-1 block" x-show="nameError" x-text="nameError"></span>
            </div>
            @if($errors->has('name'))
                <span class="text-xs text-red-500 mt-1 block">{{ $errors->first('name') }}</span>
            @endif
            <div class="mt-6 flex gap-3">
                <x-zyngga-button type="button" @click="draftName = currentName; nameError = ''; isOpen = false" variant="secondary" label="Batal" class="flex-1" />
                <x-zyngga-button type="button" 
                    @click="
                        if (!draftName.trim()) {
                            nameError = 'Nama lengkap tidak boleh kosong';
                        } else if (/\d/.test(draftName)) {
                            nameError = 'Nama tidak boleh mengandung angka';
                        } else {
                            nameError = '';
                            currentName = draftName;
                            isOpen = false;
                        }
                    " 
                    variant="primary" label="Simpan" class="flex-1" />
            </div>
        </div>
    </x-zyngga-selection-modal>

    <x-zyngga-selection-modal id="modal-phone" title="Ubah Nomor WhatsApp" openEvent="open-phone-modal">
        <div class="flex flex-col">
            <div>
                <x-zyngga-text variant="sm" weight="regular" class="mb-1.5 block">Nomor WhatsApp</x-zyngga-text>
                <x-zyngga-input 
                    x-model="draftPhone" 
                    name="phone"
                    id="phone-input" 
                    type="tel"
                    placeholder="0812xxxxxxx"
                    class="!rounded-full"
                />
                <span class="text-xs text-red-500 mt-1 block" x-show="phoneError" x-text="phoneError"></span>
            </div>
            @if($errors->has('phone'))
                <span class="text-xs text-red-500 mt-1 block">{{ $errors->first('phone') }}</span>
            @endif
            <div class="mt-6 flex gap-3">
                <x-zyngga-button type="button" @click="draftPhone = currentPhone; phoneError = ''; isOpen = false" variant="secondary" label="Batal" class="flex-1" />
                <x-zyngga-button type="button" 
                    @click="
                        if (!draftPhone.trim()) {
                            phoneError = 'Nomor WhatsApp tidak boleh kosong';
                        } else if (!/^\d+$/.test(draftPhone)) {
                            phoneError = 'Nomor WhatsApp hanya boleh berisi angka';
                        } else {
                            phoneError = '';
                            currentPhone = draftPhone;
                            isOpen = false;
                        }
                    " 
                    variant="primary" label="Simpan" class="flex-1" />
            </div>
        </div>
    </x-zyngga-selection-modal>
</div>
