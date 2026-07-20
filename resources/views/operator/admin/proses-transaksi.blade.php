<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Proses Pesanan - {{ config('app.name', 'Zyngga') }}</title>

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

    <style>
        [x-cloak] { display: none !important; }
        .transition-transform-hover:hover {
            transform: translateY(-2px);
        }
        .form-shadow {
            box-shadow: 0 4px 20px -2px rgba(148, 163, 184, 0.08), 0 2px 8px -1px rgba(148, 163, 184, 0.04);
        }
        .active-ring:focus-within {
            ring-color: #3b82f6;
            border-color: #3b82f6;
        }
    </style>
</head>
<body class="font-dm-sans antialiased bg-[#f8fafc] text-[#1e293b] h-full" x-data="{ sidebarOpen: false }">

    <!-- App Container -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR (Desktop + Mobile) -->
        @include('operator.partials.sidebar')

        <!-- MAIN WINDOW WRAPPER -->
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
            
            <!-- HEADER -->
            <header class="h-16 bg-white border-b border-slate-100/90 flex items-center justify-between px-6 sticky top-0 z-30 shrink-0">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.riwayat-pesanan', ['tab' => $tab]) }}" class="flex items-center gap-2 text-slate-500 hover:text-slate-800 text-sm font-medium transition-all">
                        <i data-feather="arrow-left" class="w-4 h-4"></i>
                        Kembali ke Riwayat Pesanan
                    </a>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <img src="/images/MyZyngga_avatar.png" alt="MyZyngga" class="w-8 h-8 rounded-full border border-slate-100 object-cover">
                    </div>
                </div>
            </header>

            <!-- CONTENT INNER CONTAINER -->
            <div class="flex-1 overflow-y-auto px-6 py-8 custom-scrollbar">
                
                <div class="max-w-5xl mx-auto space-y-6">

                    <!-- Alerts for Errors -->
                    @if(session('error'))
                        <div class="bg-rose-50 border border-rose-100 text-rose-700 text-xs font-medium px-4 py-3 rounded-xl flex items-center gap-2">
                            <i data-feather="alert-circle" class="w-4 h-4 stroke-[2.5]"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="bg-rose-50 border border-rose-100 text-rose-700 text-xs font-medium px-4 py-3 rounded-xl space-y-1">
                            <div class="flex items-center gap-2">
                                <i data-feather="alert-circle" class="w-4 h-4 stroke-[2.5]"></i>
                                <span>Terdapat kesalahan pengisian:</span>
                            </div>
                            <ul class="list-disc list-inside pl-6 font-medium">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-4">
                        <div>
                            <h1 class="text-2xl font-medium text-[#0f172a] tracking-tight">Proses Timbangan & Item Pesanan</h1>
                            <p class="text-xs font-medium text-slate-400 mt-1">Masukkan list pakaian dan lakukan timbangan untuk Nota <span class="font-mono text-blue-600">{{ $transaksi->nota }}</span></p>
                        </div>
                    </div>

                    @php
                        $layananNama = strtolower($transaksi->layananPrioritas->nama ?? 'reguler');
                        $defaultPrice = match($layananNama) {
                            'quick' => 6000,
                            'express' => 6250,
                            'kilat' => 7850,
                            'satuan' => 10000,
                            default => 4850,
                        };

                        $existingTimbangan = $transaksi->timbangan;
                        $existingItems = collect();
                        if ($transaksi->fk_tambahan) {
                            $existingItems = \Illuminate\Support\Facades\DB::table('tambahan')
                                ->where('tambahan_id', $transaksi->fk_tambahan)
                                ->get();
                        }
                    @endphp

                    <!-- Grid Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="prosesTransaksiForm()">
                        
                        <!-- Left Side: Order Details -->
                        <div class="md:col-span-1 space-y-6">
                            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 space-y-4">
                                <h3 class="font-medium text-[#0f172a] text-sm border-b border-slate-50 pb-2 flex items-center gap-2">
                                    <i data-feather="file-text" class="w-4 h-4 text-blue-500"></i>
                                    Detail Pesanan
                                </h3>
                                <div class="text-xs space-y-3 font-medium text-slate-500">
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase">Nota</p>
                                        <p class="text-[#0f172a] font-mono text-sm mt-0.5">{{ $transaksi->nota }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase">Pelanggan</p>
                                        <p class="text-[#0f172a] text-sm mt-0.5">{{ $transaksi->pelanggan->nama ?? '-' }}</p>
                                        <p class="text-slate-400 font-normal mt-0.5">{{ $transaksi->pelanggan->telepon ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase">Layanan</p>
                                        <p class="text-[#0f172a] mt-0.5 capitalize">{{ $transaksi->layananPrioritas->nama ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase">Parfum</p>
                                        <p class="text-[#0f172a] mt-0.5">{{ $transaksi->parfum ?? 'Standard' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase">Catatan</p>
                                        <p class="text-[#0f172a] font-normal leading-relaxed mt-0.5">{{ $transaksi->catatan ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 uppercase">Estimasi Awal</p>
                                        <p class="text-blue-600 font-medium text-sm mt-0.5">Rp {{ number_format($transaksi->total_bayar_akhir, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- BUKTI TIMBANGAN -->
                            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 space-y-4">
                                <h3 class="font-medium text-[#0f172a] text-sm border-b border-slate-50 pb-2 flex items-center gap-2">
                                    <i data-feather="camera" class="w-4 h-4 text-blue-500"></i>
                                    Bukti Timbangan
                                </h3>
                                @if($transaksi->bukti_timbangan)
                                    <img src="{{ $transaksi->bukti_timbangan }}" alt="Bukti Timbangan" class="w-full rounded-xl border border-slate-100 object-cover">
                                @endif
                                <form action="{{ route('admin.riwayat-pesanan.bukti-timbangan', $transaksi->id) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                                    @csrf
                                    <input type="file" name="bukti_timbangan" accept="image/*" required
                                        class="w-full text-xs font-medium text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                                    <button type="submit" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-xs font-medium transition-all">
                                        {{ $transaksi->bukti_timbangan ? 'Ganti Foto' : 'Unggah Foto' }}
                                    </button>
                                </form>
                                <p class="text-[10px] text-slate-400">Foto ini akan tampil di Galeri halaman detail pesanan pelanggan.</p>
                            </div>

                            <!-- UPGRADE LAYANAN SECTIONS -->
                            
                            {{-- 1. Pending Upgrade dari Pelanggan --}}
                            @if(isset($pendingUpgrade) && $pendingUpgrade)
                            <div class="bg-amber-50/50 border border-amber-200/60 rounded-2xl shadow-sm p-6 space-y-4">
                                <h3 class="font-medium text-amber-800 text-sm flex items-center gap-2">
                                    <i data-feather="bell" class="w-4 h-4 animate-bounce text-amber-600"></i>
                                    Permintaan Upgrade Layanan
                                </h3>
                                <div class="text-xs space-y-3 font-medium text-amber-700">
                                    <div>
                                        <p class="text-[10px] text-amber-600/70 uppercase">Layanan Baru</p>
                                        <p class="text-amber-900 text-sm mt-0.5 capitalize">{{ $pendingUpgrade['target_service_name'] }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-amber-600/70 uppercase">Biaya Selisih (Tunai)</p>
                                        <p class="text-amber-900 text-base font-medium mt-0.5">Rp {{ number_format($pendingUpgrade['price_diff'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <form action="{{ route('admin.riwayat-pesanan.konfirmasi-upgrade', $transaksi->id) }}" method="POST" class="pt-2">
                                    @csrf
                                    <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white py-2.5 rounded-xl text-xs font-medium shadow-sm transition-all flex items-center justify-center gap-2">
                                        <i data-feather="check-circle" class="w-4 h-4"></i>
                                        Konfirmasi Upgrade & Terima Cash
                                    </button>
                                </form>
                            </div>
                            @endif

                            {{-- 2. Inisiasi Upgrade Langsung oleh Operator --}}
                            @if($transaksi->canBeUpgraded() && $availableUpgrades->isNotEmpty())
                            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 space-y-4">
                                <h3 class="font-medium text-[#0f172a] text-sm border-b border-slate-50 pb-2 flex items-center gap-2">
                                    <i data-feather="arrow-up-circle" class="w-4 h-4 text-emerald-500"></i>
                                    Upgrade Layanan Langsung
                                </h3>
                                <form action="{{ route('admin.riwayat-pesanan.inisiasi-upgrade', $transaksi->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label for="new_service_id" class="text-[10px] font-medium text-slate-400 uppercase tracking-wider block mb-2">Pilih Layanan Lebih Tinggi</label>
                                        <select name="new_service_id" id="new_service_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                                            <option value="">-- Pilih Layanan --</option>
                                            @foreach($availableUpgrades as $upgrade)
                                                <option value="{{ $upgrade->id }}">
                                                    {{ $upgrade->nama }} (+Rp {{ number_format($upgrade->harga - ($transaksi->layananPrioritas->harga ?? 0), 0, ',', '.') }}/kg)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-xs font-medium shadow-sm transition-all flex items-center justify-center gap-2">
                                        <i data-feather="zap" class="w-4 h-4"></i>
                                        Proses Upgrade Tunai
                                    </button>
                                </form>
                            </div>
                            @endif

                            {{-- 3. Riwayat Upgrade Layanan --}}
                            @if(isset($upgradeHistory) && $upgradeHistory->isNotEmpty())
                            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 space-y-4">
                                <h3 class="font-medium text-[#0f172a] text-sm border-b border-slate-50 pb-2 flex items-center gap-2">
                                    <i data-feather="clock" class="w-4 h-4 text-slate-500"></i>
                                    Riwayat Upgrade Layanan
                                </h3>
                                <div class="space-y-3">
                                    @foreach($upgradeHistory as $history)
                                    <div class="flex items-start justify-between text-xs border-b border-slate-50 pb-3 last:border-0 last:pb-0">
                                        <div>
                                            <p class="font-medium text-[#0f172a] capitalize">
                                                {{ $history->layananAsal->nama ?? 'Reguler' }} <span class="text-slate-400 font-normal">→</span> {{ $history->layananTujuan->nama ?? 'Express' }}
                                            </p>
                                            <p class="text-slate-400 font-normal mt-0.5 text-[10px]">{{ $history->created_at->format('d M Y | H:i') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-slate-800">Rp {{ number_format($history->biaya_upgrade, 0, ',', '.') }}</p>
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded-md text-[9px] font-medium uppercase tracking-wider {{ $history->metode_bayar === 'Tunai' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                                                {{ $history->metode_bayar ?? 'Cashless' }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Right Side: Items and Weights Form -->
                        <div class="md:col-span-2 space-y-6">
                            <form action="{{ route('admin.riwayat-pesanan.proses', $transaksi->id) }}" method="POST" @submit.prevent="submitForm($event)" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 space-y-6">
                                @csrf
                                <input type="hidden" name="tab" value="{{ $tab }}">
                                @php
                                    $isNewOrder = in_array($transaksi->status, ['Baru', 'created', 'Perlu Diproses', 'Menunggu di Jemput', 'Menunggu di jemput']);
                                @endphp
                                
                                @if(!$isNewOrder)
                                    <!-- Read-only Info Badge -->
                                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-xs text-slate-500 font-medium leading-relaxed flex items-start gap-2">
                                        <i data-feather="info" class="w-4 h-4 text-blue-500 shrink-0 mt-0.5"></i>
                                        <div>
                                            Pesanan ini sudah diproses timbangannya dan saat ini dalam tahap <strong>{{ $transaksi->status }}</strong>. Anda dapat melihat rincian di bawah atau mengelola upgrade layanan di kolom kiri.
                                        </div>
                                    </div>
                                @endif
                                
                                <fieldset @disabled(!$isNewOrder) class="space-y-6">

                                <!-- Service Type Selection Tabs -->
                                @if($layananNama !== 'satuan')
                                <div>
                                    <label class="text-[10px] font-medium text-slate-400 uppercase tracking-wider block mb-2">Tipe Layanan Proses</label>
                                    <div class="flex p-1 bg-slate-50 border border-slate-100 rounded-xl">
                                        <button type="button" 
                                                @click="tipeLayanan = 'kiloan'"
                                                :class="tipeLayanan === 'kiloan' ? 'bg-white text-blue-600 shadow-sm border border-slate-100' : 'text-slate-500 hover:text-slate-700'"
                                                class="flex-1 py-2 text-xs font-medium rounded-lg transition-all">
                                            Kiloan (Timbangan)
                                        </button>
                                        <button type="button" 
                                                @click="tipeLayanan = 'satuan'"
                                                :class="tipeLayanan === 'satuan' ? 'bg-white text-blue-600 shadow-sm border border-slate-100' : 'text-slate-500 hover:text-slate-700'"
                                                class="flex-1 py-2 text-xs font-medium rounded-lg transition-all">
                                            Satuan (Eceran)
                                        </button>
                                    </div>
                                </div>
                                @endif
                                <input type="hidden" name="tipe_layanan" :value="tipeLayanan">

                                <!-- Satuan Items Section -->
                                <div class="space-y-4 border-t border-slate-50 pt-4">
                                    <div class="flex justify-between items-center pb-2">
                                        <h3 class="font-medium text-[#0f172a] text-sm flex items-center gap-2">
                                            <i data-feather="grid" class="w-4 h-4 text-blue-500"></i>
                                            Item Satuan Tambahan
                                        </h3>
                                        <button type="button" @click="addSatuanItem()" class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1 transition-all">
                                            <i data-feather="plus" class="w-3.5 h-3.5"></i>
                                            Tambah Item Satuan
                                        </button>
                                    </div>

                                    <!-- Alpine Loop for Satuan Items -->
                                    <div class="space-y-3">
                                        <template x-for="(item, index) in satuanItems" :key="index">
                                            <div class="flex items-center gap-3 bg-slate-50/70 p-3 rounded-xl border border-slate-100 relative group transition-all">
                                                <div class="flex-1">
                                                    <label class="text-[9px] font-medium text-slate-400 uppercase tracking-wider block mb-1">Kategori Pakaian Satuan</label>
                                                    <select :name="'satuan_items[' + index + '][kategori_pakaian_satuan_id]'"
                                                            x-model="item.kategori_pakaian_satuan_id"
                                                            @change="updateItemPrice(item)"
                                                            class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-xs font-medium text-slate-700 outline-none focus:border-blue-500 transition-all"
                                                            required>
                                                        <option value="">-- Pilih Item Satuan --</option>
                                                        <template x-for="cat in satuanCategories" :key="cat.id">
                                                            <option :value="cat.id" x-text="cat.nama_pakaian + ' (' + formatRupiah(cat.harga) + ')'"></option>
                                                        </template>
                                                    </select>
                                                </div>

                                                <div class="w-20">
                                                    <label class="text-[9px] font-medium text-slate-400 uppercase tracking-wider block mb-1 text-center">Jumlah</label>
                                                    <input type="number"
                                                           :name="'satuan_items[' + index + '][jumlah]'"
                                                           x-model.number="item.jumlah"
                                                           min="1"
                                                           class="w-full bg-white border border-slate-200 rounded-lg py-1.5 text-center text-xs font-medium text-slate-700 outline-none focus:border-blue-500 transition-all"
                                                           required>
                                                </div>

                                                <div class="w-28 text-right pr-2">
                                                    <span class="text-[9px] font-medium text-slate-400 uppercase tracking-wider block mb-1">Harga Akhir</span>
                                                    <span class="text-xs font-medium text-slate-700 block mt-1.5" x-text="formatRupiah(calculateItemTotal(item))">Rp0</span>
                                                </div>

                                                <div class="self-end pb-0.5">
                                                    <button type="button" @click="removeSatuanItem(index)"
                                                            class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-all">
                                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="satuanItems.length === 0">
                                            <p class="text-xs font-medium text-slate-400 italic text-center py-2">Belum ada item satuan yang ditambahkan.</p>
                                        </template>
                                    </div>
                                </div>

                                <!-- Weight Scale and Price calculations -->
                                <div class="space-y-4 border-t border-slate-50 pt-4" x-show="tipeLayanan === 'kiloan'" x-transition>
                                    <h3 class="font-medium text-[#0f172a] text-sm flex items-center gap-2">
                                        <i data-feather="activity" class="w-4 h-4 text-blue-500"></i>
                                        Timbangan & Tarif
                                    </h3>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <!-- Actual Weight input -->
                                        <div>
                                            <label class="text-[10px] font-medium text-slate-400 uppercase tracking-wider block mb-1.5">Berat Timbangan (Kg)</label>
                                            <div class="relative">
                                                <input type="number"
                                                        name="actual_weight"
                                                        x-model.number="actualWeight"
                                                        step="0.01"
                                                        min="0"
                                                        placeholder="0.00"
                                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3 pr-10 py-2.5 text-xs font-medium text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all">
                                                <span class="absolute right-3 inset-y-0 flex items-center text-xs font-medium text-slate-400">kg</span>
                                            </div>
                                        </div>

                                        <!-- Minimum Weight configuration -->
                                        <div>
                                            <label class="text-[10px] font-medium text-slate-400 uppercase tracking-wider block mb-1.5">Minimal Berat (Kg)</label>
                                            <div class="relative">
                                                <input type="number"
                                                       name="minimum_weight"
                                                       x-model.number="minimumWeight"
                                                       step="0.1"
                                                       min="0"
                                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3 pr-10 py-2.5 text-xs font-medium text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all">
                                                <span class="absolute right-3 inset-y-0 flex items-center text-xs font-medium text-slate-400">kg</span>
                                            </div>
                                        </div>

                                        <!-- Price per Kg -->
                                        <div>
                                            <label class="text-[10px] font-medium text-slate-400 uppercase tracking-wider block mb-1.5">Harga per Kg (Rp)</label>
                                            <div class="relative">
                                                <span class="absolute left-3 inset-y-0 flex items-center text-xs font-medium text-slate-400">Rp</span>
                                                <input type="number"
                                                       name="price_per_kg"
                                                       x-model.number="pricePerKg"
                                                       min="0"
                                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2.5 text-xs font-medium text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden fields for Satuan mode validation -->
                                <template x-if="tipeLayanan === 'satuan'">
                                    <div>
                                        <input type="hidden" name="actual_weight" value="0">
                                        <input type="hidden" name="minimum_weight" value="0">
                                        <input type="hidden" name="price_per_kg" value="0">
                                    </div>
                                </template>

                                <!-- Dynamic calculations results card -->
                                <div class="bg-blue-50/50 rounded-2xl border border-blue-100/30 p-5 space-y-3 font-medium text-xs">
                                    <div class="flex justify-between items-center text-slate-500" x-show="tipeLayanan === 'kiloan'">
                                        <span>Formula Berat Ditagih:</span>
                                        <span class="font-normal">max(Minimal, Timbangan)</span>
                                    </div>
                                    <div class="flex justify-between items-center text-slate-500" x-show="tipeLayanan === 'kiloan'">
                                        <span>Berat yang Ditagih:</span>
                                        <span class="text-slate-800 font-medium"><span x-text="chargedWeight.toFixed(2)">0.00</span> kg</span>
                                    </div>
                                    <div class="flex justify-between items-center text-slate-500" x-show="tipeLayanan === 'kiloan'">
                                        <span>Biaya Kiloan:</span>
                                        <span class="text-slate-800 font-medium" x-text="formatRupiah(chargedWeight * pricePerKg)">Rp0</span>
                                    </div>
                                    <div class="flex justify-between items-center text-slate-500">
                                        <span>Selisih Harga Layanan Prioritas:</span>
                                        <span class="text-blue-600 font-medium">+ <span x-text="formatRupiah(priorityCharge)">Rp0</span> per item satuan</span>
                                    </div>
                                    <div class="flex justify-between items-center text-slate-500">
                                        <span>Total Biaya Satuan:</span>
                                        <span class="text-slate-800 font-medium" x-text="formatRupiah(totalSatuanPrice)">Rp0</span>
                                    </div>
                                    <div class="flex justify-between items-center border-t border-blue-100/50 pt-3 text-sm">
                                        <span class="text-slate-800 font-medium">Total Biaya Layanan Akhir:</span>
                                        <span class="text-blue-600 font-medium text-lg" x-text="formatRupiah(totalPrice)">Rp0</span>
                                    </div>
                                </div>

                                </fieldset>
 
                                <!-- Submission Buttons -->
                                <div class="flex justify-end gap-3 pt-2">
                                    <a href="{{ route('admin.riwayat-pesanan') }}" class="text-center border border-slate-200 hover:bg-slate-50 text-slate-700 px-5 py-2.5 rounded-xl text-xs font-medium transition-all">
                                        {{ $isNewOrder ? 'Batal' : 'Kembali' }}
                                    </a>
                                    @if($isNewOrder)
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-xs font-medium shadow-sm transition-all flex items-center gap-1.5">
                                            <i data-feather="check" class="w-4 h-4"></i>
                                            Selesai & Proses Pesanan
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Initialize Icons & Alpine Component -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('prosesTransaksiForm', () => ({
                tipeLayanan: '{{ $layananNama === 'satuan' || $transaksi->fk_tambahan !== null ? 'satuan' : 'kiloan' }}',
                satuanCategories: @json($satuanItemsAvailable),
                priorityCharge: {{ $transaksi->layananPrioritas->harga ?? 0 }},
                satuanItems: @json($existingItems->map(function($item) {
                    $cat = \App\Models\KategoriPakaianSatuan::find($item->kategori_pakaian_satuan_id);
                    return [
                        'kategori_pakaian_satuan_id' => $item->kategori_pakaian_satuan_id,
                        'jumlah' => $item->jumlah,
                        'basePrice' => $cat ? $cat->harga : 0
                    ];
                })),
                actualWeight: {{ $existingTimbangan ? $existingTimbangan->actual_weight : (old('actual_weight', '') !== '' ? old('actual_weight') : '0') }},
                minimumWeight: {{ $existingTimbangan ? $existingTimbangan->minimum_weight : old('minimum_weight', '3.0') }},
                pricePerKg: {{ $existingTimbangan ? $existingTimbangan->price_per_kg : old('price_per_kg', $defaultPrice) }},
                originalTotal: {{ $transaksi->total_bayar_akhir }},
                
                addSatuanItem() {
                    this.satuanItems.push({ kategori_pakaian_satuan_id: '', jumlah: 1, basePrice: 0 });
                    setTimeout(() => {
                        if (typeof feather !== 'undefined') feather.replace();
                    }, 50);
                },
                removeSatuanItem(index) {
                    this.satuanItems.splice(index, 1);
                },
                updateItemPrice(item) {
                    const cat = this.satuanCategories.find(c => c.id == item.kategori_pakaian_satuan_id);
                    item.basePrice = cat ? parseFloat(cat.harga) : 0;
                },
                calculateItemTotal(item) {
                    return (item.basePrice + this.priorityCharge) * (item.jumlah || 1);
                },
                get totalSatuanPrice() {
                    return this.satuanItems.reduce((sum, item) => sum + this.calculateItemTotal(item), 0);
                },
                get chargedWeight() {
                    if (!this.actualWeight || this.actualWeight <= 0) return 0;
                    return Math.max(this.minimumWeight, this.actualWeight);
                },
                get totalPrice() {
                    let price = this.totalSatuanPrice;
                    if (this.tipeLayanan === 'kiloan') {
                        price += Math.round(this.chargedWeight * this.pricePerKg);
                    }
                    return price;
                },
                formatRupiah(value) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
                },
                submitForm(e) {
                    const hasWeight = this.actualWeight && parseFloat(this.actualWeight) > 0;
                    const hasSatuan = this.satuanItems.length > 0 && this.satuanItems.some(item => item.kategori_pakaian_satuan_id !== '');
                    
                    if (!hasWeight && !hasSatuan) {
                        alert('Silakan isi berat timbangan kiloan ATAU masukkan minimal satu item satuan tambahan.');
                        return;
                    }
                    e.target.submit();
                }
            }));
        });
    </script>
</body>
</html>
