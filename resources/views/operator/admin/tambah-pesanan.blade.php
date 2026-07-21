<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Tambah Pesanan Manual - {{ config('app.name', 'Zyngga') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;1,400;1,500&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Remix Icon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />

    <style>
        [x-cloak] { display: none !important; }
        
        body, input, select, textarea, button {
            font-family: 'DM Sans', sans-serif;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        select {
            background-image: none !important;
            -webkit-appearance: none;
            appearance: none;
        }
        select::-ms-expand { display: none; }
    </style>
</head>
<body class="antialiased h-full overflow-hidden" style="background:#E6F0FF; color:#0F0F0F;" x-data="{ sidebarOpen: false }">

    <!-- App Container -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR (Desktop + Mobile) -->
        @include('operator.partials.sidebar')

        <!-- MAIN WINDOW WRAPPER -->
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
            
            <!-- HEADER -->
            <header class="h-12 bg-white flex items-center justify-between px-4 sticky top-0 z-30 shrink-0">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-1 text-[#0F0F0F] hover:opacity-70 transition-opacity">
                        <i data-feather="menu" class="w-5 h-5"></i>
                    </button>
                    <a href="{{ route('admin.riwayat-pesanan') }}" class="flex items-center gap-1.5 text-xs font-medium text-[#0F0F0F] hover:text-[#003E9C] transition-colors">
                        <i class="ri-arrow-left-line text-base"></i>
                        <span>Kembali</span>
                    </a>
                </div>
                
                <div class="relative" x-data="{ profileOpen: false }">
                    <button @click="profileOpen = !profileOpen" type="button" class="flex items-center focus:outline-none cursor-pointer bg-transparent border-0 p-0">
                        <img src="/images/MyZyngga_avatar.png" alt="MyZyngga" class="w-6 h-6 rounded-full object-cover" style="border:0.5px solid #0F0F0F;">
                    </button>
                    
                    <div x-show="profileOpen" 
                         @click.outside="profileOpen = false" 
                         x-transition 
                         x-cloak 
                         class="absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg border border-slate-100 py-1 z-50">
                        <button type="button" 
                                onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();" 
                                class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors text-left bg-transparent border-0 cursor-pointer">
                            <i data-feather="log-out" class="w-4 h-4 text-rose-600"></i>
                            <span>Keluar Aplikasi</span>
                        </button>
                        <form id="logout-form-header" method="POST" action="{{ route('logout') }}" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </header>

            <!-- CONTENT INNER CONTAINER -->
            <div class="flex-1 overflow-y-auto px-5 py-4 custom-scrollbar" style="background:#E6F0FF;">
                
                <div class="max-w-xl mx-auto w-full flex flex-col gap-4">

                    <!-- ALERTS FOR ERRORS -->
                    @if(session('error'))
                        <div class="bg-rose-50 border border-rose-100 text-rose-700 text-xs font-medium px-4 py-3 rounded-xl flex items-center gap-2">
                            <i data-feather="alert-circle" class="w-4 h-4 shrink-0"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="bg-rose-50 border border-rose-100 text-rose-700 text-xs font-medium px-4 py-3 rounded-xl space-y-1">
                            <div class="flex items-center gap-2">
                                <i data-feather="alert-circle" class="w-4 h-4 shrink-0"></i>
                                <span>Terdapat kesalahan pengisian:</span>
                            </div>
                            <ul class="list-disc list-inside pl-6">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- FORM CARD CONTAINER -->
                    <form action="{{ route('admin.riwayat-pesanan.store') }}" method="POST" 
                          class="bg-white rounded-2xl p-6 shadow-sm space-y-4" 
                          x-data="{ pelangganOption: '{{ old('pelanggan_option', 'existing') }}' }">
                        @csrf

                        <!-- 1. PILIH TIPE PELANGGAN -->
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-[#0F0F0F] block text-center">Pilih Tipe Pelanggan</label>
                            <div class="flex p-1 bg-[#F4F4F4] rounded-2xl gap-1">
                                <button type="button" 
                                        @click="pelangganOption = 'existing'"
                                        :class="pelangganOption === 'existing' ? 'bg-white text-[#003E9C] border border-[#003E9C] font-medium shadow-sm' : 'text-[#808080] font-normal border-0 bg-transparent'"
                                        class="flex-1 py-3 text-xs rounded-xl transition-all text-center cursor-pointer">
                                    Pelanggan Terdaftar
                                </button>
                                <button type="button" 
                                        @click="pelangganOption = 'new'"
                                        :class="pelangganOption === 'new' ? 'bg-white text-[#003E9C] border border-[#003E9C] font-medium shadow-sm' : 'text-[#808080] font-normal border-0 bg-transparent'"
                                        class="flex-1 py-3 text-xs rounded-xl transition-all text-center cursor-pointer">
                                    Pelanggan Baru
                                </button>
                            </div>
                            <input type="hidden" name="pelanggan_option" :value="pelangganOption">
                        </div>

                        <!-- 2. PILIH AKUN PELANGGAN (EXISTING) -->
                        <div x-show="pelangganOption === 'existing'" x-transition class="space-y-1.5">
                            <label class="block text-xs font-medium text-[#0F0F0F]">Pilih Akun Pelanggan</label>
                            <div class="relative">
                                <select name="pelanggan_id" 
                                        class="w-full bg-white border rounded-full px-4 text-xs font-normal text-[#0F0F0F] focus:outline-none appearance-none" 
                                        style="border-color:#CCCCCC; height:48px;">
                                    <option value="" disabled selected>-- Cari / Pilih Pelanggan --</option>
                                    @foreach($pelangganList as $p)
                                        <option value="{{ $p->id }}" {{ old('pelanggan_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama }} ({{ $p->telepon }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-[#808080]">
                                    <i data-feather="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>

                        <!-- INPUT DATA PELANGGAN BARU (NEW) -->
                        <div x-show="pelangganOption === 'new'" x-transition class="space-y-3">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-medium text-[#0F0F0F]">Nama Pelanggan Baru</label>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Contoh: Rian" class="w-full bg-white border rounded-full px-4 text-xs font-normal text-[#0F0F0F] focus:outline-none" style="border-color:#CCCCCC; height:48px;">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-medium text-[#0F0F0F]">No. Telepon / WA</label>
                                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="Contoh: 0812XXXXXXXX" class="w-full bg-white border rounded-full px-4 text-xs font-normal text-[#0F0F0F] focus:outline-none" style="border-color:#CCCCCC; height:48px;">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-medium text-[#0F0F0F]">Alamat</label>
                                <input type="text" name="customer_address" value="{{ old('customer_address') }}" placeholder="Alamat rumah / jalan..." class="w-full bg-white border rounded-full px-4 text-xs font-normal text-[#0F0F0F] focus:outline-none" style="border-color:#CCCCCC; height:48px;">
                            </div>
                        </div>

                        <!-- 3. JENIS LAYANAN PRIORITAS -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium text-[#0F0F0F]">Jenis Layanan Prioritas</label>
                            <div class="relative">
                                <select name="layanan_prioritas_id" 
                                        class="w-full bg-white border rounded-full px-4 text-xs font-normal text-[#0F0F0F] focus:outline-none appearance-none" 
                                        style="border-color:#CCCCCC; height:48px;" 
                                        required>
                                    <option value="" disabled selected>-- Pilih Layanan --</option>
                                    @foreach($prioritasList as $l)
                                        <option value="{{ $l->id }}" {{ old('layanan_prioritas_id') == $l->id ? 'selected' : '' }}>
                                            {{ $l->nama }} (+ {{ $l->harga > 0 ? 'Rp ' . number_format($l->harga, 0, ',', '.') : 'Gratis' }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-[#808080]">
                                    <i data-feather="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>

                        <!-- 4. KARYAWAN PENANGGUNG JAWAB -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium text-[#0F0F0F]">Karyawan Penanggung Jawab</label>
                            <div class="relative">
                                <select name="pegawai_id" 
                                        class="w-full bg-white border rounded-full px-4 text-xs font-normal text-[#0F0F0F] focus:outline-none appearance-none" 
                                        style="border-color:#CCCCCC; height:48px;" 
                                        required>
                                    <option value="" disabled selected>-- Pilih Karyawan --</option>
                                    @foreach($pegawaiList as $w)
                                        <option value="{{ $w->id }}" {{ old('pegawai_id') == $w->id ? 'selected' : '' }}>
                                            {{ $w->name }} ({{ ucfirst($w->role) }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-[#808080]">
                                    <i data-feather="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>

                        <!-- 5. METODE PEMBAYARAN -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium text-[#0F0F0F]">Metode Pembayaran</label>
                            <div class="relative">
                                <select name="jenis_pembayaran" 
                                        class="w-full bg-white border rounded-full px-4 text-xs font-normal text-[#0F0F0F] focus:outline-none appearance-none" 
                                        style="border-color:#CCCCCC; height:48px;" 
                                        required>
                                    <option value="" disabled>-- Pilih Metode Pembayaran --</option>
                                    <option value="cash" {{ old('jenis_pembayaran') === 'cash' ? 'selected' : '' }}>Tunai / Cash</option>
                                    <option value="qris" {{ old('jenis_pembayaran', 'qris') === 'qris' ? 'selected' : '' }}>QRIS</option>
                                    <option value="transfer" {{ old('jenis_pembayaran') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                </select>
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-[#808080]">
                                    <i data-feather="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>

                        <!-- 6. STATUS PEMBAYARAN -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium text-[#0F0F0F]">Status Pembayaran</label>
                            <div class="relative">
                                <select name="payment_status" 
                                        class="w-full bg-white border rounded-full px-4 text-xs font-normal text-[#0F0F0F] focus:outline-none appearance-none" 
                                        style="border-color:#CCCCCC; height:48px;" 
                                        required>
                                    <option value="" disabled>-- Pilih Status Pembayaran --</option>
                                    <option value="pending" {{ old('payment_status') === 'pending' ? 'selected' : '' }}>Belum Lunas (Pending)</option>
                                    <option value="paid" {{ old('payment_status') === 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                                </select>
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-[#808080]">
                                    <i data-feather="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>

                        <!-- 7. PILIHAN PARFUM -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium text-[#0F0F0F]">Pilihan Parfum</label>
                            <div class="relative">
                                <select name="parfum" 
                                        class="w-full bg-white border rounded-full px-4 text-xs font-normal text-[#0F0F0F] focus:outline-none appearance-none" 
                                        style="border-color:#CCCCCC; height:48px;">
                                    <option value="" disabled>-- Pilih Parfum --</option>
                                    <option value="Standard" {{ old('parfum') === 'Standard' ? 'selected' : '' }}>Standard (Soft)</option>
                                    <option value="Fresh" {{ old('parfum') === 'Fresh' ? 'selected' : '' }}>Fresh (Floral)</option>
                                    <option value="Lavender" {{ old('parfum') === 'Lavender' ? 'selected' : '' }}>Lavender (Menenangkan)</option>
                                    <option value="None" {{ old('parfum') === 'None' ? 'selected' : '' }}>Tanpa Parfum</option>
                                </select>
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-[#808080]">
                                    <i data-feather="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>

                        <!-- 8. CHECKBOX ANTAR LAUNDRY (DARI GAMBAR 1) -->
                        <div class="flex items-center gap-2 py-1">
                            <input type="checkbox" 
                                   id="antar_laundry" 
                                   name="antar_laundry" 
                                   value="1" 
                                   {{ old('antar_laundry') ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#003E9C] border-[#CCCCCC] rounded focus:ring-blue-500 cursor-pointer">
                            <label for="antar_laundry" class="text-xs font-normal text-[#0F0F0F] cursor-pointer select-none">
                                Antar Laundry (Kirim pesanan ke alamat saat selesai)
                            </label>
                        </div>

                        <!-- 9. CATATAN KHUSUS -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium text-[#0F0F0F]">Catatan Khusus</label>
                            <textarea name="catatan" 
                                      placeholder="Catatan atau instruksi pakaian.." 
                                      class="w-full bg-white border rounded-2xl p-4 text-xs font-normal text-[#0F0F0F] focus:outline-none placeholder:text-[#808080]" 
                                      style="border-color:#CCCCCC; height:100px;">{{ old('catatan') }}</textarea>
                        </div>

                        <!-- 10. TOMBOL AKSI FORM -->
                        <div class="flex items-center justify-center gap-3 pt-3">
                            <a href="{{ route('admin.riwayat-pesanan') }}" 
                               class="flex-1 text-center font-medium text-xs rounded-full border transition-colors flex items-center justify-center cursor-pointer"
                               style="border-color:#003E9C; color:#003E9C; height:48px;">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="flex-1 text-center text-white font-medium text-xs rounded-full border-0 shadow-sm transition-colors flex items-center justify-center cursor-pointer"
                                    style="background:#003E9C; height:48px;">
                                Buat Pesanan
                            </button>
                        </div>
                    </form>

                </div>

            </div>

        </div>

    </div>

    <!-- CDNs and Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
</body>
</html>
