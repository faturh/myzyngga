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
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

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
<body class="font-outfit antialiased bg-[#f8fafc] text-[#1e293b] h-full" x-data="{ sidebarOpen: false }">

    <!-- App Container -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR LEFT (DESKTOP) -->
        <aside class="hidden lg:flex lg:flex-col lg:w-64 bg-white border-r border-slate-100/90 h-full shrink-0">
            <!-- Store Profile Head -->
            <div class="p-6 border-b border-slate-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <img src="/images/MyZyngga_avatar.png" alt="MyZyngga" class="w-10 h-10 rounded-full border border-slate-100 object-cover shadow-sm">
                    <div>
                        <h2 class="font-bold text-[#0f172a] text-sm leading-tight">MyZyngga</h2>
                        <span class="text-[11px] text-slate-400 font-medium">Laundry Operator</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 py-6 space-y-7">
                <div>
                    <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <i data-feather="home" class="w-3.5 h-3.5"></i>
                        <span>Tokoku</span>
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                Beranda
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <div class="flex items-center gap-2 px-3 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <i data-feather="shopping-cart" class="w-3.5 h-3.5"></i>
                        <span>Pesanan</span>
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.riwayat-pesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold bg-blue-50/70 text-blue-600 border border-blue-100/20 transition-all">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Riwayat Pesanan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                <div class="text-[11px] text-slate-400 text-center font-medium">
                    &copy; 2026 Zyngga Laundry.
                </div>
            </div>
        </aside>

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
                
                <div class="max-w-5xl mx-auto space-y-6">

                    <!-- Alerts for Errors -->
                    @if(session('error'))
                        <div class="bg-rose-50 border border-rose-100 text-rose-700 text-xs font-bold px-4 py-3 rounded-xl flex items-center gap-2">
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

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-4">
                        <div>
                            <h1 class="text-2xl font-extrabold text-[#0f172a] tracking-tight">Proses Timbangan & Item Pesanan</h1>
                            <p class="text-xs font-semibold text-slate-400 mt-1">Masukkan list pakaian dan lakukan timbangan untuk Nota <span class="font-mono text-blue-600">{{ $transaksi->nota }}</span></p>
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

                        $oldItems = old('items');
                        $initialItems = [];
                        if (!empty($oldItems) && is_array($oldItems)) {
                            foreach ($oldItems as $oldItem) {
                                if (empty($oldItem['nama_item'])) continue;
                                $isPredefined = $itemsAvailable->contains('nama', $oldItem['nama_item']);
                                $initialItems[] = [
                                    'nama_item' => $oldItem['nama_item'],
                                    'qty' => (int) ($oldItem['qty'] ?? 1),
                                    'checked' => true,
                                    'predefined' => $isPredefined,
                                ];
                            }
                            foreach ($itemsAvailable as $item) {
                                $alreadyIncluded = collect($initialItems)->contains('nama_item', $item->nama);
                                if (!$alreadyIncluded) {
                                    $initialItems[] = [
                                        'nama_item' => $item->nama,
                                        'qty' => 1,
                                        'checked' => false,
                                        'predefined' => true,
                                    ];
                                }
                            }
                        } else {
                            foreach ($itemsAvailable as $item) {
                                $initialItems[] = [
                                    'nama_item' => $item->nama,
                                    'qty' => 1,
                                    'checked' => false,
                                    'predefined' => true,
                                ];
                            }
                        }
                    @endphp

                    <!-- Grid Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="prosesTransaksiForm()">
                        
                        <!-- Left Side: Order Details -->
                        <div class="md:col-span-1 space-y-6">
                            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 space-y-4">
                                <h3 class="font-bold text-[#0f172a] text-sm border-b border-slate-50 pb-2 flex items-center gap-2">
                                    <i data-feather="file-text" class="w-4 h-4 text-blue-500"></i>
                                    Detail Pesanan
                                </h3>
                                <div class="text-xs space-y-3 font-semibold text-slate-500">
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
                                        <p class="text-blue-600 font-extrabold text-sm mt-0.5">Rp {{ number_format($transaksi->total_bayar_akhir, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Items and Weights Form -->
                        <div class="md:col-span-2 space-y-6">
                            <form action="{{ route('admin.riwayat-pesanan.proses', $transaksi->id) }}" method="POST" @submit.prevent="submitForm($event)" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 space-y-6">
                                @csrf

                                <!-- Items List Container -->
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                                        <h3 class="font-bold text-[#0f172a] text-sm flex items-center gap-2">
                                            <i data-feather="list" class="w-4 h-4 text-blue-500"></i>
                                            Daftar Item Laundry
                                        </h3>
                                        <button type="button" @click="addCustomItem()" class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1 transition-all">
                                            <i data-feather="plus" class="w-3.5 h-3.5"></i>
                                            Tambah Item Kustom
                                        </button>
                                    </div>

                                    <!-- Alpine Loop for Items -->
                                    <div class="space-y-3 max-h-[350px] overflow-y-auto pr-1">
                                        <template x-for="(item, index) in items" :key="index">
                                            <div>
                                                <!-- If predefined item (shows checkbox) -->
                                                <template x-if="item.predefined">
                                                    <div class="flex items-center justify-between p-3 bg-slate-50 hover:bg-slate-100/75 rounded-xl border border-slate-100 transition-all">
                                                        <div class="flex items-center gap-3">
                                                            <input type="checkbox"
                                                                   x-model="item.checked"
                                                                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4.5 h-4.5 cursor-pointer">
                                                            <span class="text-xs font-bold text-slate-700" x-text="item.nama_item"></span>
                                                        </div>
                                                        
                                                        <div class="flex items-center gap-2" x-show="item.checked" x-transition>
                                                            <button type="button" @click="if(item.qty > 1) item.qty--" class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 text-xs font-bold">-</button>
                                                            <input type="number"
                                                                   :name="item.checked ? `items[${index}][qty]` : ''"
                                                                   x-model.number="item.qty"
                                                                   min="1"
                                                                   class="w-12 text-center bg-white border border-slate-200 rounded-lg py-1 text-xs font-bold text-slate-700 outline-none">
                                                            <button type="button" @click="item.qty++" class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 text-xs font-bold">+</button>
                                                            
                                                            <input type="hidden" :name="item.checked ? `items[${index}][nama_item]` : ''" :value="item.nama_item">
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- If custom item (shows text input) -->
                                                <template x-if="!item.predefined">
                                                    <div class="flex items-center gap-3 bg-blue-50/30 p-3 rounded-xl border border-blue-100/50 relative group transition-all">
                                                        <div class="flex-1">
                                                            <label class="text-[9px] font-bold text-blue-500 uppercase tracking-wider block mb-1">Item Kustom</label>
                                                            <input type="text"
                                                                   :name="`items[${index}][nama_item]`"
                                                                   x-model="item.nama_item"
                                                                   placeholder="Nama pakaian/laundry..."
                                                                   class="w-full bg-white border border-blue-200/60 rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-700 outline-none focus:border-blue-500 transition-all"
                                                                   required>
                                                        </div>
                                                        <div class="w-24">
                                                            <label class="text-[9px] font-bold text-blue-500 uppercase tracking-wider block mb-1 text-center">Qty</label>
                                                            <div class="flex items-center gap-1 justify-center">
                                                                <button type="button" @click="if(item.qty > 1) item.qty--" class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 text-xs font-bold">-</button>
                                                                <input type="number"
                                                                       :name="`items[${index}][qty]`"
                                                                       x-model.number="item.qty"
                                                                       min="1"
                                                                       class="w-10 text-center bg-white border border-slate-200 rounded-lg py-1 text-xs font-bold text-slate-700 outline-none">
                                                                <button type="button" @click="item.qty++" class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 text-xs font-bold">+</button>
                                                            </div>
                                                        </div>
                                                        <div class="self-end pb-0.5">
                                                            <button type="button" @click="removeItem(index)"
                                                                    class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-all">
                                                                <i data-feather="trash-2" class="w-4 h-4"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Weight Scale and Price calculations -->
                                <div class="space-y-4 border-t border-slate-50 pt-4">
                                    <h3 class="font-bold text-[#0f172a] text-sm flex items-center gap-2">
                                        <i data-feather="activity" class="w-4 h-4 text-blue-500"></i>
                                        Timbangan & Tarif
                                    </h3>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <!-- Actual Weight input -->
                                        <div>
                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Berat Timbangan (Kg)</label>
                                            <div class="relative">
                                                <input type="number"
                                                       name="actual_weight"
                                                       x-model.number="actualWeight"
                                                       step="0.01"
                                                       min="0.01"
                                                       placeholder="0.00"
                                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3 pr-10 py-2.5 text-xs font-bold text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all"
                                                       required>
                                                <span class="absolute right-3 inset-y-0 flex items-center text-xs font-bold text-slate-400">kg</span>
                                            </div>
                                        </div>

                                        <!-- Minimum Weight configuration -->
                                        <div>
                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Minimal Berat (Kg)</label>
                                            <div class="relative">
                                                <input type="number"
                                                       name="minimum_weight"
                                                       x-model.number="minimumWeight"
                                                       step="0.1"
                                                       min="0"
                                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3 pr-10 py-2.5 text-xs font-bold text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all"
                                                       required>
                                                <span class="absolute right-3 inset-y-0 flex items-center text-xs font-bold text-slate-400">kg</span>
                                            </div>
                                        </div>

                                        <!-- Price per Kg -->
                                        <div>
                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Harga per Kg (Rp)</label>
                                            <div class="relative">
                                                <span class="absolute left-3 inset-y-0 flex items-center text-xs font-bold text-slate-400">Rp</span>
                                                <input type="number"
                                                       name="price_per_kg"
                                                       x-model.number="pricePerKg"
                                                       min="0"
                                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2.5 text-xs font-bold text-slate-700 outline-none focus:border-blue-500 focus:bg-white transition-all"
                                                       required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic calculations results card -->
                                <div class="bg-blue-50/50 rounded-2xl border border-blue-100/30 p-5 space-y-3 font-semibold text-xs">
                                    <div class="flex justify-between items-center text-slate-500">
                                        <span>Formula Berat Ditagih:</span>
                                        <span class="font-normal">max(Minimal, Timbangan)</span>
                                    </div>
                                    <div class="flex justify-between items-center text-slate-500">
                                        <span>Berat yang Ditagih:</span>
                                        <span class="text-slate-800 font-bold"><span x-text="chargedWeight.toFixed(2)">0.00</span> kg</span>
                                    </div>
                                    <div class="flex justify-between items-center text-slate-500">
                                        <span>Tarif per Kg:</span>
                                        <span class="text-slate-800 font-bold" x-text="formatRupiah(pricePerKg)">Rp0</span>
                                    </div>
                                    <div class="flex justify-between items-center border-t border-blue-100/50 pt-3 text-sm">
                                        <span class="text-slate-800 font-bold">Total Biaya Layanan:</span>
                                        <span class="text-blue-600 font-extrabold text-lg" x-text="formatRupiah(totalPrice)">Rp0</span>
                                    </div>
                                </div>

                                <!-- Submission Buttons -->
                                <div class="flex justify-end gap-3 pt-2">
                                    <a href="{{ route('admin.riwayat-pesanan') }}" class="text-center border border-slate-200 hover:bg-slate-50 text-slate-700 px-5 py-2.5 rounded-xl text-xs font-bold transition-all">
                                        Batal
                                    </a>
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                                        <i data-feather="check" class="w-4 h-4"></i>
                                        Selesai & Proses Pesanan
                                    </button>
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
                items: @json($initialItems),
                actualWeight: {{ old('actual_weight', '') !== '' ? old('actual_weight') : '0' }},
                minimumWeight: {{ old('minimum_weight', '3.0') }},
                pricePerKg: {{ old('price_per_kg', $defaultPrice) }},
                originalTotal: {{ $transaksi->total_bayar_akhir }},
                
                addCustomItem() {
                    this.items.push({ nama_item: '', qty: 1, checked: true, predefined: false });
                    setTimeout(() => {
                        if (typeof feather !== 'undefined') feather.replace();
                    }, 50);
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                get chargedWeight() {
                    if (!this.actualWeight || this.actualWeight <= 0) return 0;
                    return Math.max(this.minimumWeight, this.actualWeight);
                },
                get totalPrice() {
                    return Math.round(this.chargedWeight * this.pricePerKg);
                },
                formatRupiah(value) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
                },
                submitForm(e) {
                    const hasActiveItem = this.items.some(i => i.checked || !i.predefined);
                    if (!hasActiveItem) {
                        alert('Silakan pilih minimal satu item laundry.');
                        return;
                    }
                    e.target.submit();
                }
            }));
        });
    </script>
</body>
</html>
