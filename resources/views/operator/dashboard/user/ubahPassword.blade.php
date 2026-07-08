@extends("operator.partials.layout")

@section("js")
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
                text: '{{ $title }} Gagal Diubah',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'OK',
            })
        @endif
    </script>
@endsection

@section("content")
    <div class="max-w-xl mx-auto space-y-6">
        
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
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Email Karyawan: <span class="text-blue-600 font-semibold">{{ $user->email }}</span></p>
                </div>
            </div>

            <div class="p-6">
                <form action="{{ route('user.update.password', $user->slug) }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="text" name="slug" value="{{ $user->slug }}" hidden>

                    <!-- Password Baru -->
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <span class="text-red-500">*</span> Password Baru
                        </span>
                        <input type="password" name="password" placeholder="Masukkan password baru" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                        @error("password")
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div class="space-y-1">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <span class="text-red-500">*</span> Konfirmasi Password Baru
                        </span>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors" required />
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-xl text-sm transition-colors text-center shadow-sm">
                            Ganti Password
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
