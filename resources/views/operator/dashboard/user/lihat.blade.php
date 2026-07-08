@extends("operator.partials.layout")

@section("js")
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($("input[name='role']").val() == 'gamis') {
                $("#form_gamis").html(`
                    <label id="form_kk_gamis" class="block space-y-1 w-full mt-4">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Kartu Keluarga Gamis</span>
                        <input type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none cursor-not-allowed text-slate-500" value="{{ (isset($profile->gamis) && $profile->gamis) ? $profile->gamis->kartu_keluarga : 'Tidak ada' }}" readonly />
                    </label>
                `);
            }
        });
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
                    <h2 class="font-bold text-[#0f172a] text-lg">{{ $title }} : {{ $profile->nama }}</h2>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">ID Karyawan: <span class="text-blue-600 font-semibold">#{{ $user->id }}</span></p>
                </div>
            </div>

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Role -->
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Role / Peran</span>
                        <input type="text" value="{{ $user->roles[0]->name ?? '-' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly />
                    </div>

                    <!-- Cabang / RW -->
                    @if ($user->roles[0]->name == 'rw')
                        <div class="space-y-1">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Nomor RW</span>
                            <input type="text" value="{{ $profile->nomor_rw ?? '-' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly />
                        </div>
                    @else
                        <div class="space-y-1">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Cabang</span>
                            <input type="text" value="{{ $user->cabang ? $user->cabang->nama : '-' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly />
                        </div>
                    @endif
                </div>

                <div id="form_gamis"></div>

                <!-- Username -->
                <div class="space-y-1">
                    <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Username</span>
                    <input type="text" value="{{ $user->username }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly />
                </div>

                <!-- Email -->
                <div class="space-y-1">
                    <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Email</span>
                    <input type="text" value="{{ $user->email }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly />
                </div>

                <!-- Nama -->
                <div class="space-y-1">
                    <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</span>
                    <input type="text" value="{{ $profile->nama }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly />
                </div>

                <!-- Jenis Kelamin -->
                <div class="space-y-1.5">
                    <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Jenis Kelamin</span>
                    <div class="flex items-center gap-6 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 text-slate-500">
                        <label class="flex items-center gap-2 cursor-not-allowed text-sm font-semibold">
                            <input type="radio" value="L" name="jenis_kelamin" class="w-4 h-4 text-blue-600 border-slate-300" @if($profile->jenis_kelamin == 'L') checked @endif disabled />
                            Laki-laki
                        </label>
                        <label class="flex items-center gap-2 cursor-not-allowed text-sm font-semibold">
                            <input type="radio" value="P" name="jenis_kelamin" class="w-4 h-4 text-blue-600 border-slate-300" @if($profile->jenis_kelamin == 'P') checked @endif disabled />
                            Perempuan
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Tempat Lahir -->
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Tempat Lahir</span>
                        <input type="text" value="{{ $profile->tempat_lahir }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly />
                    </div>

                    <!-- Tanggal Lahir -->
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Lahir</span>
                        <input type="date" value="{{ $profile->tanggal_lahir }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly />
                    </div>
                </div>

                <!-- Telepon -->
                <div class="space-y-1">
                    <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Nomor Telepon</span>
                    <input type="text" value="{{ $profile->telepon }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly />
                </div>

                <!-- Tarif Gaji per Kg -->
                <div class="space-y-1">
                    <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Tarif Gaji per Kg (Rp)</span>
                    <input type="text" value="{{ number_format($user->gaji ?? 0, 0, ',', '.') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly />
                </div>

                <!-- Alamat -->
                <div class="space-y-1">
                    <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Lengkap</span>
                    <textarea rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly>{{ $profile->alamat }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Mulai Kerja -->
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Mulai Kerja</span>
                        <input type="date" value="{{ $profile->mulai_kerja }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly />
                    </div>

                    <!-- Selesai Kerja -->
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Selesai Kerja</span>
                        <input type="date" value="{{ $profile->selesai_kerja }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly />
                    </div>
                </div>

                @if($user->roles[0]->name == "gamis" && isset($profile->gamis) && $profile->gamis)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <div class="space-y-1">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Kartu Keluarga</span>
                            <input type="text" value="{{ $profile->gamis->kartu_keluarga ?? '-' }}" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly />
                        </div>
                        <div class="space-y-1">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">RT</span>
                            <input type="text" value="{{ $profile->gamis->rt ?? '-' }}" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly />
                        </div>
                        <div class="space-y-1">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">RW</span>
                            <input type="text" value="{{ $profile->gamis->rw ?? '-' }}" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm cursor-not-allowed text-slate-500" readonly />
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="pt-4 flex flex-col sm:flex-row gap-3">
                    @if (!$trash)
                        @role(["admin", "manajer_laundry"])
                            <a href="{{ route('user.edit', $user->slug) }}" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-xl text-sm transition-colors text-center shadow-sm">
                                Ubah Karyawan
                            </a>
                        @endrole
                    @endif
                    <a href="{{ route('user') }}" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 px-6 rounded-xl text-sm transition-colors text-center">
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
