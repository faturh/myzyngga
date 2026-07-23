<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Proses Pekerjaan - {{ config('app.name', 'Zyngga') }}</title>

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

        /* Hide number input spinners */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
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
                            <ul class="list-disc list-inside pl-6 font-medium">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- PAGE TITLE SECTION -->
                    <div>
                        <h1 class="text-base font-medium" style="color:#0F0F0F;">Proses Pekerjaan</h1>
                        <p class="text-xs font-normal" style="color:#808080;">{{ $transaksi->nota }}</p>
                    </div>

                    @php
                        $layananNama = strtolower($transaksi->layananPrioritas->nama ?? 'reguler');
                        $oldItems = old('items');
                        $initialItems = [];
                        if (!empty($oldItems) && is_array($oldItems)) {
                            foreach ($oldItems as $oldItem) {
                                if (empty($oldItem['nama_item'])) continue;
                                $isPredefined = $itemsAvailable->contains('nama', $oldItem['nama_item']);
                                $initialItems[] = [
                                    'id' => 'item_' . str()->slug($oldItem['nama_item']) . '_' . uniqid(),
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
                                        'id' => 'item_' . str()->slug($item->nama) . '_' . uniqid(),
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
                                    'id' => 'item_' . str()->slug($item->nama) . '_' . uniqid(),
                                    'nama_item' => $item->nama,
                                    'qty' => 1,
                                    'checked' => false,
                                    'predefined' => true,
                                ];
                            }
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
                                <span style="color:#808080;">Deadline Pengerjaan</span>
                                <span class="font-medium" style="color:#0F0F0F;">{{ $transaksi->getDeadlineWaktu()->locale('id')->isoFormat('dddd, D MMM | HH.mm') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span style="color:#808080;">Total Bayar</span>
                                <span class="font-medium" style="color:#003E9C;">Rp {{ number_format($transaksi->total_bayar_akhir, 0, ',', '.') }}</span>
                            </div>

                            <!-- Data yang di-hide untuk preservasi backend logic -->
                            <div class="hidden">
                                @if($transaksi->timbangan)
                                    <span>Berat: {{ number_format($transaksi->timbangan->charged_weight, 2) }} kg</span>
                                @endif
                                <span>Estimasi: {{ $transaksi->getEstimasiPengerjaanJam() }} Jam</span>
                            </div>
                        </div>
                    </div>

                    <!-- FORM SECTION FOR KARYAWAN & RINCIAN PAKAIAN -->
                    <div x-data="prosesPekerjaanForm()" class="space-y-4">
                        <form action="{{ route('admin.riwayat-pesanan.kerjakan', $transaksi->id) }}" method="POST" @submit.prevent="submitForm($event)" class="space-y-4">
                            @csrf
                            <input type="hidden" name="tab" value="{{ $tab }}">

                            <!-- KARTU 2: KARYAWAN PENANGGUNG JAWAB -->
                            <div class="bg-white rounded-lg p-5 shadow-sm space-y-3">
                                <h3 class="text-sm font-medium" style="color:#0F0F0F;">Karyawan Penanggung Jawab</h3>
                                <div class="relative">
                                    <select name="pegawai_id" class="w-full bg-white border rounded-lg px-3 text-xs text-[#0F0F0F] font-normal focus:outline-none appearance-none" style="border-color:#CCCCCC; height:40px;" required>
                                        <option value="">-- Pilih Pegawai --</option>
                                        @foreach($pegawaiList as $pegawai)
                                            <option value="{{ $pegawai->id }}" {{ old('pegawai_id', $transaksi->getRawPegawaiId()) == $pegawai->id ? 'selected' : '' }}>
                                                {{ $pegawai->name }} ({{ ucfirst(str_replace('_', ' ', $pegawai->role)) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#808080]">
                                        <i data-feather="chevron-down" class="w-3.5 h-3.5"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- KARTU 3: RINCIAN PAKAIAN -->
                            <div class="bg-white rounded-lg p-5 shadow-sm space-y-3">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-medium" style="color:#0F0F0F;">Rincian Pakaian</h3>
                                    @if($layananNama !== 'satuan')
                                    <button type="button" @click="addCustomItem()" class="text-xs font-medium text-[#003E9C] hover:underline bg-transparent border-0 cursor-pointer">
                                        + Tambah Item
                                    </button>
                                    @endif
                                </div>

                                <!-- Item Satuan Terdaftar Info Box -->
                                @if($transaksi->tambahanSatuan && $transaksi->tambahanSatuan->count() > 0)
                                <div class="bg-[#F8FAFC] p-3 rounded-xl border border-[#CCCCCC] space-y-1.5 text-xs">
                                    <p class="font-medium text-[#0F0F0F]">Item Satuan Terdaftar:</p>
                                    @foreach($transaksi->tambahanSatuan as $item)
                                        <div class="flex justify-between items-center text-[#808080]">
                                            <span>{{ $item->kategoriPakaianSatuan->nama_pakaian ?? '-' }} ({{ $item->jumlah }} pcs)</span>
                                            <span class="font-medium text-[#0F0F0F]">Rp {{ number_format($item->harga_akhir, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                @endif

                                @if($layananNama !== 'satuan')
                                <div class="space-y-2.5 max-h-[350px] overflow-y-auto pr-1 custom-scrollbar">
                                    <template x-for="(item, index) in items" :key="item.id || index">
                                        <div>
                                            <!-- Predefined item -->
                                            <template x-if="item.predefined">
                                                <div class="flex items-center justify-between bg-[#F8FAFC] p-2.5 rounded-xl border border-[#CCCCCC]">
                                                    <label class="flex items-center gap-2.5 cursor-pointer flex-1 select-none">
                                                        <input type="checkbox"
                                                               x-model="item.checked"
                                                               class="rounded border-[#CCCCCC] text-[#003E9C] focus:ring-[#003E9C] w-4 h-4 cursor-pointer">
                                                        <span class="text-xs font-medium text-[#0F0F0F]" x-text="item.nama_item"></span>
                                                    </label>
                                                    
                                                    <div class="flex items-center gap-2" x-show="item.checked" x-transition>
                                                        <button type="button" @click="if(item.qty > 1) item.qty--" class="w-6 h-6 rounded-md bg-white border border-[#CCCCCC] flex items-center justify-center text-[#0F0F0F] hover:bg-slate-50 text-xs font-medium">-</button>
                                                        <input type="number"
                                                               :name="item.checked ? `items[${index}][qty]` : ''"
                                                               x-model.number="item.qty"
                                                               min="1"
                                                               class="w-10 text-center bg-transparent text-xs font-medium text-[#0F0F0F] outline-none">
                                                        <button type="button" @click="item.qty++" class="w-6 h-6 rounded-md bg-white border border-[#CCCCCC] flex items-center justify-center text-[#0F0F0F] hover:bg-slate-50 text-xs font-medium">+</button>
                                                        
                                                        <input type="hidden" :name="item.checked ? `items[${index}][nama_item]` : ''" :value="item.nama_item">
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Custom item -->
                                            <template x-if="!item.predefined">
                                                <div class="flex items-center gap-2 bg-[#F8FAFC] p-2.5 rounded-xl border border-[#CCCCCC]">
                                                    <div class="flex-1">
                                                        <input type="text"
                                                               :name="`items[${index}][nama_item]`"
                                                               x-model="item.nama_item"
                                                               placeholder="Nama item..."
                                                               class="w-full bg-white border rounded-lg px-3 text-xs text-[#0F0F0F] font-normal focus:outline-none"
                                                               style="border-color:#CCCCCC; height:36px;"
                                                               required>
                                                    </div>
                                                    <div class="flex items-center gap-1.5 shrink-0">
                                                        <button type="button" @click="if(item.qty > 1) item.qty--" class="w-6 h-6 rounded-md bg-white border border-[#CCCCCC] flex items-center justify-center text-[#0F0F0F] hover:bg-slate-50 text-xs font-medium">-</button>
                                                        <input type="number"
                                                               :name="`items[${index}][qty]`"
                                                               x-model.number="item.qty"
                                                               min="1"
                                                               class="w-10 text-center bg-transparent text-xs font-medium text-[#0F0F0F] outline-none">
                                                        <button type="button" @click="item.qty++" class="w-6 h-6 rounded-md bg-white border border-[#CCCCCC] flex items-center justify-center text-[#0F0F0F] hover:bg-slate-50 text-xs font-medium">+</button>
                                                        
                                                        <button type="button" @click="removeItem(index)" class="p-1 text-[#EF4444] hover:bg-rose-50 rounded-lg transition-colors bg-transparent border-0 cursor-pointer">
                                                            <i class="ri-delete-bin-line text-base"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                                @endif

                                <!-- ACTION BUTTONS -->
                                <div class="flex items-center justify-center gap-3 pt-2">
                                    <a href="{{ route('admin.riwayat-pesanan', ['tab' => $tab]) }}" 
                                       class="flex-1 text-center font-medium text-xs rounded-full border transition-colors flex items-center justify-center cursor-pointer"
                                       style="border-color:#003E9C; color:#003E9C; height:48px;">
                                        Batal
                                    </a>
                                    <button type="submit" 
                                            class="flex-1 text-center text-white font-medium text-xs rounded-full border-0 shadow-sm transition-colors flex items-center justify-center cursor-pointer"
                                            style="background:#003E9C; height:48px;">
                                        Mulai Kerja & Cetak Nota
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

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
            Alpine.data('prosesPekerjaanForm', () => ({
                items: @json($initialItems),
                
                addCustomItem() {
                    this.items.push({ id: 'custom_' + Date.now() + '_' + Math.random(), nama_item: '', qty: 1, checked: true, predefined: false });
                    setTimeout(() => {
                        if (typeof feather !== 'undefined') feather.replace();
                    }, 50);
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                submitForm(e) {
                    const hasActiveItem = this.items.some(i => i.checked || !i.predefined);
                    if (!hasActiveItem) {
                        alert('Silakan pilih minimal satu jenis pakaian untuk rincian pakaian.');
                        return;
                    }
                    e.target.submit();
                }
            }));
        });
    </script>
</body>
</html>
