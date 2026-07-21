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

        body, input, select, textarea, button {
            font-family: 'DM Sans', sans-serif;
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        select {
            background-image: none !important;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        select::-ms-expand { display: none; }

        .relative-input-container input::-webkit-calendar-picker-indicator {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100%; height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 10;
        }
        .relative-input-container { position: relative; }
    </style>
</head>
<body
    class="antialiased h-full overflow-hidden"
    style="background:#E6F0FF; color:#0F0F0F;"
    x-data="{
        sidebarOpen: false,
        modalOpen: false,
        deleteModalOpen: false,
        deleteAction: '',
        deleteLabel: '',
        openDeleteModal(action, label) {
            this.deleteAction = action;
            this.deleteLabel = label;
            this.deleteModalOpen = true;
        },
        closeDeleteModal() {
            this.deleteModalOpen = false;
            this.deleteAction = '';
            this.deleteLabel = '';
        },
        confirmDelete() {
            document.getElementById('deleteForm').submit();
        }
    }"
>

    <!-- App Container -->
    <div class="flex h-screen overflow-hidden">

        <!-- SIDEBAR (Desktop + Mobile) -->
        @include('operator.partials.sidebar')

        <!-- MAIN WINDOW WRAPPER -->
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden">

            <!-- HEADER : bg white, h-48px, padding 0 16px, gap 16px -->
            <header class="h-12 bg-white flex items-center gap-4 px-4 sticky top-0 z-30 shrink-0">
                <button @click="sidebarOpen = true" class="lg:hidden p-1 text-[#0F0F0F] hover:opacity-70 transition-opacity">
                    <i data-feather="menu" class="w-5 h-5"></i>
                </button>
                <h1 class="text-sm font-medium flex-1" style="color:#0F0F0F;">Keuangan Toko</h1>
                <img src="/images/MyZyngga_avatar.png" alt="MyZyngga" class="w-6 h-6 rounded-full object-cover" style="border:0.5px solid #0F0F0F;">
            </header>

            <!-- CONTENT INNER CONTAINER -->
            <div class="flex-1 overflow-y-auto px-5 py-4 custom-scrollbar" style="background:#E6F0FF;">

                <div class="max-w-5xl mx-auto w-full flex flex-col gap-4">

                    <!-- Alerts -->
                    @if(session('success'))
                        <div class="bg-white text-xs font-medium px-4 py-3 rounded-lg flex items-center gap-2" style="color:#10B981;">
                            <i data-feather="check-circle" class="w-4 h-4 shrink-0" style="color:#10B981;"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-white text-xs font-medium px-4 py-3 rounded-lg space-y-1" style="color:#EF4444;">
                            @foreach($errors->all() as $error)
                                <div class="flex items-center gap-2">
                                    <i data-feather="alert-circle" class="w-4 h-4 shrink-0" style="color:#EF4444;"></i>
                                    <span>{{ $error }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- 1. BUTTON CATAT KAS MANUAL : bg #003E9C, radius 100, h-48, text 14/500 white -->
                    <button
                        @click="modalOpen = true"
                        class="w-full text-sm font-medium py-3.5 px-4 rounded-full shadow-sm transition-colors text-center"
                        style="background:#003E9C; color:#FFFFFF;"
                        onmouseover="this.style.background='#002d73'" onmouseout="this.style.background='#003E9C'"
                    >
                        + Catat Kas Manual
                    </button>

                    <!-- 2. CARD SALDO : bg #10B981, radius 8, padding 16, gap 8 -->
                    <div class="p-4 rounded-lg" style="background:#10B981;">
                        <span class="text-sm font-medium block" style="color:#FFFFFF;">Total Saldo Toko</span>
                        <h3 class="text-2xl font-medium mt-2" style="color:#FFFFFF;">Rp {{ number_format($saldoToko, 0, ',', '.') }}</h3>
                    </div>

                    <!-- 3. RINGKASAN KAS : gap 16 -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Card Pemasukan : bg white/80, radius 8, padding 12 16 -->
                        <div class="bg-white/80 py-3 px-4 rounded-lg flex flex-col items-center text-center gap-2">
                            <span class="text-xs font-normal" style="color:#808080;">Total Pemasukan</span>
                            <h3 class="text-base font-normal" style="color:#10B981;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
                        </div>

                        <!-- Card Pengeluaran -->
                        <div class="bg-white/80 py-3 px-4 rounded-lg flex flex-col items-center text-center gap-2">
                            <span class="text-xs font-normal" style="color:#808080;">Total Pengeluaran</span>
                            <h3 class="text-base font-normal" style="color:#EF4444;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
                        </div>
                    </div>

                    <!-- Split layout: filter + riwayat -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">

                        <!-- 4. CARD FILTER : bg white, radius 8, padding 16, gap 16 -->
                        <div class="bg-white p-4 rounded-lg md:col-span-1">
                            <form method="GET" action="{{ route('admin.keuangan') }}" class="flex flex-col gap-4">

                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium" style="color:#000000;">Jenis Filter</label>
                                    <div class="relative">
                                        <select name="filter_type" id="filter_type"
                                            class="w-full text-xs font-normal rounded-full px-4 py-3 focus:outline-none appearance-none"
                                            style="border:1px solid #CCCCCC; color:#808080; height:48px;">
                                            <option value="daily" {{ $filterType === 'daily' ? 'selected' : '' }}>Harian</option>
                                            <option value="weekly" {{ $filterType === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                            <option value="monthly" {{ $filterType === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center" style="color:#808080;">
                                            <i data-feather="chevron-down" class="w-4 h-4"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium" style="color:#000000;">Pilih Waktu</label>
                                    <div class="relative relative-input-container">
                                        <input type="date" name="date_value" id="date_value"
                                            value="{{ $filterType === 'daily' ? $dateValue : '' }}"
                                            class="w-full text-xs font-normal rounded-full px-4 py-3 focus:outline-none"
                                            style="border:1px solid #CCCCCC; color:#808080; height:48px;">
                                        <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center" style="color:#808080;">
                                            <i data-feather="calendar" class="w-4 h-4"></i>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="w-full text-xs font-normal rounded-full text-center"
                                    style="background:#003E9C; color:#FFFFFF; height:48px;">
                                    Terapkan Filter
                                </button>
                            </form>
                        </div>

                        <!-- 5. CARD RIWAYAT TRANSAKSI : bg white, radius 8, padding 16, gap 16 -->
                        <div class="bg-white rounded-lg p-4 flex flex-col gap-4 md:col-span-2">
                            <div>
                                <h3 class="text-sm font-medium" style="color:#000000;">Riwayat Transaksi</h3>
                                <p class="text-[10px] font-normal mt-0.5" style="color:#808080;">Periode: {{ $startDate }} s.d. {{ $endDate }}</p>
                            </div>

                            <div class="overflow-x-auto custom-scrollbar">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr style="border-bottom:1px solid #F4F4F4;">
                                            <th class="hidden">Tanggal</th>
                                            <th class="hidden">Sumber</th>
                                            <th class="pb-2 text-xs font-medium" style="color:#000000;">Kategori</th>
                                            <th class="pb-2 text-xs font-medium" style="color:#000000;">Keterangan</th>
                                            <th class="pb-2 text-xs font-medium text-center" style="color:#000000;">Jumlah</th>
                                            <th class="pb-2 text-xs font-medium text-right" style="color:#000000;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-xs">
                                        @forelse($records as $rec)
                                            <tr style="border-bottom:1px solid #F4F4F4;">
                                                <td class="hidden">{{ $rec['tanggal'] }}</td>
                                                <td class="hidden">{{ $rec['source'] }}</td>

                                                <td class="py-3 font-normal" style="color:#808080;">
                                                    {{ $rec['kategori'] ?: ($rec['tipe'] === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran') }}
                                                </td>

                                                <td class="py-3 font-normal" style="color:#808080;">
                                                    {{ $rec['keterangan'] ?: '-' }}
                                                </td>

                                                <td class="py-3 text-center font-normal whitespace-nowrap" style="color:#808080;">
                                                    Rp {{ number_format($rec['nominal'], 0, ',', '.') }}
                                                </td>

                                                <td class="py-3 text-right">
                                                    @if($rec['source'] === 'manual')
                                                        <button
                                                            type="button"
                                                            @click="openDeleteModal('{{ route('admin.keuangan.destroy', $rec['id']) }}', '{{ addslashes($rec['keterangan'] ?: ($rec['kategori'] ?: 'catatan ini')) }}')"
                                                            class="p-1.5 rounded-lg transition-colors cursor-pointer inline-flex items-center justify-center border-0 bg-transparent"
                                                            style="color:#EF4444;"
                                                        >
                                                            <i data-feather="trash-2" class="w-5 h-5 pointer-events-none" stroke-width="2.5"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-[10px] font-normal italic" style="color:#CCCCCC;">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-12 text-center font-normal" style="color:#808080;">
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
    </div>

    <!-- OVERLAY + MODAL CATAT KAS MANUAL -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak role="dialog" aria-modal="true">
        <!-- Overlay: rgba(0,0,0,0.5) -->
        <div class="fixed inset-0" style="background:rgba(0,0,0,0.5);" @click="modalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <!-- Modal Container: w-340, padding 20, gap 16, radius 16, shadow -->
            <div
                class="relative w-full max-w-[340px] bg-white p-5 flex flex-col gap-4"
                style="border-radius:16px; box-shadow:0px 8px 24px rgba(0,0,0,0.1);"
                @click.away="modalOpen = false"
            >
                <!-- Header Form -->
                <h3 class="text-sm font-medium" style="color:#000000;">Catat Kas Manual</h3>

                <form method="POST" action="{{ route('admin.keuangan.store') }}" class="flex flex-col gap-4">
                    @csrf

                    <!-- Field: Tipe Transaksi -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-medium" style="color:#000000;">Tipe Transaksi</label>
                        <div class="relative">
                            <select name="tipe" required
                                class="w-full text-xs font-normal rounded-full px-4 focus:outline-none appearance-none"
                                style="border:1px solid #CCCCCC; opacity:0.9; color:#808080; height:48px;">
                                <option value="pengeluaran">Pengeluaran</option>
                                <option value="pemasukan">Pemasukan</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center" style="color:#1E1E1E;">
                                <i data-feather="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Field: Kategori -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-medium" style="color:#000000;">Kategori</label>
                        <input type="text" name="kategori" placeholder="Kas Masuk" required
                            class="w-full text-xs font-normal rounded-full px-4 focus:outline-none"
                            style="border:1px solid #CCCCCC; opacity:0.9; color:#808080; height:48px;">
                    </div>

                    <!-- Field: Tanggal -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-medium" style="color:#000000;">Tanggal</label>
                        <div class="relative relative-input-container">
                            <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}"
                                class="w-full text-xs font-normal rounded-full px-4 focus:outline-none"
                                style="border:1px solid #CCCCCC; opacity:0.9; color:#808080; height:48px;">
                            <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center" style="color:#808080;">
                                <i data-feather="calendar" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Field: Nominal -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-medium" style="color:#000000;">Nominal</label>
                        <input type="number" name="nominal" min="1" placeholder="50000" required
                            class="w-full text-xs font-normal rounded-full px-4 focus:outline-none"
                            style="border:1px solid #CCCCCC; opacity:0.9; color:#808080; height:48px;">
                    </div>

                    <!-- Field: Keterangan -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-medium" style="color:#000000;">Keterangan</label>
                        <input type="text" name="keterangan" placeholder="Pembayaran token"
                            class="w-full text-xs font-normal rounded-full px-4 focus:outline-none"
                            style="border:1px solid #CCCCCC; opacity:0.9; color:#808080; height:48px;">
                    </div>

                    <!-- Footer buttons: gap 12, h-48, radius 100 -->
                    <div class="flex items-center gap-3 pt-1">
                        <button type="button" @click="modalOpen = false"
                            class="flex-1 text-sm font-medium rounded-full transition-all"
                            style="background:#FFFFFF; border:1px solid #003E9C; color:#003E9C; height:48px;">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 text-sm font-medium rounded-full transition-all"
                            style="background:#003E9C; border:1px solid #003E9C; color:#FFFFFF; height:48px;">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- OVERLAY + MODAL KONFIRMASI HAPUS (custom, tidak pakai confirm() native) -->
    <div x-show="deleteModalOpen" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak role="dialog" aria-modal="true">
        <div class="fixed inset-0" style="background:rgba(0,0,0,0.5);" @click="closeDeleteModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div
                class="relative w-full max-w-[300px] bg-white p-5 flex flex-col gap-4 text-center"
                style="border-radius:16px; box-shadow:0px 8px 24px rgba(0,0,0,0.1);"
                @click.away="closeDeleteModal()"
            >
                <div class="flex items-center justify-center w-10 h-10 rounded-full mx-auto" style="background:rgba(239,68,68,0.1);">
                    <i data-feather="alert-triangle" class="w-5 h-5" style="color:#EF4444;"></i>
                </div>

                <div class="space-y-1">
                    <h3 class="text-sm font-medium" style="color:#000000;">Hapus Catatan Manual?</h3>
                    <p class="text-xs" style="color:#808080;" x-text="'Anda akan menghapus: ' + deleteLabel"></p>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" @click="closeDeleteModal()"
                        class="flex-1 text-sm font-medium rounded-full transition-all"
                        style="background:#FFFFFF; border:1px solid #003E9C; color:#003E9C; height:48px;">
                        Batal
                    </button>
                    <button type="button" @click="confirmDelete()"
                        class="flex-1 text-sm font-medium rounded-full transition-all"
                        style="background:#EF4444; border:1px solid #EF4444; color:#FFFFFF; height:48px;">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Form tersembunyi yang di-submit dinamis oleh modal konfirmasi hapus -->
    <form id="deleteForm" method="POST" :action="deleteAction" class="hidden">
        @csrf
        @method('DELETE')
    </form>

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
                    if (dateValueInput.value && dateValueInput.value.length < 10) {
                        dateValueInput.value = new Date().toISOString().split('T')[0];
                    }
                } else if (val === 'weekly') {
                    dateValueInput.type = 'week';
                } else if (val === 'monthly') {
                    dateValueInput.type = 'month';
                }
            }

            if (filterTypeSelect && dateValueInput) {
                filterTypeSelect.addEventListener('change', adjustInputType);

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
            }

            document.addEventListener('alpine:updated', function() {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            });
        });
    </script>
</body>
</html>