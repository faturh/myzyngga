<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kelola Akun - {{ config('app.name', 'Zyngga') }}</title>

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

        /* Native Dialog Backdrop styling */
        dialog::backdrop {
            background-color: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
        }
        
        select {
            background-image: none !important;
            -webkit-appearance: none;
            appearance: none;
        }
        select::-ms-expand { display: none; }
    </style>
</head>
<body class="antialiased h-full overflow-hidden" style="background:#E6F0FF; color:#0F0F0F;" x-data="{ sidebarOpen: false, search: '' }">

    <!-- App Container -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR (Desktop + Mobile) -->
        @include('operator.partials.sidebar')

        <!-- MAIN WINDOW WRAPPER -->
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
            
            <!-- HEADER -->
            @include('operator.partials.header', ['title' => 'Kelola Akun'])

            <!-- CONTENT INNER CONTAINER -->
            <div class="flex-1 overflow-y-auto px-5 py-4 custom-scrollbar" style="background:#E6F0FF;">
                
                <div class="max-w-5xl mx-auto w-full flex flex-col gap-4">

                    <!-- TOP ACTIONS BAR -->
                    <div class="flex flex-col gap-3">
                        @role(["pic", "manajer_laundry", "admin"])
                            <button type="button" onclick="document.getElementById('tambah_user_modal').showModal()" 
                                    class="w-full text-sm font-medium py-3.5 px-4 rounded-full shadow-sm transition-colors text-center border-0 cursor-pointer flex items-center justify-center gap-1.5"
                                    style="background:#003E9C; color:#FFFFFF;"
                                    onmouseover="this.style.background='#002d73'" onmouseout="this.style.background='#003E9C'">
                                <i class="ri-add-fill text-lg"></i>
                                Tambah Akun
                            </button>
                        @endrole

                        <div class="relative w-full">
                            <input type="text" 
                                   x-model="search"
                                   placeholder="Cari nama atau email..." 
                                   class="w-full text-xs font-normal rounded-full pl-10 pr-4 focus:outline-none placeholder:text-[#808080] bg-white border"
                                   style="border-color:#CCCCCC; color:#0F0F0F; height:48px;">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none" style="color:#808080;">
                                <i data-feather="search" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>

                    <!-- USER CARDS LIST -->
                    @php
                        $allUsers = collect();
                        if(isset($manajer)) {
                            $allUsers = $allUsers->concat($manajer);
                        }
                        if(isset($pegawai)) {
                            $allUsers = $allUsers->concat($pegawai);
                        }
                        if(isset($gamis)) {
                            $allUsers = $allUsers->concat($gamis);
                        }
                    @endphp

                    <div class="space-y-4">
                        @forelse($allUsers as $item)
                            <div class="bg-white rounded-lg p-4 flex flex-col gap-3 shadow-sm"
                                 x-show="search === '' || '{{ strtolower($item->nama) }}'.includes(search.toLowerCase()) || '{{ strtolower($item->email) }}'.includes(search.toLowerCase())">
                                
                                <!-- Baris 1: Nama & Role Badge -->
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-medium" style="color:#0F0F0F;">
                                        {{ $item->nama }}
                                    </div>
                                    <span class="text-[10px] font-medium text-white px-2.5 py-1 rounded-full"
                                          style="background: {{ strtolower($item->role) === 'operator' ? '#003E9C' : '#F2994A' }};">
                                        {{ strtolower($item->role) === 'operator' ? 'Operator' : 'Karyawan' }}
                                    </span>
                                </div>

                                <!-- Baris 2: Email -->
                                <div class="flex items-center justify-between text-xs font-normal" style="color:#808080;">
                                    <span>Email</span>
                                    <span>{{ $item->email }}</span>
                                </div>

                                <!-- Baris 3: No. Telepon -->
                                <div class="flex items-center justify-between text-xs font-normal" style="color:#808080;">
                                    <span>No. Telepon</span>
                                    <span>{{ $item->telepon }}</span>
                                </div>

                                <!-- Divider -->
                                <div class="border-t border-[#F4F4F4] my-1"></div>

                                <!-- Baris 4: Actions -->
                                <div class="flex items-center justify-end gap-2 pt-1">
                                    @role(["pic", "manajer_laundry", "operator", "admin"])
                                        <!-- Edit User -->
                                        <button type="button" 
                                                class="edit-user-btn w-9 h-9 rounded-full flex items-center justify-center border hover:bg-slate-50 transition-colors cursor-pointer bg-white"
                                                style="border-color:#F2994A; color:#F2994A;"
                                                title="Edit Pengguna" 
                                                data-slug="{{ $item->slug }}"
                                                data-nama="{{ $item->nama }}"
                                                data-username="{{ $item->username ?? '' }}"
                                                data-email="{{ $item->email ?? '' }}"
                                                data-telepon="{{ $item->telepon }}"
                                                data-gaji="{{ $item->gaji ?? 0 }}"
                                                data-bank="{{ $item->bank ?? '' }}"
                                                data-rekening="{{ $item->nomor_rekening ?? '' }}"
                                                data-role="{{ $item->role ?? '' }}">
                                            <i class="ri-pencil-line text-base"></i>
                                        </button>

                                        @if(strtolower($item->role ?? '') === 'operator')
                                            <!-- Edit Password (Operator Only) -->
                                            <button type="button" 
                                                    class="edit-password-btn w-9 h-9 rounded-full flex items-center justify-center border hover:bg-slate-50 transition-colors cursor-pointer bg-white"
                                                    style="border-color:#9333EA; color:#9333EA;"
                                                    title="Ganti Password"
                                                    data-slug="{{ $item->slug }}"
                                                    data-nama="{{ $item->nama }}">
                                                <i class="ri-lock-line text-base"></i>
                                            </button>
                                        @endif

                                        <!-- Delete User -->
                                        <button type="button" 
                                                onclick="return delete_button('{{ $item->slug }}', '{{ $item->nama }}')"
                                                class="w-9 h-9 rounded-full flex items-center justify-center border hover:bg-slate-50 transition-colors cursor-pointer bg-white"
                                                style="border-color:#EF4444; color:#EF4444;"
                                                title="Hapus Pengguna">
                                            <i class="ri-delete-bin-line text-base"></i>
                                        </button>
                                    @endrole
                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-lg p-6 shadow-sm text-center italic text-slate-400">
                                Belum ada akun pengguna yang terdaftar.
                            </div>
                        @endforelse
                    </div>

                    <!-- MODAL: TAMBAH USER -->
                    <dialog id="tambah_user_modal" class="max-w-2xl w-full bg-white rounded-lg p-6 shadow-xl relative border-0 focus:outline-none">
                        <div class="mb-4 border-b border-[#F4F4F4] pb-3 flex justify-between items-center">
                            <h3 class="text-sm font-medium" style="color:#0F0F0F;">Tambah Akun Baru</h3>
                            <button type="button" onclick="document.getElementById('tambah_user_modal').close()" class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer bg-transparent border-0 text-base">✕</button>
                        </div>
                        <form action="{{ route('user.store') }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="cabang_id" value="{{ \App\Models\Cabang::first()->id ?? 1 }}" />

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5"><span class="text-red-500">*</span> Role / Peran</label>
                                    <div class="relative">
                                        <select name="role" class="w-full bg-white border rounded-full px-4 py-2 text-[#808080] font-normal focus:outline-none appearance-none" style="border-color:#CCCCCC; height:48px;" required>
                                            <option value="" disabled selected>Pilih Role!</option>
                                            <option value="operator">Operator</option>
                                            <option value="pegawai_laundry">Karyawan</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none" style="color:#808080;">
                                            <i data-feather="chevron-down" class="w-4 h-4"></i>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5"><span class="text-red-500">*</span> Nama Lengkap</label>
                                    <input type="text" name="nama" placeholder="Nama lengkap" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" required />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5"><span class="text-red-500">*</span> Username</label>
                                    <input type="text" name="username" placeholder="Username" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" required />
                                </div>
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5"><span class="text-red-500">*</span> Email</label>
                                    <input type="email" name="email" placeholder="Email" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" required />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5"><span class="text-red-500">*</span> Nomor Telepon</label>
                                    <input type="text" name="telepon" placeholder="Nomor Telepon" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" required />
                                </div>
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5">Tarif Gaji per Kg (Rp)</label>
                                    <input type="number" name="gaji" placeholder="Masukkan tarif gaji per kg" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5">Tipe Bank</label>
                                    <input type="text" name="bank" placeholder="Contoh: BCA, Mandiri, BNI" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" />
                                </div>
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5">Nomor Rekening</label>
                                    <input type="text" name="nomor_rekening" placeholder="Nomor Rekening" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5"><span class="text-red-500">*</span> Password</label>
                                    <input type="password" name="password" placeholder="Password" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" required />
                                </div>
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5"><span class="text-red-500">*</span> Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" placeholder="Ulangi password" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" required />
                                </div>
                            </div>

                            <div class="pt-3 flex gap-3 border-t border-[#F4F4F4]">
                                <button type="button" onclick="document.getElementById('tambah_user_modal').close()" class="flex-1 bg-white border hover:bg-slate-50 text-[#808080] font-medium text-xs rounded-full transition-all flex items-center justify-center" style="border-color:#CCCCCC; height:48px;">Batal</button>
                                <button type="submit" class="flex-1 text-white font-medium text-xs rounded-full transition-all border-0 cursor-pointer flex items-center justify-center" style="background:#003E9C; height:48px;">Simpan</button>
                            </div>
                        </form>
                    </dialog>

                    <!-- MODAL: EDIT USER -->
                    <dialog id="edit_user_modal" class="max-w-2xl w-full bg-white rounded-lg p-6 shadow-xl relative border-0 focus:outline-none">
                        <div class="mb-4 border-b border-[#F4F4F4] pb-3 flex justify-between items-center">
                            <h3 class="text-sm font-medium" style="color:#0F0F0F;">Ubah Akun Pengguna</h3>
                            <button type="button" onclick="document.getElementById('edit_user_modal').close()" class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer bg-transparent border-0 text-base">✕</button>
                        </div>
                        <form id="edit_user_form" action="" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" id="edit_cabang_id" name="cabang_id" value="{{ \App\Models\Cabang::first()->id ?? 1 }}" />

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5"><span class="text-red-500">*</span> Role / Peran</label>
                                    <div class="relative">
                                        <select id="edit_role" name="role" class="w-full bg-white border rounded-full px-4 py-2 text-[#808080] font-normal focus:outline-none appearance-none" style="border-color:#CCCCCC; height:48px;" required>
                                            <option value="operator">Operator</option>
                                            <option value="pegawai_laundry">Karyawan</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none" style="color:#808080;">
                                            <i data-feather="chevron-down" class="w-4 h-4"></i>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5"><span class="text-red-500">*</span> Nama Lengkap</label>
                                    <input type="text" id="edit_nama" name="nama" placeholder="Nama lengkap" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" required />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5"><span class="text-red-500">*</span> Username</label>
                                    <input type="text" id="edit_username" name="username" placeholder="Username" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" required />
                                </div>
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5"><span class="text-red-500">*</span> Email</label>
                                    <input type="email" id="edit_email" name="email" placeholder="Email" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" required />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5"><span class="text-red-500">*</span> Nomor Telepon</label>
                                    <input type="text" id="edit_telepon" name="telepon" placeholder="Nomor Telepon" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" required />
                                </div>
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5">Tarif Gaji per Kg (Rp)</label>
                                    <input type="number" id="edit_gaji" name="gaji" placeholder="Masukkan tarif gaji per kg" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5">Tipe Bank</label>
                                    <input type="text" id="edit_bank" name="bank" placeholder="Contoh: BCA, Mandiri, BNI" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" />
                                </div>
                                <div>
                                    <label class="block text-xs font-normal text-[#808080] mb-1.5">Nomor Rekening</label>
                                    <input type="text" id="edit_nomor_rekening" name="nomor_rekening" placeholder="Nomor Rekening" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" />
                                </div>
                            </div>

                            <div class="pt-3 flex gap-3 border-t border-[#F4F4F4]">
                                <button type="button" onclick="document.getElementById('edit_user_modal').close()" class="flex-1 bg-white border hover:bg-slate-50 text-[#808080] font-medium text-xs rounded-full transition-all flex items-center justify-center" style="border-color:#CCCCCC; height:48px;">Batal</button>
                                <button type="submit" class="flex-1 text-white font-medium text-xs rounded-full transition-all border-0 cursor-pointer flex items-center justify-center" style="background:#003E9C; height:48px;">Simpan Perubahan</button>
                            </div>
                        </form>
                    </dialog>

                    <!-- MODAL: UBAH PASSWORD -->
                    <dialog id="ubah_password_modal" class="max-w-md w-full bg-white rounded-lg p-6 shadow-xl relative border-0 focus:outline-none">
                        <div class="mb-4 border-b border-[#F4F4F4] pb-3 flex justify-between items-center">
                            <h3 class="text-sm font-medium" style="color:#0F0F0F;">Ubah Password</h3>
                            <button type="button" onclick="document.getElementById('ubah_password_modal').close()" class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer bg-transparent border-0 text-base">✕</button>
                        </div>
                        <form id="ubah_password_form" action="" method="POST" class="space-y-3">
                            @csrf
                            
                            <div>
                                <label class="block text-xs font-normal text-[#808080] mb-1.5">Nama Pengguna</label>
                                <input type="text" id="pass_nama" class="w-full bg-slate-100 border rounded-full px-4 py-2 text-[#808080] font-medium focus:outline-none" style="border-color:#CCCCCC; height:48px;" readonly />
                            </div>

                            <div>
                                <label class="block text-xs font-normal text-[#808080] mb-1.5"><span class="text-red-500">*</span> Password Baru</label>
                                <input type="password" name="password" placeholder="Masukkan password baru" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" required />
                            </div>

                            <div>
                                <label class="block text-xs font-normal text-[#808080] mb-1.5"><span class="text-red-500">*</span> Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="w-full bg-white border rounded-full px-4 py-2 text-[#0F0F0F] font-normal focus:outline-none" style="border-color:#CCCCCC; height:48px;" required />
                            </div>

                            <div class="pt-3 flex gap-3 border-t border-[#F4F4F4]">
                                <button type="button" onclick="document.getElementById('ubah_password_modal').close()" class="flex-1 bg-white border hover:bg-slate-50 text-[#808080] font-medium text-xs rounded-full transition-all flex items-center justify-center" style="border-color:#CCCCCC; height:48px;">Batal</button>
                                <button type="submit" class="flex-1 text-white font-medium text-xs rounded-full transition-all border-0 cursor-pointer flex items-center justify-center" style="background:#003E9C; height:48px;">Ganti Password</button>
                            </div>
                        </form>
                    </dialog>

                </div>
            </div>
        </div>
    </div>

    <!-- CDNs and Scripts -->
    <!-- Load jQuery first -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <!-- Load SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Edit User button handler
            $(document).on('click', '.edit-user-btn', function() {
                var slug = $(this).data('slug');
                var nama = $(this).data('nama');
                var username = $(this).data('username');
                var email = $(this).data('email');
                var telepon = $(this).data('telepon');
                var gaji = $(this).data('gaji');
                var role = $(this).data('role');
                var bank = $(this).data('bank');
                var rekening = $(this).data('rekening');

                $('#edit_nama').val(nama);
                $('#edit_username').val(username);
                $('#edit_email').val(email);
                $('#edit_telepon').val(telepon);
                $('#edit_gaji').val(gaji);
                $('#edit_role').val(role);
                $('#edit_bank').val(bank);
                $('#edit_nomor_rekening').val(rekening);

                var actionUrl = "{{ route('user.update', ':user') }}".replace(':user', slug);
                $('#edit_user_form').attr('action', actionUrl);

                document.getElementById('edit_user_modal').showModal();
            });

            // Edit Password button handler
            $(document).on('click', '.edit-password-btn', function() {
                var slug = $(this).data('slug');
                var nama = $(this).data('nama');

                $('#pass_nama').val(nama);

                var actionUrl = "{{ route('user.update.password', ':slug') }}".replace(':slug', slug);
                $('#ubah_password_form').attr('action', actionUrl);

                document.getElementById('ubah_password_modal').showModal();
            });
        });

        @if (session()->has("success"))
            Swal.fire({
                title: 'Berhasil',
                text: '{{ session("success") }}',
                icon: 'success',
                confirmButtonColor: '#003E9C',
                confirmButtonText: 'OK',
            });
        @endif

        @if (session()->has("error"))
            Swal.fire({
                title: 'Gagal',
                text: '{{ session("error") }}',
                icon: 'error',
                confirmButtonColor: '#003E9C',
                confirmButtonText: 'OK',
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                title: 'Gagal',
                html: '<div class="text-left"><ul class="list-disc pl-5 space-y-1 text-sm text-red-600">' +
                      @foreach ($errors->all() as $error)
                          '<li>{{ $error }}</li>' +
                      @endforeach
                      '</ul></div>',
                icon: 'error',
                confirmButtonColor: '#003E9C',
                confirmButtonText: 'OK',
            });
        @endif

        function delete_button(slug, nama) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                html: "<p>Data akan dihapus!</p>" +
                    "<div class='divider'></div>" +
                    "<b>Data: " + nama + "</b>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#003E9C',
                cancelButtonColor: '#EF4444',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "{{ route('user.delete') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "slug": slug
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Data berhasil dihapus!',
                                icon: 'success',
                                confirmButtonColor: '#003E9C',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        },
                        error: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Data gagal dihapus!',
                            })
                        }
                    });
                }
            })
        }
    </script>

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
