<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Gaji Karyawan - {{ config('app.name', 'Zyngga') }}</title>

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
    </style>
</head>
<body class="font-dm-sans antialiased bg-[#f8fafc] text-[#1e293b] h-full overflow-hidden" x-data="{ sidebarOpen: false }">

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
                </div>
                
                <!-- Right Header Actions -->
                <div class="flex items-center gap-4" x-data="{ open: false }">
                    <div class="relative">
                        <button @click="open = !open" class="flex items-center gap-3 hover:bg-slate-50 px-3 py-1.5 rounded-xl transition-all">
                            <img src="/images/MyZyngga_avatar.png" alt="MyZyngga" class="w-8 h-8 rounded-full border border-slate-100 object-cover">
                        </button>
                        
                        <!-- Dropdown Settings/Logout -->
                        <div x-show="open" @click.away="open = false" x-transition x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50">
                            <div class="px-4 py-2 border-b border-slate-50 mb-1">
                                <p class="text-xs font-bold text-[#0f172a]">MyZyngga Operator</p>
                                <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email ?? 'operator@zyngga.com' }}</p>
                            </div>
                            <a href="#" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                <i data-feather="settings" class="w-3.5 h-3.5 text-slate-400"></i>
                                Pengaturan Toko
                            </a>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50">
                                <i data-feather="log-out" class="w-3.5 h-3.5"></i>
                                Keluar Aplikasi
                            </a>
                            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- CONTENT INNER CONTAINER -->
            <div class="flex-1 overflow-y-auto px-6 py-8 custom-scrollbar">
                
                <!-- Inner Page Grid -->
                <div class="max-w-4xl mx-auto space-y-6">
                    
                    <!-- Date Filter and Presets Form -->
                    <form method="GET" action="{{ route('admin.gaji-karyawan') }}" class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm space-y-4">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h2 class="text-sm font-extrabold text-[#0f172a] uppercase tracking-wider">Filter Periode Rekap Gaji</h2>
                                <p class="text-xs font-semibold text-slate-400 mt-1">Atur rentang tanggal untuk merekap gaji berdasarkan berat kiloan (kg) pengerjaan.</p>
                            </div>
                            
                            <!-- Preset Buttons -->
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" onclick="setPreset('today')" class="bg-slate-50 hover:bg-slate-100 text-slate-700 px-3.5 py-2 rounded-xl text-xs font-bold transition-all border border-slate-150">Hari Ini</button>
                                <button type="button" onclick="setPreset('week')" class="bg-slate-50 hover:bg-slate-100 text-slate-700 px-3.5 py-2 rounded-xl text-xs font-bold transition-all border border-slate-150">Minggu Ini</button>
                                <button type="button" onclick="setPreset('month')" class="bg-slate-50 hover:bg-slate-100 text-slate-700 px-3.5 py-2 rounded-xl text-xs font-bold transition-all border border-slate-150">Bulan Ini</button>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Pilih Karyawan</label>
                                <select name="pegawai_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-700 font-bold focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all text-xs h-10">
                                    <option value="">Semua Karyawan</option>
                                    @foreach ($allKaryawan as $emp)
                                        <option value="{{ $emp->id }}" @if ($selectedEmployeeId == $emp->id) selected @endif>
                                            {{ $emp->name ?? $emp->username }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Tanggal Mulai</label>
                                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-slate-700 font-bold focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all text-xs h-10" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Tanggal Selesai</label>
                                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-slate-700 font-bold focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all text-xs h-10" />
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-1.5 h-10">
                                    <i data-feather="filter" class="w-4 h-4"></i>
                                    Filter
                                </button>
                                <a href="{{ route('admin.gaji-karyawan.download', ['start_date' => $startDate, 'end_date' => $endDate, 'pegawai_id' => $selectedEmployeeId]) }}" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-1.5 h-10">
                                    <i data-feather="download" class="w-4 h-4"></i>
                                    Unduh Excel
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <script>
                    function setPreset(type) {
                        const startInput = document.getElementById('start_date');
                        const endInput = document.getElementById('end_date');
                        const today = new Date().toISOString().split('T')[0];
                        
                        if (type === 'today') {
                            startInput.value = today;
                            endInput.value = today;
                        } else if (type === 'week') {
                            const d = new Date();
                            const day = d.getDay();
                            const diff = d.getDate() - day + (day === 0 ? -6 : 1);
                            const monday = new Date(d.setDate(diff)).toISOString().split('T')[0];
                            startInput.value = monday;
                            endInput.value = today;
                        } else if (type === 'month') {
                            const d = new Date();
                            const firstDay = new Date(d.getFullYear(), d.getMonth(), 2).toISOString().split('T')[0];
                            startInput.value = firstDay;
                            endInput.value = today;
                        }
                    }
                    </script>

                    <!-- LEFT/CENTER CONTENT PANEL -->
                    <div class="w-full space-y-6 bg-white border border-slate-100 p-6 rounded-2xl shadow-sm"
                         x-data="{
                             sortOrder: 'default',
                             employees: {{ json_encode($karyawan) }},
                             get sortedEmployees() {
                                 if (this.sortOrder === 'asc') {
                                     return [...this.employees].sort((a, b) => a.total_gaji - b.total_gaji);
                                 } else if (this.sortOrder === 'desc') {
                                     return [...this.employees].sort((a, b) => b.total_gaji - a.total_gaji);
                                 }
                                 return this.employees;
                             },
                             // MODAL STATE
                             showBayarModal: false,
                             showTarifModal: false,
                             selectedEmp: null,
                             payoutAmount: 0,
                             payoutDate: '{{ now()->toDateString() }}',
                             payoutNote: '',
                             tarifAmount: 0,

                             openBayar(emp) {
                                 this.selectedEmp = emp;
                                 this.payoutAmount = emp.total_gaji;
                                 this.payoutNote = 'Pembayaran Gaji Karyawan ' + emp.name + ' Periode {{ $startDate }} s/d {{ $endDate }}';
                                 this.showBayarModal = true;
                             },
                             openTarif(emp) {
                                 this.selectedEmp = emp;
                                 this.tarifAmount = emp.gaji_per_kg;
                                 this.showTarifModal = true;
                             }
                         }">
                        
                        <!-- Header Row inside Card -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-5">
                            <div>
                                <h1 class="text-xl font-extrabold text-[#0f172a] leading-none">Daftar Gaji Karyawan</h1>
                                <p class="text-xs font-semibold text-slate-400 mt-1.5">
                                    Pantau pengeluaran gaji karyawan laundry berdasarkan kilogram pengerjaan.
                                </p>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="text-slate-400 font-semibold whitespace-nowrap">Urutkan Gaji:</span>
                                    <select x-model="sortOrder" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 font-bold focus:outline-none focus:ring-1 focus:ring-blue-500 transition-all">
                                        <option value="default">Default</option>
                                        <option value="asc">Terendah -> Tertinggi</option>
                                        <option value="desc">Tertinggi -> Terendah</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Employees List Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                        <th class="pb-3 pl-2">Karyawan</th>
                                        <th class="pb-3 text-center">Tarif Gaji/Kg</th>
                                        <th class="pb-3 text-center">Total Kg Dikerjakan</th>
                                        <th class="pb-3 text-right pr-2">Total Gaji Diterima</th>
                                        <th class="pb-3 text-right pr-2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 text-xs">
                                    <template x-for="emp in sortedEmployees" :key="emp.id">
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="py-4 pl-2">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shadow-sm" x-text="emp.initial"></div>
                                                    <div>
                                                        <p class="font-extrabold text-[#0f172a] text-sm" x-text="emp.name"></p>
                                                        <div class="text-[10px] text-slate-400 font-semibold space-y-0.5 mt-0.5">
                                                            <p>ID Karyawan: #<span x-text="emp.id"></span></p>
                                                            <p>Rekening: <span class="text-blue-600 font-bold" x-text="emp.bank"></span> (<span class="font-mono text-slate-500" x-text="emp.nomor_rekening"></span>)</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 text-center font-semibold text-slate-700" x-text="'Rp ' + emp.gaji_per_kg.toLocaleString('id-ID') + ' / kg'"></td>
                                            <td class="py-4 text-center font-extrabold text-blue-600 text-sm" x-text="emp.total_kg + ' kg'"></td>
                                            <td class="py-4 text-right pr-2 font-black text-emerald-600 text-sm" x-text="'Rp ' + emp.total_gaji.toLocaleString('id-ID')"></td>
                                            <td class="py-4 text-right pr-2 whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button @click="openTarif(emp)" type="button" class="bg-amber-50 hover:bg-amber-100 text-amber-700 font-extrabold text-[10px] px-2.5 py-1.5 rounded-xl transition-all border border-amber-200">
                                                        Atur Tarif
                                                    </button>
                                                    <button @click="openBayar(emp)" type="button" class="bg-blue-50 hover:bg-blue-100 text-blue-700 font-extrabold text-[10px] px-2.5 py-1.5 rounded-xl transition-all border border-blue-200">
                                                        Bayar Gaji
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Modal Bayar Gaji -->
                        <div x-show="showBayarModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm" @click="showBayarModal = false"></div>
                                
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                
                                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100">
                                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                                        <h3 class="text-sm font-extrabold text-[#0f172a] uppercase tracking-wider">Bayar Gaji Karyawan</h3>
                                        <button @click="showBayarModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.gaji-karyawan.bayar') }}" method="POST" class="p-6 space-y-4">
                                        @csrf
                                        <input type="hidden" name="pegawai_id" :value="selectedEmp?.id">
                                        <input type="hidden" name="start_date" value="{{ $startDate }}">
                                        <input type="hidden" name="end_date" value="{{ $endDate }}">
                                        
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Nama Karyawan</label>
                                            <input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-500 font-bold" :value="selectedEmp?.name" readonly>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Bank</label>
                                                <input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-500 font-bold" :value="selectedEmp?.bank" readonly>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">No. Rekening</label>
                                                <input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-500 font-bold" :value="selectedEmp?.nomor_rekening" readonly>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Tanggal Pembayaran</label>
                                            <input type="date" name="tanggal" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 font-bold focus:outline-none focus:border-blue-500" x-model="payoutDate" required>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Nominal Payout (Rp)</label>
                                            <input type="number" name="nominal" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 font-bold focus:outline-none focus:border-blue-500" x-model="payoutAmount" required>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Catatan / Keterangan</label>
                                            <textarea name="keterangan" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 font-semibold focus:outline-none focus:border-blue-500" x-model="payoutNote" required></textarea>
                                        </div>
                                        
                                        <div class="pt-2 flex gap-3">
                                            <button type="button" @click="showBayarModal = false" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs py-2.5 rounded-xl transition-all">Batal</button>
                                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-2.5 rounded-xl transition-all shadow-sm">Konfirmasi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Atur Tarif -->
                        <div x-show="showTarifModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm" @click="showTarifModal = false"></div>
                                
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                
                                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100">
                                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                                        <h3 class="text-sm font-extrabold text-[#0f172a] uppercase tracking-wider">Atur Tarif Gaji per Kg</h3>
                                        <button @click="showTarifModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.gaji-karyawan.update-tarif') }}" method="POST" class="p-6 space-y-4">
                                        @csrf
                                        <input type="hidden" name="pegawai_id" :value="selectedEmp?.id">
                                        
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Nama Karyawan</label>
                                            <input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-500 font-bold" :value="selectedEmp?.name" readonly>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Tarif Gaji Baru (Rp / kg)</label>
                                            <input type="number" name="gaji" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 font-bold focus:outline-none focus:border-blue-500" x-model="tarifAmount" required>
                                        </div>
                                        
                                        <div class="pt-2 flex gap-3">
                                            <button type="button" @click="showTarifModal = false" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs py-2.5 rounded-xl transition-all">Batal</button>
                                            <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs py-2.5 rounded-xl transition-all shadow-sm">Simpan Tarif</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Pengiriman Gaji Card -->
                    <div class="w-full space-y-6 bg-white border border-slate-100 p-6 rounded-2xl shadow-sm">
                        <div class="border-b border-slate-100 pb-5">
                            <h2 class="text-lg font-extrabold text-[#0f172a] leading-none">Riwayat Pengiriman Gaji</h2>
                            <p class="text-xs font-semibold text-slate-400 mt-1.5">
                                Riwayat pembayaran gaji yang telah diselesaikan sebelumnya.
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                        <th class="pb-3 pl-2">Karyawan</th>
                                        <th class="pb-3">Tanggal Bayar</th>
                                        <th class="pb-3">Periode Kerja</th>
                                        <th class="pb-3">Bank & No. Rekening</th>
                                        <th class="pb-3 text-right pr-2">Total Gaji Dibayar</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 text-xs">
                                    @forelse($historyGaji as $history)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="py-4 pl-2 font-extrabold text-[#0f172a]">
                                                {{ $history->pegawai->name ?? $history->pegawai->username ?? 'N/A' }}
                                            </td>
                                            <td class="py-4 font-semibold text-slate-600">
                                                {{ $history->tanggal->format('d M Y') }}
                                            </td>
                                            <td class="py-4 font-semibold text-slate-500">
                                                {{ $history->start_date ? $history->start_date->format('d M Y') : '-' }} s/d {{ $history->end_date ? $history->end_date->format('d M Y') : '-' }}
                                            </td>
                                            <td class="py-4 font-bold text-blue-600">
                                                {{ $history->bank ?? '-' }} (<span class="font-mono text-slate-500 font-semibold">{{ $history->nomor_rekening ?? '-' }}</span>)
                                            </td>
                                            <td class="py-4 text-right pr-2 font-black text-emerald-600 text-sm">
                                                Rp {{ number_format($history->nominal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-6 text-center text-slate-400 font-medium italic">
                                                Belum ada riwayat pengiriman gaji.
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
