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
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-outfit antialiased bg-[#f8fafc] text-[#1e293b] h-full" x-data="{ sidebarOpen: false }">

    <!-- App Container -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR (Desktop + Mobile) -->
        @include('operator.partials.sidebar')

        <!-- MAIN WINDOW WRAPPER -->
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
            
            <!-- HEADER -->
            <header class="h-16 bg-white border-b border-slate-100/90 flex items-center justify-between px-6 sticky top-0 z-30 shrink-0">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.riwayat-pesanan') }}" class="flex items-center gap-2 text-slate-500 hover:text-slate-800 text-sm font-semibold transition-all">
                        <i data-feather="arrow-left" class="w-4 h-4"></i>
                        Kembali ke Riwayat Pesanan
                    </a>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <img src="/images/MyZyngga_avatar.png" alt="MyZyngga" class="w-8 h-8 rounded-full border border-slate-100 object-cover">
                        <span class="text-sm font-bold text-[#0f172a]">MyZyngga</span>
                    </div>
                </div>
            </header>

            <!-- CONTENT INNER CONTAINER -->
            <div class="flex-1 overflow-y-auto px-6 py-8 custom-scrollbar">
                
                <div class="max-w-3xl mx-auto space-y-6">

                    <!-- Alerts for Errors -->
                    @if(session('error'))
                        <div class="bg-rose-50 border border-rose-100 text-rose-700 text-xs font-bold px-4 py-3 rounded-xl flex items-center gap-2 animate-none">
                            <i data-feather="alert-circle" class="w-4 h-4 stroke-[2.5]"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="bg-rose-50 border border-rose-100 text-rose-700 text-xs font-bold px-4 py-3 rounded-xl space-y-1">
                            <div class="flex items-center gap-2">
                                <i data-feather="alert-circle" class="w-4 h-4 stroke-[2.5]"></i>
                                <span>Terdapat kesalahan pengisian:</span>
                            </div>
                            <ul class="list-disc list-inside pl-6 font-semibold">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="border-b border-slate-100 pb-4">
                        <h1 class="text-2xl font-extrabold text-[#0f172a] tracking-tight">Tambah Pesanan Baru</h1>
                        <p class="text-xs font-semibold text-slate-400 mt-1">Buat pesanan manual untuk pelanggan yang langsung datang ke laundry (walk-in).</p>
                    </div>

                    <form action="{{ route('admin.riwayat-pesanan.store') }}" method="POST" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 space-y-6" x-data="{ pelangganOption: 'existing' }">
                        @csrf

                        <!-- Pelanggan Option selection -->
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Pilih Tipe Pelanggan</label>
                            <div class="flex flex-col sm:flex-row p-1 bg-slate-50 border border-slate-100 rounded-xl gap-1">
                                <button type="button" 
                                        @click="pelangganOption = 'existing'"
                                        :class="pelangganOption === 'existing' ? 'bg-white text-blue-600 shadow-sm border border-slate-100' : 'text-slate-500 hover:text-slate-700'"
                                        class="flex-1 py-2 text-xs font-bold rounded-lg transition-all text-center">
                                    Pelanggan Terdaftar
                                </button>
                                <button type="button" 
                                        @click="pelangganOption = 'new'"
                                        :class="pelangganOption === 'new' ? 'bg-white text-blue-600 shadow-sm border border-slate-100' : 'text-slate-500 hover:text-slate-700'"
                                        class="flex-1 py-2 text-xs font-bold rounded-lg transition-all text-center">
                                    Pelanggan Baru
                                </button>
                            </div>
                            <input type="hidden" name="pelanggan_option" :value="pelangganOption">
                        </div>

                        <!-- Existing Customer dropdown -->
                        <div x-show="pelangganOption === 'existing'" x-transition class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pilih Akun Pelanggan</label>
                            <select name="pelanggan_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all">
                                <option value="">-- Cari / Pilih Pelanggan --</option>
                                @foreach($pelangganList as $p)
                                    <option value="{{ $p->id }}" {{ old('pelanggan_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }} ({{ $p->telepon }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- New Customer inputs -->
                        <div x-show="pelangganOption === 'new'" x-transition class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Pelanggan Baru</label>
                                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Contoh: Rian" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">No. Telepon / WA</label>
                                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="Contoh: 0812XXXXXXXX" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all">
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Alamat</label>
                                <input type="text" name="customer_address" value="{{ old('customer_address') }}" placeholder="Alamat rumah / jalan..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all">
                            </div>
                        </div>

                        <!-- Service and Worker Details -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jenis Layanan Prioritas</label>
                                <select name="layanan_prioritas_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all" required>
                                    <option value="">-- Pilih Layanan --</option>
                                    @foreach($prioritasList as $l)
                                        <option value="{{ $l->id }}" {{ old('layanan_prioritas_id') == $l->id ? 'selected' : '' }}>
                                            {{ $l->nama }} (+ {{ $l->harga > 0 ? 'Rp ' . number_format($l->harga, 0, ',', '.') : 'Gratis' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Karyawan Penanggung Jawab</label>
                                <select name="pegawai_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach($pegawaiList as $w)
                                        <option value="{{ $w->id }}" {{ old('pegawai_id') == $w->id ? 'selected' : '' }}>
                                            {{ $w->name }} ({{ ucfirst($w->role) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Payment configuration -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-slate-50 pt-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Metode Pembayaran</label>
                                <select name="jenis_pembayaran" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all" required>
                                    <option value="cash" {{ old('jenis_pembayaran') === 'cash' ? 'selected' : '' }}>Tunai / Cash</option>
                                    <option value="qris" {{ old('jenis_pembayaran', 'qris') === 'qris' ? 'selected' : '' }}>QRIS</option>
                                    <option value="transfer" {{ old('jenis_pembayaran') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Status Pembayaran</label>
                                <select name="payment_status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all" required>
                                    <option value="pending" {{ old('payment_status') === 'pending' ? 'selected' : '' }}>Belum Lunas (Pending)</option>
                                    <option value="paid" {{ old('payment_status') === 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Addons inputs -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-slate-50 pt-4">
                            <div class="space-y-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pilihan Parfum</label>
                                    <select name="parfum" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all">
                                        <option value="Standard" {{ old('parfum') === 'Standard' ? 'selected' : '' }}>Standard (Soft)</option>
                                        <option value="Fresh" {{ old('parfum') === 'Fresh' ? 'selected' : '' }}>Fresh (Floral)</option>
                                        <option value="Lavender" {{ old('parfum') === 'Lavender' ? 'selected' : '' }}>Lavender (Menenangkan)</option>
                                        <option value="None" {{ old('parfum') === 'None' ? 'selected' : '' }}>Tanpa Parfum</option>
                                    </select>
                                </div>

                                <div class="flex items-center gap-2.5">
                                    <input type="checkbox" 
                                           id="antar_laundry" 
                                           name="antar_laundry" 
                                           value="1" 
                                           {{ old('antar_laundry') ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 cursor-pointer">
                                    <label for="antar_laundry" class="text-xs font-bold text-slate-700 cursor-pointer select-none">
                                        Antar Laundry (Kirim pesanan ke alamat saat selesai)
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catatan Khusus</label>
                                <textarea name="catatan" placeholder="Catatan atau instruksi pakaian..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all h-20">{{ old('catatan') }}</textarea>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-50">
                            <a href="{{ route('admin.riwayat-pesanan') }}" class="text-center border border-slate-200 hover:bg-slate-50 text-slate-700 px-5 py-2.5 rounded-xl text-xs font-bold transition-all">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                                <i data-feather="check" class="w-4 h-4"></i>
                                Buat Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Initialize Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
</body>
</html>
