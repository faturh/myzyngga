@extends("operator.partials.layout")

@section("css")
    <!-- Remix Icon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <!-- DataTables CSS CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.dataTables.css" />
@endsection

@section("js")
    <!-- Load jQuery first -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Load DataTables JS -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.dataTables.js"></script>
    <!-- Load SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr',
                    },
                },
                order: [],
                pagingType: 'full_numbers',
            });

            $('#myTable1').DataTable({
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr',
                    },
                },
                order: [],
                pagingType: 'full_numbers',
            });

            $('#myTable2').DataTable({
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr',
                    },
                },
                order: [],
                pagingType: 'full_numbers',
            });

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
                confirmButtonColor: '#6419E6',
                confirmButtonText: 'OK',
            });
        @endif

        @if (session()->has("error"))
            Swal.fire({
                title: 'Gagal',
                text: '{{ session("error") }}',
                icon: 'error',
                confirmButtonColor: '#6419E6',
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
                confirmButtonColor: '#6419E6',
                confirmButtonText: 'OK',
            });
        @endif

        function delete_button(slug, nama) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                html: "<p>Data akan masuk ke dalam Trash!</p>" +
                    "<div class='divider'></div>" +
                    "<b>Data: " + nama + "</b>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6419E6',
                cancelButtonColor: '#F87272',
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
                                confirmButtonColor: '#6419E6',
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

        function restore_button(slug, nama) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                html: "<p>Data akan dipulihkan!</p>" +
                    "<div class='divider'></div>" +
                    "<b>Data: " + nama + "</b>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6419E6',
                cancelButtonColor: '#F87272',
                confirmButtonText: 'Pulihkan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "{{ route('user.restore') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "slug": slug
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Data berhasil dipulihkan!',
                                icon: 'success',
                                confirmButtonColor: '#6419E6',
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
                                title: 'Data gagal dipulihkan!',
                            })
                        }
                    });
                }
            })
        }

        function destroy_button(slug, nama) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                html: "<p>Data yang dihapus permanen tidak dapat dipulihkan kembali!</p>" +
                    "<div class='divider'></div>" +
                    "<b>Data: " + nama + "</b>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6419E6',
                cancelButtonColor: '#F87272',
                confirmButtonText: 'Hapus Permanen',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "{{ route('user.destroy') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "slug": slug
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Data berhasil dihapus permanen!',
                                icon: 'success',
                                confirmButtonColor: '#6419E6',
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
                                title: 'Data gagal dihapus permanen!',
                            })
                        }
                    });
                }
            })
        }

        function export_button(cabang) {
            Swal.fire({
                title: 'Apakah Anda ingin mengunduh data?',
                html: "<p>Data yang diunduh berupa file Excel</p>",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#6419E6',
                cancelButtonColor: '#F87272',
                confirmButtonText: 'Unduh',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "get",
                        url: "{{ route('user.export') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "cabang": cabang
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Data berhasil diunduh!',
                                icon: 'success',
                                confirmButtonColor: '#6419E6',
                                confirmButtonText: 'OK'
                            });
                        },
                        error: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Data gagal diunduh!',
                            })
                        }
                    });
                }
            })
        }
    </script>
@endsection

