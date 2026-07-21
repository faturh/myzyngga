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
<body class="antialiased h-full overflow-hidden" style="background:#E6F0FF; color:#0F0F0F;" x-data="{ sidebarOpen: false }">

    <!-- App Container -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR (Desktop + Mobile) -->
        @include('operator.partials.sidebar')

        <!-- MAIN WINDOW WRAPPER -->
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
            
            <!-- HEADER -->
            @include('operator.partials.header', ['title' => 'Gaji Karyawan'])

            <!-- CONTENT INNER CONTAINER -->
            <div class="flex-1 overflow-y-auto px-5 py-4 custom-scrollbar" style="background:#E6F0FF;">
                
                <div class="max-w-5xl mx-auto w-full flex flex-col gap-4">
                    
                    <!-- Date Filter Form -->
                    <form method="GET" action="{{ route('admin.gaji-karyawan') }}" class="bg-white rounded-lg p-4 shadow-sm flex flex-col gap-3">
                        <div class="text-sm font-medium mb-1" style="color:#0F0F0F;">Filter Periode Rekapitulasi Gaji</div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-normal text-[#808080] mb-1.5">Pilih Karyawan</label>
                                <div class="relative">
                                    <select name="pegawai_id" class="w-full bg-white border rounded-full px-4 py-2 text-[#808080] font-normal focus:outline-none appearance-none" style="border-color:#CCCCCC; height:48px;">
                                        <option value="">Semua Karyawan</option>
                                        @foreach ($allKaryawan as $emp)
                                            <option value="{{ $emp->id }}" @if ($selectedEmployeeId == $emp->id) selected @endif>
                                                {{ $emp->name ?? $emp->username }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none" style="color:#808080;">
                                        <i data-feather="chevron-down" class="w-4 h-4"></i>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-normal text-[#808080] mb-1.5">Tanggal Mulai</label>
                                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="w-full bg-white border rounded-full px-4 py-2 text-[#808080] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" />
                            </div>
                            <div>
                                <label class="block text-xs font-normal text-[#808080] mb-1.5">Tanggal Selesai</label>
                                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="w-full bg-white border rounded-full px-4 py-2 text-[#808080] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" />
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-1">
                            <button type="submit" class="w-full text-white font-medium text-xs rounded-full transition-all border-0 cursor-pointer flex items-center justify-center gap-1.5" style="background:#003E9C; height:48px;">
                                Filter
                            </button>
                            <a href="{{ route('admin.gaji-karyawan.download', ['start_date' => $startDate, 'end_date' => $endDate, 'pegawai_id' => $selectedEmployeeId]) }}" 
                               class="w-full bg-white border hover:bg-slate-50 text-[#003E9C] font-medium text-xs rounded-full transition-all flex items-center justify-center gap-1.5"
                               style="border-color:#003E9C; height:48px; border-width:1px;">
                                Unduh Excel
                            </a>
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
                    <div class="w-full space-y-4"
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

                        <!-- Employees Cards Loop -->
                        <template x-for="emp in sortedEmployees" :key="emp.id">
                            <div class="bg-white rounded-lg p-4 flex flex-col gap-3 shadow-sm">
                                <!-- Baris 1: Nama Karyawan -->
                                <div class="text-sm font-medium" style="color:#0F0F0F;" x-text="emp.name"></div>

                                <!-- Baris 2: Detail Rekening Bank -->
                                <div class="text-xs font-normal" style="color:#808080;">
                                    <span x-text="emp.bank"></span> – <span x-text="emp.nomor_rekening"></span>
                                </div>

                                <!-- Baris 3: Total Pengerjaan -->
                                <div class="flex items-center justify-between text-xs font-normal" style="color:#808080;">
                                    <span>Total Pengerjaan</span>
                                    <span x-text="emp.total_kg + ' kg'"></span>
                                </div>

                                <!-- Baris 4: Tarif per Kg -->
                                <div class="flex items-center justify-between text-xs font-normal" style="color:#808080;">
                                    <span>Tarif per Kg</span>
                                    <span x-text="'Rp ' + emp.gaji_per_kg.toLocaleString('id-ID')"></span>
                                </div>

                                <!-- Garis Pemisah (Divider) -->
                                <div class="border-t border-[#F4F4F4] my-1"></div>

                                <!-- Baris 5: Total Gaji Diterima & Tombol Aksi -->
                                <div class="flex items-center justify-between pt-1">
                                    <div>
                                        <p class="text-[12px] font-normal" style="color:#808080;">Total Gaji Diterima</p>
                                        <p class="text-xs font-medium text-[#0F0F0F]" x-text="'Rp ' + emp.total_gaji.toLocaleString('id-ID')"></p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button @click="openTarif(emp)" type="button" class="text-center text-white px-5 py-2 rounded-full text-xs font-medium shadow-sm transition-all border-0 cursor-pointer flex items-center justify-center gap-1.5" style="background:#F2994A; height:38px;">
                                            Atur Tarif
                                        </button>
                                        <button @click="openBayar(emp)" type="button" class="text-center text-white px-5 py-2 rounded-full text-xs font-medium shadow-sm transition-all border-0 cursor-pointer flex items-center justify-center gap-1.5" style="background:#003E9C; height:38px;">
                                            Bayar Gaji
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Modal Bayar Gaji -->
                        <div x-show="showBayarModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm" @click="showBayarModal = false"></div>
                                
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                
                                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100">
                                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                                        <h3 class="text-sm font-medium text-[#0f172a] uppercase tracking-wider">Bayar Gaji Karyawan</h3>
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
                                            <label class="block text-[10px] font-medium text-slate-400 uppercase mb-1.5">Nama Karyawan</label>
                                            <input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-500 font-medium" :value="selectedEmp?.name" readonly>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[10px] font-medium text-slate-400 uppercase mb-1.5">Bank</label>
                                                <input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-500 font-medium" :value="selectedEmp?.bank" readonly>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-medium text-slate-400 uppercase mb-1.5">No. Rekening</label>
                                                <input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-500 font-medium" :value="selectedEmp?.nomor_rekening" readonly>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-[10px] font-medium text-slate-400 uppercase mb-1.5">Tanggal Pembayaran</label>
                                            <input type="date" name="tanggal" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 font-medium focus:outline-none focus:border-blue-500" x-model="payoutDate" required>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-[10px] font-medium text-slate-400 uppercase mb-1.5">Nominal Payout (Rp)</label>
                                            <input type="number" name="nominal" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 font-medium focus:outline-none focus:border-blue-500" x-model="payoutAmount" required>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-[10px] font-medium text-slate-400 uppercase mb-1.5">Catatan / Keterangan</label>
                                            <textarea name="keterangan" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 font-medium focus:outline-none focus:border-blue-500" x-model="payoutNote" required></textarea>
                                        </div>
                                        
                                        <div class="pt-2 flex gap-3">
                                            <button type="button" @click="showBayarModal = false" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs py-2.5 rounded-xl transition-all">Batal</button>
                                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs py-2.5 rounded-xl transition-all shadow-sm">Konfirmasi</button>
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
                                        <h3 class="text-sm font-medium text-[#0f172a] uppercase tracking-wider">Atur Tarif Gaji per Kg</h3>
                                        <button @click="showTarifModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.gaji-karyawan.update-tarif') }}" method="POST" class="p-6 space-y-4">
                                        @csrf
                                        <input type="hidden" name="pegawai_id" :value="selectedEmp?.id">
                                        
                                        <div>
                                            <label class="block text-[10px] font-medium text-slate-400 uppercase mb-1.5">Nama Karyawan</label>
                                            <input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-500 font-medium" :value="selectedEmp?.name" readonly>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-[10px] font-medium text-slate-400 uppercase mb-1.5">Tarif Gaji Baru (Rp / kg)</label>
                                            <input type="number" name="gaji" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 font-medium focus:outline-none focus:border-blue-500" x-model="tarifAmount" required>
                                        </div>
                                        
                                        <div class="pt-2 flex gap-3">
                                            <button type="button" @click="showTarifModal = false" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs py-2.5 rounded-xl transition-all">Batal</button>
                                            <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white font-medium text-xs py-2.5 rounded-xl transition-all shadow-sm">Simpan Tarif</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Pengiriman Gaji Card -->
                    <div class="w-full space-y-6 bg-white border border-slate-100 p-6 rounded-2xl shadow-sm hidden">
                        <div class="border-b border-slate-100 pb-5">
                            <h2 class="text-lg font-medium text-[#0f172a] leading-none">Riwayat Pengiriman Gaji</h2>
                            <p class="text-xs font-medium text-slate-400 mt-1.5">
                                Riwayat pembayaran gaji yang telah diselesaikan sebelumnya.
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 text-[11px] font-medium text-slate-400 uppercase tracking-wider">
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
                                            <td class="py-4 pl-2 font-medium text-[#0f172a]">
                                                {{ $history->pegawai->name ?? $history->pegawai->username ?? 'N/A' }}
                                            </td>
                                            <td class="py-4 font-medium text-slate-600">
                                                {{ $history->tanggal->format('d M Y') }}
                                            </td>
                                            <td class="py-4 font-medium text-slate-500">
                                                {{ $history->start_date ? $history->start_date->format('d M Y') : '-' }} s/d {{ $history->end_date ? $history->end_date->format('d M Y') : '-' }}
                                            </td>
                                            <td class="py-4 font-medium text-blue-600">
                                                {{ $history->bank ?? '-' }} (<span class="font-mono text-slate-500 font-medium">{{ $history->nomor_rekening ?? '-' }}</span>)
                                            </td>
                                            <td class="py-4 text-right pr-2 font-medium text-emerald-600 text-sm">
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
