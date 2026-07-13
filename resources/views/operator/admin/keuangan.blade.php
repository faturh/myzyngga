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
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar */
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
    </style>
</head>
<body class="font-outfit antialiased bg-[#f8fafc] text-[#1e293b] h-full overflow-hidden" x-data="{ sidebarOpen: false, modalOpen: false }">

    <!-- App Container -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR (Desktop + Mobile) -->
        @include('operator.partials.sidebar')

        <!-- MAIN WINDOW WRAPPER -->
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
            
            <!-- HEADER -->
            <header class="h-16 bg-white border-b border-slate-100/90 flex items-center justify-between px-6 sticky top-0 z-30 shrink-0">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-lg transition-colors">
                        <i data-feather="menu" class="w-6 h-6"></i>
                    </button>
                    <h1 class="text-lg font-bold text-slate-800">Keuangan Toko</h1>
                </div>

                <div class="flex items-center gap-4">
                    <button @click="modalOpen = true" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-sm hover:shadow transition-all duration-300 flex items-center gap-2">
                        <i data-feather="plus" class="w-4 h-4"></i>
                        <span>Catat Kas Manual</span>
                    </button>
                </div>
            </header>

            <!-- CONTENT INNER CONTAINER -->
            <div class="flex-1 overflow-y-auto px-6 py-8 custom-scrollbar">
                <div class="max-w-7xl mx-auto space-y-8">
                    
                    @if(session('success'))
                        <div class="bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-bold px-4 py-3.5 rounded-2xl flex items-center gap-3">
                            <i data-feather="check-circle" class="w-5 h-5 text-emerald-500"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-rose-50 border border-rose-100 text-rose-800 text-xs font-bold px-4 py-3.5 rounded-2xl space-y-1">
                            @foreach($errors->all() as $error)
                                <div class="flex items-center gap-3">
                                    <i data-feather="alert-circle" class="w-5 h-5 text-rose-500"></i>
                                    <span>{{ $error }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- TOP FINANCIAL SUMMARY METRICS -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <!-- Metric 1: Saldo Toko -->
                        <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white p-6 rounded-2xl shadow-sm relative overflow-hidden">
                            <div class="absolute right-[-10px] bottom-[-10px] opacity-10 text-white">
                                <i data-feather="credit-card" class="w-32 h-32"></i>
                            </div>
                            <span class="text-xs font-bold text-emerald-100/90 block">Total Saldo Toko (Akumulasi)</span>
                            <h3 class="text-3xl font-black mt-2">Rp {{ number_format($saldoToko, 0, ',', '.') }}</h3>
                            <p class="text-[10px] text-emerald-100/80 mt-2 font-medium">Berdasarkan transaksi lunas & penyesuaian manual</p>
                        </div>

                        <!-- Metric 2: Total Pemasukan -->
                        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <i data-feather="arrow-down-left" class="w-6 h-6"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs font-bold text-slate-400 block">Pemasukan (Terfilter)</span>
                                <h3 class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
                            </div>
                        </div>

                        <!-- Metric 3: Total Pengeluaran -->
                        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                                <i data-feather="arrow-up-right" class="w-6 h-6"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs font-bold text-slate-400 block">Pengeluaran (Terfilter)</span>
                                <h3 class="text-2xl font-black text-rose-600 mt-1">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
                            </div>
                        </div>

                    </div>

                    <!-- FILTER BOARD -->
                    <div class="bg-white border border-slate-100/90 p-6 rounded-2xl shadow-sm">
                        <form method="GET" action="{{ route('admin.keuangan') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                            
                            <!-- Filter Type Picker -->
                            <div>
                                <label class="text-xs font-bold text-[#0f172a] block mb-2">Jenis Filter</label>
                                <select name="filter_type" id="filter_type" class="w-full text-xs font-semibold bg-[#f8fafc] border border-slate-100 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-all">
                                    <option value="daily" {{ $filterType === 'daily' ? 'selected' : '' }}>Harian</option>
                                    <option value="weekly" {{ $filterType === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                    <option value="monthly" {{ $filterType === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                </select>
                            </div>

                            <!-- Date Value Input -->
                            <div>
                                <label class="text-xs font-bold text-[#0f172a] block mb-2">Pilih Waktu</label>
                                <input type="date" name="date_value" id="date_value" value="{{ $filterType === 'daily' ? $dateValue : '' }}" class="w-full text-xs font-semibold bg-[#f8fafc] border border-slate-100 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-all">
                            </div>

                            <!-- Submit Action Button -->
                            <div>
                                <button type="submit" class="w-full bg-[#0f172a] hover:bg-[#1e293b] text-white font-bold text-xs px-4 py-3 rounded-xl shadow-sm transition-all duration-300 flex items-center justify-center gap-2">
                                    <i data-feather="filter" class="w-4 h-4"></i>
                                    <span>Terapkan Filter</span>
                                </button>
                            </div>

                        </form>
                    </div>

                    <!-- LEDGER TABLE CARD -->
                    <div class="bg-white border border-slate-100/90 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between">
                            <h3 class="font-extrabold text-sm text-[#0f172a]">Detail Alur Kas ({{ $startDate }} s.d. {{ $endDate }})</h3>
                            <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[10px] font-bold">
                                {{ $records->count() }} Entri Kas
                            </span>
                        </div>
                        
                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/50 border-b border-slate-100">
                                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Sumber</th>
                                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Kategori</th>
                                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Keterangan</th>
                                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider text-right">Jumlah</th>
                                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100/80 text-xs">
                                    @forelse($records as $rec)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4 font-bold text-[#0f172a] whitespace-nowrap">{{ $rec['tanggal'] }}</td>
                                            <td class="px-6 py-4">
                                                @if($rec['source'] === 'transaksi')
                                                    <span class="bg-purple-50 text-purple-600 px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase">Sistem</span>
                                                @else
                                                    <span class="bg-amber-50 text-amber-600 px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase">Manual</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 font-semibold text-slate-600">{{ $rec['kategori'] }}</td>
                                            <td class="px-6 py-4 font-medium text-slate-500 max-w-xs truncate" title="{{ $rec['keterangan'] }}">{{ $rec['keterangan'] ?: '-' }}</td>
                                            <td class="px-6 py-4 text-right font-black whitespace-nowrap {{ $rec['tipe'] === 'pemasukan' ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $rec['tipe'] === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($rec['nominal'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($rec['source'] === 'manual')
                                                    <form method="POST" action="{{ route('admin.keuangan.destroy', $rec['id']) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan manual ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 p-1.5 rounded-lg transition-colors">
                                                            <i data-feather="trash-2" class="w-4 h-4"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-slate-300 text-[10px] font-bold italic">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">
                                                <div class="flex flex-col items-center justify-center gap-2">
                                                    <i data-feather="folder-open" class="w-8 h-8 opacity-45"></i>
                                                    <span>Belum ada catatan keuangan untuk rentang waktu yang dipilih.</span>
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
                    <h3 class="font-extrabold text-[#0f172a] text-sm">Catat Kas Manual</h3>
                    <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Modal Body (Form) -->
                <form method="POST" action="{{ route('admin.keuangan.store') }}" class="p-6 space-y-4">
                    @csrf

                    <div>
                        <label class="text-xs font-bold text-[#0f172a] block mb-1.5">Tipe Transaksi</label>
                        <select name="tipe" required class="w-full text-xs font-semibold bg-[#f8fafc] border border-slate-100 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-all">
                            <option value="pengeluaran">Pengeluaran (Kas Keluar)</option>
                            <option value="pemasukan">Pemasukan (Kas Masuk)</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-[#0f172a] block mb-1.5">Kategori</label>
                        <input type="text" name="kategori" placeholder="Contoh: Pencairan Dana, Operasional, Kas Masuk" required class="w-full text-xs font-semibold bg-[#f8fafc] border border-slate-100 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-all">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-[#0f172a] block mb-1.5">Tanggal</label>
                        <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full text-xs font-semibold bg-[#f8fafc] border border-slate-100 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-all">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-[#0f172a] block mb-1.5">Nominal (Rupiah)</label>
                        <input type="number" name="nominal" min="1" placeholder="Masukkan angka tanpa titik, contoh: 50000" required class="w-full text-xs font-semibold bg-[#f8fafc] border border-slate-100 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-all">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-[#0f172a] block mb-1.5">Keterangan Tambahan</label>
                        <textarea name="keterangan" rows="3" placeholder="Tulis rincian pencairan dana di sini..." class="w-full text-xs font-semibold bg-[#f8fafc] border border-slate-100 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-all"></textarea>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-4 border-t border-slate-50 flex items-center justify-end gap-3">
                        <button type="button" @click="modalOpen = false" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs px-4 py-2.5 rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-sm transition-all duration-300">
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
    </script>
</body>
</html>
