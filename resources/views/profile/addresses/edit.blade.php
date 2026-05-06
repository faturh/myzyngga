<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ubah Alamat – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-zyngga-blue-50 min-h-screen">

    <div class="min-h-screen flex flex-col">
        <x-dashboard-header 
            title="Ubah Alamat" 
            :backUrl="route('addresses.index')" 
            :maxWidth="'max-w-3xl'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        <main class="w-full max-w-3xl mx-auto py-6 px-5 flex-1 flex flex-col gap-6">
            
            <div class="px-1">
                <x-zyngga-text variant="lg" weight="bold" class="mb-1">Ubah Alamat</x-zyngga-text>
                <x-zyngga-text variant="sm" color="neutral-500">Perbarui detail alamat penjemputan kamu.</x-zyngga-text>
            </div>

            <x-zyngga-card>
                <form action="{{ route('addresses.update', $address) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <x-zyngga-input 
                        label="Label Alamat" 
                        name="label" 
                        placeholder="Contoh: Rumah, Kantor, Kost"
                        :value="old('label', $address->label)"
                        required 
                        autofocus
                        :error="$errors->first('label')"
                    />

                    <x-zyngga-input 
                        label="Alamat Lengkap" 
                        name="address_detail" 
                        placeholder="Nama jalan, nomor rumah, kelurahan, kecamatan"
                        :value="old('address_detail', $address->address_detail)"
                        required 
                        :error="$errors->first('address_detail')"
                    />

                    <x-zyngga-input 
                        label="Catatan (Opsional)" 
                        name="note" 
                        placeholder="Contoh: Pagar warna biru, depan masjid"
                        :value="old('note', $address->note)"
                        :error="$errors->first('note')"
                    />

                    <div class="pt-4">
                        <x-zyngga-button 
                            type="submit" 
                            variant="primary" 
                            size="m" 
                            label="Simpan Perubahan" 
                            class="w-full"
                        />
                    </div>
                </form>
            </x-zyngga-card>

        </main>
    </div>

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
</body>
</html>
