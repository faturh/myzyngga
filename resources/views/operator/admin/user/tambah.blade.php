@extends("operator.partials.layout")

@section("js")
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $("select[name='role']").change(function() {
                if ($("select[name='role']").find(":selected").text() == 'gamis') {
                    $("#form_gamis").html(`
                        <label id="form_kk_gamis" class="block space-y-1 w-full mt-4">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <span class="text-red-500">*</span> Kartu Keluarga Gamis
                            </span>
                            <select name="gamis_id" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required>
                                <option disabled selected>Pilih Kartu Keluarga!</option>
                                @foreach ($kkGamis as $item)
                                    <option value="{{ $item->id }}">
                                        KK: {{ $item->kartu_keluarga }} | RT: {{ $item->rt }} | RW: {{ $item->rw }}
                                    </option>
                                @endforeach
                            </select>
                            @error("gamis_id")
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </label>
                    `);
                } else {
                    $("#form_gamis").html("");
                }
            });
        });

        @if (session()->has("success"))
            Swal.fire({
                title: 'Berhasil',
                text: '{{ session("success") }}',
                icon: 'success',
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'OK',
            });
        @endif

        @if (session()->has("error"))
            Swal.fire({
                title: 'Gagal',
                text: '{{ session("error") }}',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK',
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                title: 'Gagal',
                text: '{{ $title }} Gagal Dibuat',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK',
            })
        @endif
    </script>
@endsection

@section("content")
    <div class="max-w-3xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between">
            <a href="{{ route('user') }}" class="flex items-center gap-2 text-slate-500 hover:text-slate-800 text-sm font-semibold transition-all">
                <i data-feather="arrow-left" class="w-4 h-4"></i>
                Kembali ke Daftar Karyawan
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100/90 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h2 class="font-bold text-[#0f172a] text-lg">{{ $title }}</h2>
                    @if ($isCabang[0])
                        <p class="text-xs text-slate-400 font-medium mt-0.5">Cabang: <span class="text-blue-600 font-semibold">{{ $isCabang[1] }}</span></p>
                    @endif
                </div>
            </div>

            <div class="p-6">
                <form action="{{ route("user.store") }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    
                    <!-- Cabang (Hidden, default to active user's cabang or first available) -->
                    <input type="hidden" name="cabang_id" value="{{ auth()->user()->cabang_id ?? \App\Models\Cabang::first()->id ?? 1 }}" />

                    <!-- Role -->
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <span class="text-red-500">*</span> Role / Peran
                        </span>
                        <select name="role" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required>
                            <option disabled @if(!request()->query('role')) selected @endif>Pilih Role!</option>
                            @foreach ($role as $item)
                                <option value="{{ $item->name }}" @if(request()->query('role') == $item->name) selected @endif>{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error("role")
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div id="form_gamis"></div>

                    <!-- Username -->
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <span class="text-red-500">*</span> Username
                        </span>
                        <input type="text" name="username" placeholder="Masukkan username" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" value="{{ old("username") }}" required />
                        @error("username")
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <span class="text-red-500">*</span> Email
                        </span>
                        <input type="email" name="email" placeholder="Masukkan email karyawan" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" value="{{ old("email") }}" required />
                        @error("email")
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Password -->
                        <div class="space-y-1">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <span class="text-red-500">*</span> Password
                            </span>
                            <input type="password" name="password" placeholder="Masukkan password" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                            @error("password")
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="space-y-1">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <span class="text-red-500">*</span> Konfirmasi Password
                            </span>
                            <input type="password" name="password_confirmation" placeholder="Ulangi password" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                        </div>
                    </div>

                    <!-- Nama -->
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <span class="text-red-500">*</span> Nama Lengkap
                        </span>
                        <input type="text" name="nama" placeholder="Nama lengkap karyawan" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" value="{{ old("nama") }}" required />
                        @error("nama")
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="space-y-1.5">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <span class="text-red-500">*</span> Jenis Kelamin
                        </span>
                        <div class="flex items-center gap-6 bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3.5">
                            <label class="flex items-center gap-2 cursor-pointer text-sm font-semibold text-slate-600">
                                <input type="radio" value="L" name="jenis_kelamin" class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500" required />
                                Laki-laki
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-sm font-semibold text-slate-600">
                                <input type="radio" value="P" name="jenis_kelamin" class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500" required />
                                Perempuan
                            </label>
                        </div>
                        @error("jenis_kelamin")
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tempat Lahir -->
                        <div class="space-y-1">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <span class="text-red-500">*</span> Tempat Lahir
                            </span>
                            <input type="text" name="tempat_lahir" placeholder="Tempat lahir" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" value="{{ old("tempat_lahir") }}" required />
                            @error("tempat_lahir")
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="space-y-1">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <span class="text-red-500">*</span> Tanggal Lahir
                            </span>
                            <input type="date" name="tanggal_lahir" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" value="{{ old("tanggal_lahir") }}" required />
                            @error("tanggal_lahir")
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Telepon -->
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <span class="text-red-500">*</span> Nomor Telepon
                        </span>
                        <input type="text" name="telepon" placeholder="Contoh: 0812XXXXXXXX" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" value="{{ old("telepon") }}" required />
                        @error("telepon")
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Tarif Gaji per Kg -->
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Tarif Gaji per Kg (Rp)
                        </span>
                        <input type="number" name="gaji" placeholder="Masukkan tarif gaji per kg" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" value="{{ old("gaji") }}" />
                        @error("gaji")
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tipe Bank -->
                        <div class="space-y-1">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Tipe Bank
                            </span>
                            <input type="text" name="bank" placeholder="Contoh: BCA, Mandiri, BNI" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" value="{{ old("bank") }}" />
                            @error("bank")
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nomor Rekening -->
                        <div class="space-y-1">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Nomor Rekening
                            </span>
                            <input type="text" name="nomor_rekening" placeholder="Masukkan nomor rekening" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" value="{{ old("nomor_rekening") }}" />
                            @error("nomor_rekening")
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <span class="text-red-500">*</span> Alamat Lengkap
                        </span>
                        <textarea name="alamat" placeholder="Masukkan alamat tinggal saat ini" rows="3" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required>{{ old("alamat") }}</textarea>
                        @error("alamat")
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Mulai Kerja -->
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Tanggal Mulai Kerja
                        </span>
                        <input type="date" name="mulai_kerja" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" value="{{ old("mulai_kerja") }}" />
                        @error("mulai_kerja")
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm transition-colors text-center shadow-sm">
                            Tambah Karyawan
                        </button>
                        <a href="{{ route('user') }}" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 px-6 rounded-xl text-sm transition-colors text-center">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
