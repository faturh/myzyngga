<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Keuangan Toko - {{ config('app.name', 'Zyngga') }}</title>

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
        
        /* Custom scrollbar for sidebar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
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

        /* Hide Tailwind Forms background SVG arrow on select */
        select {
            background-image: none !important;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            padding-right: 2.5rem !important;
        }
        select::-ms-expand {
            display: none;
        }

        /* Hide native calendar picker indicator icon but keep it clickable across the input */
        input[type="date"]::-webkit-calendar-picker-indicator {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        /* Style for parent container of date input to allow relative positioning absolute picker */
        .relative-input-container {
            position: relative;
        }
    </style>
</head>
<body class="font-dm-sans antialiased bg-[#f8fafc] text-[#1e293b] h-full overflow-hidden" x-data="{ sidebarOpen: false, modalOpen: false }">

    <!-- App Container -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR (Desktop + Mobile) -->
        @include('operator.partials.sidebar')

        <!-- MAIN WINDOW WRAPPER -->
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
            
            <!-- HEADER -->
            <header class="h-16 bg-white border-b border-slate-100/90 flex items-center justify-between px-6 sticky top-0 z-30 shrink-0">
                <div class="flex items-center gap-4">
                    <!-- Mobile Hamburger Menu Button -->
                    <button @click="sidebarOpen = true" class="lg:hidden p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-lg transition-colors">
                        <i data-feather="menu" class="w-6 h-6"></i>
                    </button>
                    <h1 class="text-lg font-medium text-slate-800">Keuangan Toko</h1>
                </div>

                <!-- Right Header Actions -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <img src="/images/MyZyngga_avatar.png" alt="MyZyngga" class="w-8 h-8 rounded-full border border-slate-100 object-cover">
                    </div>
                </div>
            </header>

            <!-- CONTENT INNER CONTAINER -->
            <div class="flex-1 overflow-y-auto px-6 py-8 custom-scrollbar bg-[#f0f6ff]">
                
                <!-- Inner Page Stack: Constrained and Centered for mockup alignment -->
                <div class="max-w-md mx-auto w-full space-y-5">
                    
                    <!-- Alerts for success/errors -->
                    @if(session('success'))
                        <div class="bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-medium px-4 py-3 rounded-xl flex items-center gap-2 animate-none">
                            <i data-feather="check-circle" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-rose-50 border border-rose-100 text-rose-800 text-xs font-medium px-4 py-3 rounded-xl space-y-1">
                            @foreach($errors->all() as $error)
                                <div class="flex items-center gap-2">
                                    <i data-feather="alert-circle" class="w-4 h-4 text-rose-500 shrink-0"></i>
                                    <span>{{ $error }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- 1. CATAT KAS MANUAL BUTTON (Full width) -->
                    <button @click="modalOpen = true" class="w-full bg-[#0b4ab1] hover:bg-blue-800 text-white font-medium text-sm py-4 px-4 rounded-2xl shadow-sm transition-colors text-center block">
                        + Catat Kas Manual
                    </button>

                    <!-- 2. TOTAL SALDO TOKO CARD -->
                    <div class="bg-[#0f9f6e] text-white p-6 rounded-2xl shadow-sm relative overflow-hidden">
                        <span class="text-xs font-normal text-emerald-100/90 block">Total Saldo Toko</span>
                        <h3 class="text-3xl font-medium mt-1">Rp {{ number_format($saldoToko, 0, ',', '.') }}</h3>
                        <p class="text-[10px] text-emerald-100/80 mt-4 font-normal">Berdasarkan transaksi lunas & penyesuaian</p>
                    </div>

                    <!-- 3. PEMASUKAN & PENGELUARAN CARDS (Side-by-side) -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Pemasukan Card -->
                        <div class="bg-white border border-slate-100/80 p-5 rounded-2xl shadow-sm flex flex-col justify-center">
                            <span class="text-xs font-normal text-slate-400 block">Pemasukan</span>
                            <h3 class="text-lg font-medium text-[#0f9f6e] mt-1">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
                        </div>

                        <!-- Pengeluaran Card -->
                        <div class="bg-white border border-slate-100/80 p-5 rounded-2xl shadow-sm flex flex-col justify-center">
                            <span class="text-xs font-normal text-slate-400 block">Pengeluaran</span>
                            <h3 class="text-lg font-medium text-rose-500 mt-1">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
                        </div>
                    </div>

                    <!-- 4. FILTER CARD -->
                    <div class="bg-white border border-slate-100/80 p-5 rounded-2xl shadow-sm">
                        <form method="GET" action="{{ route('admin.keuangan') }}" class="space-y-4">
                            <!-- Jenis Filter -->
                            <div class="space-y-1">
                                <label class="text-xs font-normal text-slate-400 block">Jenis Filter</label>
                                <div class="relative">
                                    <select name="filter_type" id="filter_type" class="w-full text-xs font-medium bg-[#f8fafc] border border-slate-100 rounded-xl px-4 py-3.5 focus:outline-none focus:border-blue-500 focus:bg-white appearance-none transition-all">
                                        <option value="daily" {{ $filterType === 'daily' ? 'selected' : '' }}>Harian</option>
                                        <option value="weekly" {{ $filterType === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                        <option value="monthly" {{ $filterType === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                        <i data-feather="chevron-down" class="w-4 h-4"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Pilih Waktu -->
                            <div class="space-y-1">
                                <label class="text-xs font-normal text-slate-400 block">Pilih Waktu</label>
                                <div class="relative relative-input-container">
                                    <input type="date" name="date_value" id="date_value" value="{{ $filterType === 'daily' ? $dateValue : '' }}" class="w-full text-xs font-medium bg-[#f8fafc] border border-slate-100 rounded-xl px-4 py-3.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all relative" style="position: relative;">
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                        <i data-feather="calendar" class="w-4 h-4"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-2">
                                <button type="submit" class="w-full bg-[#0b4ab1] hover:bg-blue-800 text-white font-medium text-sm py-3.5 px-4 rounded-xl shadow-sm transition-colors text-center block">
                                    Terapkan Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- 5. RIWAYAT TRANSAKSI CARD -->
                    <div class="bg-white border border-slate-100/80 rounded-2xl shadow-sm p-5 space-y-4">
                        <div>
                            <h3 class="font-medium text-slate-800 text-sm">Riwayat Transaksi</h3>
                            <p class="text-[10px] text-slate-400 font-normal mt-0.5">Periode: {{ $startDate }} s.d. {{ $endDate }}</p>
                        </div>
                        
                        <hr class="border-slate-100">

                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 text-[10px] text-slate-400 font-medium">
                                        <!-- Hidden columns preserved in DOM but hidden as requested -->
                                        <th class="hidden">Tanggal</th>
                                        <th class="hidden">Sumber</th>
                                        <th class="hidden">Kategori</th>
                                        
                                        <th class="pb-3 text-xs font-medium">Keterangan</th>
                                        <th class="pb-3 text-xs font-medium text-center">Nominal</th>
                                        <th class="pb-3 text-xs font-medium text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 text-xs">
                                    @forelse($records as $rec)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <!-- Preserved hidden columns -->
                                            <td class="hidden">{{ $rec['tanggal'] }}</td>
                                            <td class="hidden">{{ $rec['source'] }}</td>
                                            <td class="hidden">{{ $rec['kategori'] }}</td>
                                            
                                            <!-- Keterangan Column -->
                                            <td class="py-4 font-normal text-slate-500">
                                                {{ $rec['kategori'] ?: ($rec['tipe'] === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran') }}
                                            </td>
                                            
                                            <!-- Nominal Column -->
                                            <td class="py-4 text-center font-normal text-slate-700 whitespace-nowrap">
                                                Rp {{ number_format($rec['nominal'], 0, ',', '.') }}
                                            </td>
                                            
                                            <!-- Aksi Column -->
                                            <td class="py-4 text-right">
                                                @if($rec['source'] === 'manual')
                                                    <form id="delete-form-{{ $rec['id'] }}" method="POST" action="{{ route('admin.keuangan.destroy', $rec['id']) }}" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                    <button type="button" onclick="deleteKeuanganRecord({{ $rec['id'] }})" class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 p-1.5 rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center border-0 bg-transparent">
                                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                @else
                                                    <span class="text-slate-300 text-[10px] font-normal italic">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-12 text-center text-slate-400 font-normal">
                                                <div class="flex flex-col items-center justify-center gap-2">
                                                    <i data-feather="folder-open" class="w-8 h-8 opacity-45"></i>
                                                    <span>Belum ada catatan keuangan.</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- MODAL PENULISAN TRANSAKSI MANUAL -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="modalOpen = false"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg" @click.away="modalOpen = false">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between">
                    <h3 class="font-medium text-[#0f172a] text-sm">Catat Kas Manual</h3>
                    <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Modal Body (Form) -->
                <form method="POST" action="{{ route('admin.keuangan.store') }}" class="p-6 space-y-4">
                    @csrf

                    <div>
                        <label class="text-xs font-normal text-slate-500 block mb-1.5">Tipe Transaksi</label>
                        <select name="tipe" required class="w-full text-xs font-medium bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                            <option value="pengeluaran">Pengeluaran (Kas Keluar)</option>
                            <option value="pemasukan">Pemasukan (Kas Masuk)</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-normal text-slate-500 block mb-1.5">Kategori</label>
                        <input type="text" name="kategori" placeholder="Contoh: Pencairan Dana, Operasional, Kas Masuk" required class="w-full text-xs font-medium bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="text-xs font-normal text-slate-500 block mb-1.5">Tanggal</label>
                        <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full text-xs font-medium bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="text-xs font-normal text-slate-500 block mb-1.5">Nominal (Rupiah)</label>
                        <input type="number" name="nominal" min="1" placeholder="Masukkan angka tanpa titik, contoh: 50000" required class="w-full text-xs font-medium bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="text-xs font-normal text-slate-500 block mb-1.5">Keterangan Tambahan</label>
                        <textarea name="keterangan" rows="3" placeholder="Tulis rincian pencairan dana di sini..." class="w-full text-xs font-medium bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 focus:bg-white transition-all"></textarea>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-4 border-t border-slate-50 flex items-center justify-end gap-3">
                        <button type="button" @click="modalOpen = false" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium text-xs px-4 py-2.5 rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" class="bg-[#0b4ab1] hover:bg-blue-800 text-white font-medium text-xs px-4 py-2.5 rounded-xl shadow-sm transition-all duration-300">
                            Simpan Catatan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- Initialize Icons & Alpine logic for date value inputs -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            const filterTypeSelect = document.getElementById('filter_type');
            const dateValueInput = document.getElementById('date_value');

            function adjustInputType() {
                const val = filterTypeSelect.value;
                if (val === 'daily') {
                    dateValueInput.type = 'date';
                    // Parse if value was month or week format
                    if (dateValueInput.value && dateValueInput.value.length < 10) {
                        dateValueInput.value = new Date().toISOString().split('T')[0];
                    }
                } else if (val === 'weekly') {
                    dateValueInput.type = 'week';
                } else if (val === 'monthly') {
                    dateValueInput.type = 'month';
                }
            }

            filterTypeSelect.addEventListener('change', adjustInputType);
            
            // Initial call based on current backend value
            const currentFilter = "{{ $filterType }}";
            const currentValue = "{{ $dateValue }}";
            
            if (currentFilter === 'weekly') {
                dateValueInput.type = 'week';
                dateValueInput.value = currentValue;
            } else if (currentFilter === 'monthly') {
                dateValueInput.type = 'month';
                dateValueInput.value = currentValue;
            } else {
                dateValueInput.type = 'date';
                dateValueInput.value = currentValue;
            }
        });

        window.deleteKeuanganRecord = function(id) {
            if (confirm('Apakah Anda yakin ingin menghapus catatan manual ini?')) {
                const form = document.getElementById('delete-form-' + id);
                if (form) {
                    form.submit();
                } else {
                    console.error('Delete form not found for ID:', id);
                }
            }
        };
    </script>
</body>
</html>
