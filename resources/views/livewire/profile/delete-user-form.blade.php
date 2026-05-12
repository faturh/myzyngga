<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header>
        <x-zyngga-text variant="sm" weight="medium" color="danger">{{ __('Hapus Akun') }}</x-zyngga-text>
        <x-zyngga-text variant="xs" color="neutral-500" class="mt-1 block leading-relaxed">
            {{ __('Setelah akun dihapus, semua data Anda akan hilang secara permanen. Harap berhati-hati sebelum melanjutkan.') }}
        </x-zyngga-text>
    </header>

    <x-zyngga-button
        variant="danger"
        size="m"
        label="Hapus Akun Sekarang"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    />

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6">

            <x-zyngga-text variant="lg" weight="medium">
                {{ __('Apakah Anda yakin ingin menghapus akun?') }}
            </x-zyngga-text>

            <x-zyngga-text variant="sm" color="neutral-500" class="mt-2">
                {{ __('Setelah akun Anda dihapus, semua data akan hilang secara permanen. Masukkan kata sandi Anda untuk mengonfirmasi penghapusan.') }}
            </x-zyngga-text>

            <div class="mt-6">
                <x-zyngga-input 
                    label="Password" 
                    wire:model="password" 
                    id="password" 
                    name="password" 
                    type="password" 
                    class="w-3/4"
                    placeholder="Password"
                    :error="$errors->first('password')"
                />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-zyngga-button variant="neutral" label="Batal" x-on:click="$dispatch('close')" />
                <x-zyngga-button variant="danger" label="Hapus Akun" type="submit" />
            </div>
        </form>
    </x-modal>
</section>
