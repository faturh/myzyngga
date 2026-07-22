<x-auth-layout>
    <div class="min-h-screen relative flex flex-col md:flex-row bg-gradient-to-r from-[#A5C0EE] to-[#E8F0FE] animate-gradient-x overflow-hidden">
        
        <!-- Spacer for mobile gradient area -->
        <div class="relative flex-1 min-h-[120px] md:hidden w-full"></div>

        <!-- Right Side (White Container) -->
        <div class="bg-white rounded-t-[2rem] md:rounded-none md:w-[50%] md:flex-none w-full flex flex-col z-10 px-8 pt-8 pb-12 md:p-12 relative md:ml-auto md:min-h-screen">
            
            <!-- Form Container -->
            <div class="w-full max-w-[420px] mx-auto flex flex-col justify-center flex-1">
                
                <!-- Header -->
                <div class="mb-6">
                    <x-zyngga-text as="h1" variant="2xl" weight="medium" color="neutral-900" class="mb-2">Satu Langkah Lagi!</x-zyngga-text>
                    <x-zyngga-text variant="sm" weight="regular" color="neutral-500">Silakan masukkan nomor WhatsApp Anda yang aktif untuk melengkapi pendaftaran akun Zyngga via Google.</x-zyngga-text>
                </div>

                <form method="POST" action="{{ route('auth.google.phone.submit') }}" class="space-y-4">
                    @csrf

                    <!-- Phone Number -->
                    <x-zyngga-input 
                        label="Nomor WhatsApp" 
                        id="phone" 
                        type="text" 
                        name="phone" 
                        :value="old('phone')"
                        required 
                        autofocus
                        placeholder="08xxxxxxxxxx"
                        :error="$errors->first('phone')"
                    />

                    <div class="pt-4">
                        <x-zyngga-button 
                            type="submit"
                            variant="primary"
                            size="l"
                            class="w-full"
                        >
                            Selesaikan Pendaftaran
                        </x-zyngga-button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</x-auth-layout>
