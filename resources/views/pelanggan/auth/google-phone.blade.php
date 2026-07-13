<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Satu langkah lagi! Silakan masukkan nomor WhatsApp Anda yang aktif untuk melengkapi pendaftaran akun Zyngga via Google.') }}
    </div>

    <form method="POST" action="{{ route('auth.google.phone.submit') }}">
        @csrf

        <!-- Phone Number -->
        <div>
            <x-input-label for="phone" :value="__('Nomor WhatsApp')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required autofocus autocomplete="tel" placeholder="08xxxxxxxxxx" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Selesaikan Pendaftaran') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
