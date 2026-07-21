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
                    <a href="{{ route('admin.riwayat-pesanan', ['tab' => $tab]) }}" class="flex items-center gap-1.5 text-xs font-medium text-[#0F0F0F] hover:text-[#003E9C] transition-colors">
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

                    <!-- PAGE TITLE SECTION -->
                    <div>
                        <h1 class="text-base font-medium" style="color:#0F0F0F;">Proses Timbangan</h1>
                        <p class="text-xs font-normal" style="color:#808080;">{{ $transaksi->nota }}</p>
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

                    <!-- KARTU 1: DETAIL PESANAN -->
                    <div class="bg-white rounded-lg p-5 shadow-sm space-y-3">
                        <h3 class="text-sm font-medium" style="color:#0F0F0F;">Detail Pesanan</h3>

                        <div class="space-y-2.5 text-xs font-normal">
                            <div class="flex items-center justify-between">
                                <span style="color:#808080;">Nama Pelanggan</span>
                                <span class="font-medium" style="color:#0F0F0F;">{{ $transaksi->pelanggan->nama ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span style="color:#808080;">Nomor Telepon</span>
                                <span class="font-medium" style="color:#0F0F0F;">{{ $transaksi->pelanggan->telepon ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span style="color:#808080;">Jenis Layanan</span>
                                <span class="font-medium capitalize" style="color:#0F0F0F;">{{ $transaksi->layananPrioritas->nama ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span style="color:#808080;">Jenis Parfum</span>
                                <span class="font-medium" style="color:#0F0F0F;">{{ $transaksi->parfum ?? 'Standard' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span style="color:#808080;">Catatan</span>
                                <span class="font-medium" style="color:#0F0F0F;">{{ $transaksi->catatan ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span style="color:#808080;">Estimasi Awal</span>
                                <span class="font-medium" style="color:#003E9C;">Rp {{ number_format($transaksi->total_bayar_akhir, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- KARTU 2: INFORMASI TIMBANGAN -->
                    <div x-data="prosesTransaksiForm()" class="space-y-4">
                        <form action="{{ route('admin.riwayat-pesanan.proses', $transaksi->id) }}" method="POST" @submit.prevent="submitForm($event)" class="bg-white rounded-lg p-5 shadow-sm space-y-4">
                            @csrf
                            <input type="hidden" name="tab" value="{{ $tab }}">
                            @php
                                $isNewOrder = in_array($transaksi->status, ['Baru', 'created', 'Perlu Diproses', 'Menunggu di Jemput', 'Menunggu di jemput']);
                            @endphp

                            <h3 class="text-sm font-medium" style="color:#0F0F0F;">Informasi Timbangan</h3>

                            <fieldset @disabled(!$isNewOrder) class="space-y-4">

                                <!-- TIPE LAYANAN SWITCH (Pill Container) -->
                                @if($layananNama !== 'satuan')
                                    <div class="flex p-1 bg-[#D9D9D9] rounded-xl">
                                        <button type="button" 
                                                @click="tipeLayanan = 'kiloan'"
                                                :class="tipeLayanan === 'kiloan' ? 'bg-white text-[#003E9C] shadow-sm font-medium' : 'text-[#808080] font-normal'"
                                                class="flex-1 py-2 text-xs rounded-lg transition-all border-0 cursor-pointer">
                                            Kiloan (Timbangan)
                                        </button>
                                        <button type="button" 
                                                @click="tipeLayanan = 'satuan'"
                                                :class="tipeLayanan === 'satuan' ? 'bg-white text-[#003E9C] shadow-sm font-medium' : 'text-[#808080] font-normal'"
                                                class="flex-1 py-2 text-xs rounded-lg transition-all border-0 cursor-pointer">
                                            Satuan (Eceran)
                                        </button>
                                    </div>
                                @endif
                                <input type="hidden" name="tipe_layanan" :value="tipeLayanan">

                                <!-- ITEM SATUAN TAMBAHAN SECTION -->
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-medium" style="color:#0F0F0F;">Item Satuan Tambahan</span>
                                        <button type="button" @click="addSatuanItem()" class="text-xs font-medium text-[#003E9C] hover:underline bg-transparent border-0 cursor-pointer">
                                            + Tambah Item
                                        </button>
                                    </div>

                                    <div class="space-y-2.5">
                                        <template x-for="(item, index) in satuanItems" :key="index">
                                            <div class="flex items-center gap-2 bg-[#F8FAFC] p-2.5 rounded-xl border border-[#CCCCCC]">
                                                <!-- Dropdown Kategori -->
                                                <div class="flex-1 relative">
                                                    <select :name="'satuan_items[' + index + '][kategori_pakaian_satuan_id]'"
                                                            x-model="item.kategori_pakaian_satuan_id"
                                                            @change="updateItemPrice(item)"
                                                            class="w-full bg-white border rounded-lg px-3 text-xs text-[#0F0F0F] font-normal focus:outline-none appearance-none"
                                                            style="border-color:#CCCCCC; height:40px;"
                                                            required>
                                                        <option value="">-- Pilih Item Satuan --</option>
                                                        <template x-for="cat in satuanCategories" :key="cat.id">
                                                            <option :value="cat.id" x-text="cat.nama_pakaian + ' (' + formatRupiah(cat.harga) + ')'"></option>
                                                        </template>
                                                    </select>
                                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#808080]">
                                                        <i data-feather="chevron-down" class="w-3.5 h-3.5"></i>
                                                    </div>
                                                </div>

                                                <!-- Input Jumlah -->
                                                <div class="w-16">
                                                    <input type="number"
                                                           :name="'satuan_items[' + index + '][jumlah]'"
                                                           x-model.number="item.jumlah"
                                                           min="1"
                                                           class="w-full bg-white border rounded-lg text-center text-xs font-medium text-[#0F0F0F] focus:outline-none"
                                                           style="border-color:#CCCCCC; height:40px;"
                                                           required>
                                                </div>

                                                <!-- Total Harga Baris -->
                                                <div class="w-24 text-right">
                                                    <span class="text-xs font-medium text-[#0F0F0F]" x-text="formatRupiah(calculateItemTotal(item))">Rp0</span>
                                                </div>

                                                <!-- Button Hapus -->
                                                <button type="button" @click="removeSatuanItem(index)" class="p-1.5 text-[#EF4444] hover:bg-rose-50 rounded-lg transition-colors bg-transparent border-0 cursor-pointer">
                                                    <i class="ri-delete-bin-line text-base"></i>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="satuanItems.length === 0">
                                            <p class="text-xs font-normal text-[#808080] italic text-center py-1">Belum ada item satuan yang ditambahkan.</p>
                                        </template>
                                    </div>
                                </div>

                                <!-- BERAT TIMBANGAN SECTION -->
                                <div x-show="tipeLayanan === 'kiloan'" x-transition class="space-y-1">
                                    <label class="block text-xs font-normal text-[#808080]">Berat Timbangan</label>
                                    <div class="relative">
                                        <input type="number"
                                               name="actual_weight"
                                               x-model.number="actualWeight"
                                               step="0.01"
                                               min="0"
                                               placeholder="0"
                                               class="w-full bg-white border rounded-lg px-4 py-2.5 text-xs font-medium text-[#0F0F0F] focus:outline-none"
                                               style="border-color:#CCCCCC; height:48px;">
                                        <span class="absolute right-4 inset-y-0 flex items-center text-xs font-normal text-[#808080]">Kg</span>
                                    </div>
                                    <p class="text-[11px] font-normal text-[#808080] mt-1">Tarif aktif: Rp {{ number_format($defaultPrice, 0, ',', '.') }}/kg</p>
                                </div>

                                <!-- HIDDEN FIELDS UNTUK PRESERVASI BACKEND LOGIC -->
                                <input type="hidden" name="minimum_weight" x-model.number="minimumWeight">
                                <input type="hidden" name="price_per_kg" x-model.number="pricePerKg">
                                <template x-if="tipeLayanan === 'satuan'">
                                    <div>
                                        <input type="hidden" name="actual_weight" value="0">
                                        <input type="hidden" name="minimum_weight" value="0">
                                        <input type="hidden" name="price_per_kg" value="0">
                                    </div>
                                </template>

                                <!-- DYNAMIC SUMMARY BOX -->
                                <div class="bg-[#F4F4F4] rounded-xl p-4 space-y-2 text-xs">
                                    <div class="flex justify-between items-center text-[#808080] font-normal" x-show="tipeLayanan === 'kiloan'">
                                        <span>Biaya Kiloan</span>
                                        <span class="text-[#0F0F0F] font-medium" x-text="formatRupiah(chargedWeight * pricePerKg)">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[#808080] font-normal">
                                        <span>Biaya Satuan</span>
                                        <span class="text-[#0F0F0F] font-medium" x-text="formatRupiah(totalSatuanPrice)">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 border-t border-[#E2E8F0]">
                                        <span class="text-[#0F0F0F] font-medium">Total Biaya</span>
                                        <span class="text-[#003E9C] font-medium text-base" x-text="formatRupiah(totalPrice)">Rp 0</span>
                                    </div>
                                </div>

                            </fieldset>

                            <!-- TOMBOL AKSI FORM -->
                            <div class="flex items-center justify-center gap-3 pt-2">
                                <a href="{{ route('admin.riwayat-pesanan', ['tab' => $tab]) }}" 
                                   class="flex-1 text-center font-medium text-xs rounded-full border transition-colors flex items-center justify-center cursor-pointer"
                                   style="border-color:#003E9C; color:#003E9C; height:48px;">
                                    Batal
                                </a>
                                @if($isNewOrder)
                                    <button type="submit" 
                                            class="flex-1 text-center text-white font-medium text-xs rounded-full border-0 shadow-sm transition-colors flex items-center justify-center cursor-pointer"
                                            style="background:#003E9C; height:48px;">
                                        Buat Pesanan
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- KARTU 3: BUKTI TIMBANGAN -->
                    <div class="bg-white rounded-lg p-5 shadow-sm space-y-3">
                        <h3 class="text-sm font-medium flex items-center gap-2" style="color:#0F0F0F;">
                            <i class="ri-camera-line text-lg text-[#003E9C]"></i>
                            Bukti Timbangan
                        </h3>

                        @if($transaksi->bukti_timbangan)
                            <img src="{{ $transaksi->bukti_timbangan }}" alt="Bukti Timbangan" class="w-full rounded-lg border border-[#F4F4F4] object-cover max-h-48">
                        @endif

                        <form action="{{ route('admin.riwayat-pesanan.bukti-timbangan', $transaksi->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3" x-data="{ fileName: '' }">
                            @csrf
                            <div class="flex items-center gap-3">
                                <label class="px-5 py-2.5 rounded-full text-xs font-medium text-white cursor-pointer transition-colors shrink-0" style="background:#003E9C;">
                                    Pilih File
                                    <input type="file" name="bukti_timbangan" accept="image/*" class="hidden" @change="fileName = $event.target.files[0]?.name || ''" required>
                                </label>
                                <span class="text-xs font-normal text-[#808080] truncate" x-text="fileName ? fileName : 'Tidak ada file yang dipilih'">Tidak ada file yang dipilih</span>
                            </div>
                            <button type="submit" class="w-full text-white font-medium text-xs rounded-full transition-all border-0 cursor-pointer flex items-center justify-center" style="background:#003E9C; height:48px;">
                                Unggah Foto
                            </button>
                        </form>
                        <p class="text-[11px] font-normal text-[#808080]">Foto ini akan tampil di Galeri halaman detail pesanan pelanggan.</p>
                    </div>

                    <!-- UPGRADE SECTIONS (JIKA ADA PENDING ATAU TERSEDIA) -->
                    @if(isset($pendingUpgrade) && $pendingUpgrade)
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-5 shadow-sm space-y-3">
                            <h3 class="font-medium text-amber-800 text-sm flex items-center gap-2">
                                <i data-feather="bell" class="w-4 h-4 text-amber-600"></i>
                                Permintaan Upgrade Layanan
                            </h3>
                            <div class="text-xs space-y-2 font-normal text-amber-700">
                                <div>
                                    <p class="text-[10px] text-amber-600 uppercase">Layanan Baru</p>
                                    <p class="text-amber-900 font-medium capitalize">{{ $pendingUpgrade['target_service_name'] }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-amber-600 uppercase">Biaya Selisih (Tunai)</p>
                                    <p class="text-amber-900 font-medium text-sm">Rp {{ number_format($pendingUpgrade['price_diff'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <form action="{{ route('admin.riwayat-pesanan.konfirmasi-upgrade', $transaksi->id) }}" method="POST" class="pt-1">
                                @csrf
                                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white py-2.5 rounded-full text-xs font-medium shadow-sm transition-all border-0 cursor-pointer flex items-center justify-center gap-2">
                                    Konfirmasi Upgrade & Terima Cash
                                </button>
                            </form>
                        </div>
                    @endif

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