@section("content")
    <div class="-mx-3 flex flex-wrap">
        <div class="w-full max-w-full flex-none px-3">




            {{-- Awal Tabel User --}}
            <div class="dark:bg-slate-850 dark:shadow-dark-xl relative mb-6 flex min-w-0 flex-col break-words rounded-2xl border-0 border-solid border-transparent bg-white bg-clip-border shadow-xl">
                <div class="border-b-solid mb-0 flex items-center justify-between rounded-t-2xl border-b-0 border-b-transparent p-6 pb-3">
                    <h6 class="font-bold dark:text-white">{{ $title }}</h6>
                    <div class="w-1/2 max-w-full flex-none px-3 text-right">
                        @role(["pic", "manajer_laundry", "admin"])
                            <button type="button" onclick="tambah_user_modal.showModal()" class="bg-150 active:opacity-85 tracking-tight-rem bg-x-25 mb-0 inline-block cursor-pointer rounded-lg border border-solid border-blue-500 bg-blue-500 hover:bg-blue-600 px-4 py-2 text-center align-middle text-sm font-bold leading-normal text-white shadow-md transition-all ease-in hover:-translate-y-px hover:opacity-75">
                                <i class="ri-add-fill"></i> Tambah Akun
                            </button>
                        @endrole
                    </div>
                </div>
                <div class="flex-auto px-0 pb-2 pt-0">
                    <div class="overflow-x-auto p-0 px-6 pb-6">
                        <table id="myTable" class="nowrap stripe mb-3 w-full max-w-full border-collapse items-center align-top text-slate-500 dark:border-white/40" style="width: 100%;">
                            <thead class="align-bottom">
                                <tr>
                                    <th class="rounded-tl bg-blue-500 text-xs font-bold uppercase text-white dark:text-white">
                                        Nama
                                    </th>
                                    <th class="bg-blue-500 text-xs font-bold uppercase text-white dark:text-white">
                                        Email
                                    </th>
                                    <th class="bg-blue-500 text-xs font-bold uppercase text-white dark:text-white">
                                        Telepon
                                    </th>
                                    <th class="bg-blue-500 text-xs font-bold uppercase text-white dark:text-white">
                                        Role
                                    </th>
                                    <th class="bg-blue-500 text-xs font-bold uppercase text-white dark:text-white">
                                        Created_at
                                    </th>
                                     <th class="rounded-tr bg-blue-500 text-xs font-bold uppercase text-white dark:text-white py-3">
                                         Aksi
                                     </th>
                                </tr>
                            </thead>
                            <tbody>
                                @role(["lurah", "operator", "pic", "admin"])
                                    @role(["lurah", "pic", "admin"])
                                        @foreach ($manajer as $item)
                                            <tr>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                    <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                        {{ $item->nama }}
                                                    </p>
                                                </td>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                    <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                        {{ $item->user->email }}
                                                    </p>
                                                </td>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                    <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                        {{ $item->telepon }}
                                                    </p>
                                                </td>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                    <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                        {{ $item->user->roles->pluck("name")->first() }}
                                                    </p>
                                                </td>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                    <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                        {{ Carbon\Carbon::parse($item->created_at)->translatedFormat("d F Y") }}
                                                    </p>
                                                </td>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                     <div class="flex items-center gap-1.5 justify-center py-1">
                                                         <a href="{{ route("user.view", $item->slug) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition-colors" title="Lihat Detail">
                                                             <i class="ri-eye-line text-lg"></i>
                                                         </a>
                                                         @role(["pic", "manajer_laundry", "operator", "admin"])
                                                             <button type="button" class="edit-user-btn inline-flex items-center justify-center w-8 h-8 rounded-lg border border-amber-200 text-amber-600 hover:bg-amber-50 transition-colors" title="Edit Pengguna" 
                                                                     data-slug="{{ $item->slug }}"
                                                                     data-nama="{{ $item->nama }}"
                                                                     data-username="{{ $item->username ?? '' }}"
                                                                     data-email="{{ $item->user->email ?? '' }}"
                                                                     data-telepon="{{ $item->telepon }}"
                                                                     data-gaji="{{ $item->gaji ?? 0 }}"
                                                                     data-bank="{{ $item->bank ?? '' }}"
                                                                     data-rekening="{{ $item->nomor_rekening ?? '' }}"
                                                                     data-role="{{ $item->user->roles->pluck('name')->first() ?? '' }}"
                                                                     data-cabang="{{ $item->cabang_id ?? '' }}">
                                                                 <i class="ri-pencil-line text-lg"></i>
                                                             </button>
                                                             <button type="button" class="edit-password-btn inline-flex items-center justify-center w-8 h-8 rounded-lg border border-purple-200 text-purple-600 hover:bg-purple-50 transition-colors" title="Ganti Password"
                                                                     data-slug="{{ $item->slug }}"
                                                                     data-nama="{{ $item->nama }}">
                                                                 <i class="ri-lock-line text-lg"></i>
                                                             </button>
                                                             <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition-colors" title="Hapus Pengguna" onclick="return delete_button('{{ $item->slug }}', '{{ $item->nama }}')">
                                                                 <i class="ri-delete-bin-line text-lg"></i>
                                                             </button>
                                                         @endrole
                                                     </div>
                                                 </td>
                                            </tr>
                                        @endforeach
                                    @endrole

                                    @foreach ($pegawai as $item)
                                        <tr>
                                            <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                    {{ $item->nama }}
                                                </p>
                                            </td>
                                            <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                    {{ $item->user->email }}
                                                </p>
                                            </td>
                                            <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                    {{ $item->telepon }}
                                                </p>
                                            </td>
                                            <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                    {{ $item->user->roles->pluck("name")->first() }}
                                                </p>
                                            </td>
                                            <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                    {{ Carbon\Carbon::parse($item->created_at)->translatedFormat("d F Y") }}
                                                </p>
                                            </td>
                                            <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                     <div class="flex items-center gap-1.5 justify-center py-1">
                                                         <a href="{{ route("user.view", $item->slug) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition-colors" title="Lihat Detail">
                                                             <i class="ri-eye-line text-lg"></i>
                                                         </a>
                                                         @role(["pic", "manajer_laundry", "operator", "admin"])
                                                             <button type="button" class="edit-user-btn inline-flex items-center justify-center w-8 h-8 rounded-lg border border-amber-200 text-amber-600 hover:bg-amber-50 transition-colors" title="Edit Pengguna" 
                                                                     data-slug="{{ $item->slug }}"
                                                                     data-nama="{{ $item->nama }}"
                                                                     data-username="{{ $item->username ?? '' }}"
                                                                     data-email="{{ $item->user->email ?? '' }}"
                                                                     data-telepon="{{ $item->telepon }}"
                                                                     data-gaji="{{ $item->gaji ?? 0 }}"
                                                                     data-bank="{{ $item->bank ?? '' }}"
                                                                     data-rekening="{{ $item->nomor_rekening ?? '' }}"
                                                                     data-role="{{ $item->user->roles->pluck('name')->first() ?? '' }}"
                                                                     data-cabang="{{ $item->cabang_id ?? '' }}">
                                                                 <i class="ri-pencil-line text-lg"></i>
                                                             </button>
                                                             <button type="button" class="edit-password-btn inline-flex items-center justify-center w-8 h-8 rounded-lg border border-purple-200 text-purple-600 hover:bg-purple-50 transition-colors" title="Ganti Password"
                                                                     data-slug="{{ $item->slug }}"
                                                                     data-nama="{{ $item->nama }}">
                                                                 <i class="ri-lock-line text-lg"></i>
                                                             </button>
                                                             <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition-colors" title="Hapus Pengguna" onclick="return delete_button('{{ $item->slug }}', '{{ $item->nama }}')">
                                                                 <i class="ri-delete-bin-line text-lg"></i>
                                                             </button>
                                                         @endrole
                                                     </div>
                                                 </td>
                                        </tr>
                                    @endforeach

                                    @foreach ($gamis as $item)
                                        <tr>
                                            <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                    {{ $item->nama }}
                                                </p>
                                            </td>
                                            <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                    {{ $item->user->email }}
                                                </p>
                                            </td>
                                            <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                    {{ $item->telepon }}
                                                </p>
                                            </td>
                                            <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                    {{ $item->user->roles->pluck("name")->first() }}
                                                </p>
                                            </td>
                                            <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                    {{ Carbon\Carbon::parse($item->created_at)->translatedFormat("d F Y") }}
                                                </p>
                                            </td>
                                            <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                     <div class="flex items-center gap-1.5 justify-center py-1">
                                                         <a href="{{ route("user.view", $item->slug) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition-colors" title="Lihat Detail">
                                                             <i class="ri-eye-line text-lg"></i>
                                                         </a>
                                                         @role(["pic", "manajer_laundry", "operator", "admin"])
                                                             <button type="button" class="edit-user-btn inline-flex items-center justify-center w-8 h-8 rounded-lg border border-amber-200 text-amber-600 hover:bg-amber-50 transition-colors" title="Edit Pengguna" 
                                                                     data-slug="{{ $item->slug }}"
                                                                     data-nama="{{ $item->nama }}"
                                                                     data-username="{{ $item->username ?? '' }}"
                                                                     data-email="{{ $item->user->email ?? '' }}"
                                                                     data-telepon="{{ $item->telepon }}"
                                                                     data-gaji="{{ $item->gaji ?? 0 }}"
                                                                     data-bank="{{ $item->bank ?? '' }}"
                                                                     data-rekening="{{ $item->nomor_rekening ?? '' }}"
                                                                     data-role="{{ $item->user->roles->pluck('name')->first() ?? '' }}"
                                                                     data-cabang="{{ $item->cabang_id ?? '' }}">
                                                                 <i class="ri-pencil-line text-lg"></i>
                                                             </button>
                                                             <button type="button" class="edit-password-btn inline-flex items-center justify-center w-8 h-8 rounded-lg border border-purple-200 text-purple-600 hover:bg-purple-50 transition-colors" title="Ganti Password"
                                                                     data-slug="{{ $item->slug }}"
                                                                     data-nama="{{ $item->nama }}">
                                                                 <i class="ri-lock-line text-lg"></i>
                                                             </button>
                                                             <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition-colors" title="Hapus Pengguna" onclick="return delete_button('{{ $item->slug }}', '{{ $item->nama }}')">
                                                                 <i class="ri-delete-bin-line text-lg"></i>
                                                             </button>
                                                         @endrole
                                                     </div>
                                                 </td>
                                        </tr>
                                    @endforeach
                                @endrole
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- Akhir Tabel User --}}

            @role(["lurah", "operator", "pic", "admin"])
                {{-- Awal Tabel User Trash --}}
                <div class="dark:bg-slate-850 dark:shadow-dark-xl relative mb-6 flex min-w-0 flex-col break-words rounded-2xl border-0 border-solid border-transparent bg-white bg-clip-border shadow-xl">
                    <div class="border-b-solid mb-0 flex items-center justify-between rounded-t-2xl border-b-0 border-b-transparent p-6 pb-3">
                        <h6 class="font-bold dark:text-white">{{ $title }} Trash <span class="text-error">(data yang telah dihapus)</span></h6>
                    </div>
                    <div class="flex-auto px-0 pb-2 pt-0">
                        <div class="overflow-x-auto p-0 px-6 pb-6">
                            <table id="myTable2" class="nowrap stripe mb-3 w-full max-w-full border-collapse items-center align-top text-slate-500 dark:border-white/40" style="width: 100%;">
                                <thead class="align-bottom">
                                    <tr>
                                        <th class="rounded-tl bg-blue-500 text-xs font-bold uppercase text-white dark:text-white">
                                            Nama
                                        </th>
                                        <th class="bg-blue-500 text-xs font-bold uppercase text-white dark:text-white">
                                            Email
                                        </th>
                                        <th class="bg-blue-500 text-xs font-bold uppercase text-white dark:text-white">
                                            Telepon
                                        </th>
                                        <th class="bg-blue-500 text-xs font-bold uppercase text-white dark:text-white">
                                            Created_at
                                        </th>
                                        <th class="bg-blue-500 text-xs font-bold uppercase text-white dark:text-white">
                                            Deleted_at
                                        </th>
                                        <th class="rounded-tr bg-blue-500 text-xs font-bold uppercase text-white dark:text-white">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @role(["lurah", "operator", "pic", "admin"])
                                        @role(["lurah", "pic", "admin"])
                                            @foreach ($manajerTrash as $item)
                                                <tr>
                                                    <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                        <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                            {{ $item->nama }}
                                                        </p>
                                                    </td>
                                                    <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                        <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                            {{ $item->email }}
                                                        </p>
                                                    </td>
                                                    <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                        <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                            {{ $item->telepon }}
                                                        </p>
                                                    </td>
                                                    <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                        <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                            {{ Carbon\Carbon::parse($item->created_at)->translatedFormat("d F Y") }}
                                                        </p>
                                                    </td>
                                                    <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                        <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                            {{ Carbon\Carbon::parse($item->deleted_at)->translatedFormat("d F Y H:i:s") }}
                                                        </p>
                                                    </td>
                                                    <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                         <div class="flex items-center gap-1.5 justify-center py-1">
                                                             <a href="{{ route("user.trash", [$item->slug]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition-colors" title="Lihat Detail">
                                                                 <i class="ri-eye-line text-lg"></i>
                                                             </a>
                                                             @role(["pic", "manajer_laundry", "operator", "admin"])
                                                                 <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-emerald-200 text-emerald-600 hover:bg-emerald-50 transition-colors" title="Pulihkan Pengguna" onclick="return restore_button('{{ $item->slug }}', '{{ $item->nama }}')">
                                                                     <i class="ri-history-line text-lg"></i>
                                                                 </button>
                                                                 <button type="button" class="inline-flex items-center justify-center px-2 h-8 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold transition-colors" title="Hapus Permanen" onclick="return destroy_button('{{ $item->slug }}', '{{ $item->nama }}')">
                                                                     Hapus Permanen
                                                                 </button>
                                                             @endrole
                                                         </div>
                                                     </td>
                                                </tr>
                                            @endforeach
                                        @endrole

                                        @foreach ($pegawaiTrash as $item)
                                            <tr>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                    <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                        {{ $item->nama }}
                                                    </p>
                                                </td>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                    <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                        {{ $item->email }}
                                                    </p>
                                                </td>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                    <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                        {{ $item->telepon }}
                                                    </p>
                                                </td>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                    <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                        {{ Carbon\Carbon::parse($item->created_at)->translatedFormat("d F Y") }}
                                                    </p>
                                                </td>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                    <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                        {{ Carbon\Carbon::parse($item->deleted_at)->translatedFormat("d F Y H:i:s") }}
                                                    </p>
                                                </td>
                                                
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                     <div class="flex items-center gap-1.5 justify-center py-1">
                                                         <a href="{{ route("user.trash", $item->slug) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition-colors" title="Lihat Detail">
                                                             <i class="ri-eye-line text-lg"></i>
                                                         </a>
                                                         @role(["pic", "manajer_laundry", "operator", "admin"])
                                                             <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-emerald-200 text-emerald-600 hover:bg-emerald-50 transition-colors" title="Pulihkan Pengguna" onclick="return restore_button('{{ $item->slug }}', '{{ $item->nama }}')">
                                                                 <i class="ri-history-line text-lg"></i>
                                                             </button>
                                                             <button type="button" class="inline-flex items-center justify-center px-2 h-8 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold transition-colors" title="Hapus Permanen" onclick="return destroy_button('{{ $item->slug }}', '{{ $item->nama }}')">
                                                                 Hapus Permanen
                                                             </button>
                                                         @endrole
                                                     </div>
                                                 </td>
                                            </tr>
                                        @endforeach

                                        @foreach ($gamisTrash as $item)
                                            <tr>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                    <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                        {{ $item->nama }}
                                                    </p>
                                                </td>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                    <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                        {{ $item->email }}
                                                    </p>
                                                </td>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                    <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                        {{ $item->telepon }}
                                                    </p>
                                                </td>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                    <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                        {{ Carbon\Carbon::parse($item->created_at)->translatedFormat("d F Y") }}
                                                    </p>
                                                </td>
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                    <p class="text-base font-semibold leading-tight text-slate-500 dark:text-slate-200">
                                                        {{ Carbon\Carbon::parse($item->deleted_at)->translatedFormat("d F Y H:i:s") }}
                                                    </p>
                                                </td>
                                                
                                                <td class="border-b border-slate-600 bg-transparent text-left align-middle">
                                                     <div class="flex items-center gap-1.5 justify-center py-1">
                                                         <a href="{{ route("user.trash", $item->slug) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition-colors" title="Lihat Detail">
                                                             <i class="ri-eye-line text-lg"></i>
                                                         </a>
                                                         @role(["pic", "manajer_laundry", "operator", "admin"])
                                                             <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-emerald-200 text-emerald-600 hover:bg-emerald-50 transition-colors" title="Pulihkan Pengguna" onclick="return restore_button('{{ $item->slug }}', '{{ $item->nama }}')">
                                                                 <i class="ri-history-line text-lg"></i>
                                                             </button>
                                                             <button type="button" class="inline-flex items-center justify-center px-2 h-8 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold transition-colors" title="Hapus Permanen" onclick="return destroy_button('{{ $item->slug }}', '{{ $item->nama }}')">
                                                                 Hapus Permanen
                                                             </button>
                                                         @endrole
                                                     </div>
                                                 </td>
                                            </tr>
                                        @endforeach
                                    @endrole
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{-- Akhir Tabel User Trash --}}
            @endrole
            <!-- Modal Tambah User -->
            <dialog id="tambah_user_modal" class="modal">
                <div class="modal-box max-w-2xl bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xl relative">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4">✕</button>
                    </form>
                    <div class="mb-5 border-b border-slate-100 pb-3">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Tambah Akun Baru</h3>
                    </div>
                    <form action="{{ route('user.store') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <!-- Default Cabang Utama (Hidden) -->
                        <input type="hidden" name="cabang_id" value="{{ \App\Models\Cabang::first()->id ?? 1 }}" />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Role Selection -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="text-red-500">*</span> Role / Peran
                                </span>
                                <select name="role" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required>
                                    <option value="" disabled selected>Pilih Role!</option>
                                    <option value="operator">Operator</option>
                                    <option value="pegawai_laundry">Karyawan</option>
                                </select>
                            </div>

                            <!-- Nama Lengkap -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="text-red-500">*</span> Nama Lengkap
                                </span>
                                <input type="text" name="nama" placeholder="Nama lengkap" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Username -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="text-red-500">*</span> Username
                                </span>
                                <input type="text" name="username" placeholder="Username" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                            </div>

                            <!-- Email -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="text-red-500">*</span> Email
                                </span>
                                <input type="email" name="email" placeholder="Email" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nomor Telepon -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="text-red-500">*</span> Nomor Telepon
                                </span>
                                <input type="text" name="telepon" placeholder="Nomor Telepon" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                            </div>

                            <!-- Tarif Gaji -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    Tarif Gaji per Kg (Rp)
                                </span>
                                <input type="number" name="gaji" placeholder="Masukkan tarif gaji per kg" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Bank -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    Tipe Bank
                                </span>
                                <input type="text" name="bank" placeholder="Contoh: BCA, Mandiri, BNI" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" />
                            </div>

                            <!-- Nomor Rekening -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    Nomor Rekening
                                </span>
                                <input type="text" name="nomor_rekening" placeholder="Nomor Rekening" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Password -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="text-red-500">*</span> Password
                                </span>
                                <input type="password" name="password" placeholder="Password" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                            </div>

                            <!-- Konfirmasi Password -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="text-red-500">*</span> Konfirmasi Password
                                </span>
                                <input type="password" name="password_confirmation" placeholder="Ulangi password" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" onclick="document.getElementById('tambah_user_modal').close()" class="px-5 py-2 text-slate-500 hover:bg-slate-100 text-sm font-bold rounded-xl transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-sm transition-colors">Simpan</button>
                        </div>
                    </form>
                </div>
            </dialog>

            <!-- Modal Edit User -->
            <dialog id="edit_user_modal" class="modal">
                <div class="modal-box max-w-2xl bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xl relative">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4">✕</button>
                    </form>
                    <div class="mb-5 border-b border-slate-100 pb-3">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Ubah Akun Pengguna</h3>
                    </div>
                    <form id="edit_user_form" action="" method="POST" class="space-y-4">
                        @csrf
                        
                        <!-- Default Cabang Utama (Hidden) -->
                        <input type="hidden" id="edit_cabang_id" name="cabang_id" value="{{ \App\Models\Cabang::first()->id ?? 1 }}" />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Role Selection -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="text-red-500">*</span> Role / Peran
                                </span>
                                <select id="edit_role" name="role" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required>
                                    <option value="operator">Operator</option>
                                    <option value="pegawai_laundry">Karyawan</option>
                                </select>
                            </div>

                            <!-- Nama Lengkap -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="text-red-500">*</span> Nama Lengkap
                                </span>
                                <input type="text" id="edit_nama" name="nama" placeholder="Nama lengkap" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Username -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="text-red-500">*</span> Username
                                </span>
                                <input type="text" id="edit_username" name="username" placeholder="Username" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                            </div>

                            <!-- Email -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="text-red-500">*</span> Email
                                </span>
                                <input type="email" id="edit_email" name="email" placeholder="Email" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nomor Telepon -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="text-red-500">*</span> Nomor Telepon
                                </span>
                                <input type="text" id="edit_telepon" name="telepon" placeholder="Nomor Telepon" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                            </div>

                            <!-- Tarif Gaji -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    Tarif Gaji per Kg (Rp)
                                </span>
                                <input type="number" id="edit_gaji" name="gaji" placeholder="Masukkan tarif gaji per kg" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Bank -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    Tipe Bank
                                </span>
                                <input type="text" id="edit_bank" name="bank" placeholder="Contoh: BCA, Mandiri, BNI" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" />
                            </div>

                            <!-- Nomor Rekening -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    Nomor Rekening
                                </span>
                                <input type="text" id="edit_nomor_rekening" name="nomor_rekening" placeholder="Nomor Rekening" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" />
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" onclick="document.getElementById('edit_user_modal').close()" class="px-5 py-2 text-slate-500 hover:bg-slate-100 text-sm font-bold rounded-xl transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-sm transition-colors">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </dialog>

            <!-- Modal Ubah Password -->
            <dialog id="ubah_password_modal" class="modal">
                <div class="modal-box bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xl relative">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4">✕</button>
                    </form>
                    <div class="mb-5 border-b border-slate-100 pb-3">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Ubah Password</h3>
                    </div>
                    <form id="ubah_password_form" action="" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Pengguna</span>
                            <input type="text" id="pass_nama" class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-500 font-semibold" readonly />
                        </div>

                        <div class="space-y-1">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <span class="text-red-500">*</span> Password Baru
                            </span>
                            <input type="password" name="password" placeholder="Masukkan password baru" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                        </div>

                        <div class="space-y-1">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <span class="text-red-500">*</span> Konfirmasi Password Baru
                            </span>
                            <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="w-full bg-[#f8fafc] dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                        </div>

                        <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" onclick="document.getElementById('ubah_password_modal').close()" class="px-5 py-2 text-slate-500 hover:bg-slate-100 text-sm font-bold rounded-xl transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-sm transition-colors">Ganti Password</button>
                        </div>
                    </form>
                </div>
            </dialog>
        </div>
    </div>
@endsection

