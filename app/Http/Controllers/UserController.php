<?php

namespace App\Http\Controllers;

use App\Exports\UserExport;
use App\Imports\UserImport;
use App\Models\Cabang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = auth()->user();
                if (!$user || !$user->isAdmin()) {
                    abort(403, 'Hanya Admin Utama yang dapat mengelola pengguna.');
                }
                return $next($request);
            }),
        ];
    }

    private function mapUserCollection($users, $hideCabang = false)
    {
        return $users->map(function ($u) use ($hideCabang) {
            $data = [
                'id' => $u->id,
                'nama' => $u->name ?? $u->username,
                'username' => $u->username,
                'email' => $u->email,
                'slug' => $u->slug,
                'telepon' => $u->phone ?? '-',
                'gaji' => $u->gaji ?? 0,
                'role' => $u->roles->pluck('name')->first() ?? $u->role ?? '-',
                'created_at' => $u->created_at,
                'deleted_at' => $u->deleted_at,
                'user' => (object) [
                    'email' => $u->email,
                    'roles' => $u->roles,
                ]
            ];

            if (!$hideCabang) {
                $data['cabang_id'] = $u->cabang_id;
                $data['nama_cabang'] = $u->cabang?->nama ?? '-';
            }

            return (object) $data;
        });
    }

    private function findUser(Request $request, bool $withTrashed = false)
    {
        $identifier = $request->user ?? $request->slug ?? $request->id;
        
        $query = User::query();
        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->where(function ($q) use ($identifier) {
            $q->where('slug', $identifier);
            if (is_numeric($identifier)) {
                $q->orWhere('id', (int)$identifier);
            }
        })->first();
    }

    public function index()
    {
        $title = "Users Management";
        $userRole = auth()->user()->roles[0]->name;

        $cabang = Cabang::where('deleted_at', null)->get();
        if ($userRole == 'admin' || $userRole == 'pic') {
            $role = Role::where('name', '!=', 'lurah')->where('name', '!=', 'rw')->where('name', '!=', 'pic')->get();
        } else if ($userRole == 'operator') {
            $role = Role::where('name', '!=', 'lurah')->where('name', '!=', 'operator')->where('name', '!=', 'rw')->where('name', '!=', 'pic')->get();
            $cabang = Cabang::where('deleted_at', null)->where('id', auth()->user()->cabang_id)->get();
        } else {
            $role = Role::get();
        }

        if ($userRole == 'lurah' || $userRole == 'pic' || $userRole == 'admin') {
            $manajer = User::whereHas('roles', function($q) { $q->where('name', 'operator'); })->with('cabang')->get();
            $pegawai = User::whereHas('roles', function($q) { $q->where('name', 'pegawai_laundry'); })->with('cabang')->get();
            $gamis = collect();

            $manajerTrash = User::whereHas('roles', function($q) { $q->where('name', 'operator'); })->onlyTrashed()->with('cabang')->get();
            $pegawaiTrash = User::whereHas('roles', function($q) { $q->where('name', 'pegawai_laundry'); })->onlyTrashed()->with('cabang')->get();
            $gamisTrash = collect();

            if (request()->expectsJson()) {
                return response()->json([
                    'data' => [
                        'manajer' => $this->mapUserCollection($manajer, true),
                        'pegawai' => $this->mapUserCollection($pegawai, true),
                    ],
                    'status' => 200
                ], 200);
            }

            return view('operator.admin.user.index', [
                'title' => $title,
                'cabang' => $cabang,
                'role' => $role,
                'manajer' => $this->mapUserCollection($manajer),
                'pegawai' => $this->mapUserCollection($pegawai),
                'gamis' => $gamis,
                'manajerTrash' => $this->mapUserCollection($manajerTrash),
                'pegawaiTrash' => $this->mapUserCollection($pegawaiTrash),
                'gamisTrash' => $gamisTrash,
            ]);

        } elseif ($userRole == 'operator') {
            $cabangId = auth()->user()->cabang_id;
            $pegawai = User::where('cabang_id', $cabangId)->whereHas('roles', function($q) { $q->where('name', 'pegawai_laundry'); })->with('cabang')->get();
            $gamis = collect();

            $pegawaiTrash = User::where('cabang_id', $cabangId)->whereHas('roles', function($q) { $q->where('name', 'pegawai_laundry'); })->onlyTrashed()->with('cabang')->get();
            $gamisTrash = collect();

            if (request()->expectsJson()) {
                return response()->json([
                    'data' => [
                        'pegawai' => $this->mapUserCollection($pegawai, true),
                    ],
                    'status' => 200
                ], 200);
            }

            return view('operator.admin.user.index', [
                'title' => $title,
                'cabang' => $cabang,
                'role' => $role,
                'pegawai' => $this->mapUserCollection($pegawai),
                'gamis' => $gamis,
                'pegawaiTrash' => $this->mapUserCollection($pegawaiTrash),
                'gamisTrash' => $gamisTrash,
            ]);
        }
    }

    public function view(Request $request)
    {
        $title = "Detail User";
        $trash = false;
        $userRole = auth()->user()->roles[0]->name;
        
        $user = $this->findUser($request);

        if ($user == null || $user->cabang_id != auth()->user()->cabang_id && $userRole != 'lurah' && $userRole != 'pic' && $userRole != 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'User tidak ditemukan', 'status' => 404], 404);
            }
            abort(404, 'USER TIDAK DITEMUKAN.');
        } else if ($user->slug == auth()->user()->slug) {
            return to_route('profile', $user->slug);
        }

        $profile = (object) [
            'nama' => $user->name ?? $user->username,
            'telepon' => $user->phone ?? '-',
            'alamat' => '-',
            'jenis_kelamin' => '-',
            'tempat_lahir' => '-',
            'tanggal_lahir' => '-',
            'mulai_kerja' => '-',
            'selesai_kerja' => '-',
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'user' => $this->mapUserCollection(collect([$user]), true)->first(),
                ],
                'status' => 200
            ], 200);
        }

        return view('operator.admin.user.lihat', compact('title', 'user', 'profile', 'trash'));
    }

    public function create()
    {
        return redirect()->route('user');
    }

    public function store(Request $request)
    {
        // Normalize name and phone inputs for convenience
        if ($request->has('name') && !$request->has('nama')) {
            $request->merge(['nama' => $request->name]);
        }
        if ($request->has('phone') && !$request->has('telepon')) {
            $request->merge(['telepon' => $request->phone]);
        }

        $validatedUser = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'cabang_id' => 'nullable|integer',
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'gaji' => 'nullable|numeric|min:0',
        ], [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute sudah ada, silakan isi yang lain.',
            'max' => ':attribute tidak boleh lebih dari :max karakter.',
            'integer' => ':attribute harus berupa angka.',
            'confirmed' => 'Konfirmasi :attribute tidak sama.',
        ]);

        $cabangId = $validatedUser['cabang_id'] ?? null;
        if (empty($cabangId) || !\App\Models\Cabang::where('id', $cabangId)->exists()) {
            $cabangId = auth()->user()->cabang_id ?? \App\Models\Cabang::first()->id ?? 1;
        }

        $roleSelected = $request->role ?? 'pegawai_laundry';

        $user = User::create([
            'username' => $validatedUser['username'],
            'email' => $validatedUser['email'],
            'password' => Hash::make($validatedUser['password']),
            'cabang_id' => $cabangId,
            'name' => $validatedUser['nama'],
            'phone' => $validatedUser['telepon'],
            'slug' => str()->slug($validatedUser['username']),
            'gaji' => (int) ($validatedUser['gaji'] ?? 0),
            'role' => $roleSelected,
        ]);

        $user->assignRole($roleSelected);

        if ($user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $this->mapUserCollection(collect([$user]), true)->first(),
                    'message' => 'User Berhasil Ditambahkan',
                    'status' => 200
                ], 200);
            }
            return to_route('user')->with('success', 'User Berhasil Ditambahkan');
        } else {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'User Gagal Ditambahkan',
                    'status' => 400
                ], 400);
            }
            return to_route('user')->with('error', 'User Gagal Ditambahkan');
        }
    }

    public function edit(Request $request)
    {
        $title = "Ubah User";
        $userRole = auth()->user()->roles[0]->name;
        $kkGamis = collect();

        if ($userRole == 'lurah') {
            abort(403, 'USER DOES NOT HAVE PERMISSION.');
        }

        $cabang = Cabang::where('id', auth()->user()->cabang_id)->withTrashed()->first();
        if ($cabang && $cabang->deleted_at) {
            abort(403, 'USER DOES NOT HAVE PERMISSION.');
        }

        if ($userRole == 'admin' || $userRole == 'pic') {
            $role = Role::where('name', '!=', 'lurah')->where('name', '!=', 'rw')->where('name', '!=', 'pic')->get();
            $cabang = Cabang::where('deleted_at', null)->get();
        } else if ($userRole == 'operator') {
            $role = Role::where('name', '!=', 'lurah')->where('name', '!=', 'operator')->where('name', '!=', 'rw')->where('name', '!=', 'pic')->get();
            $cabang = Cabang::where('deleted_at', null)->where('id', auth()->user()->cabang_id)->get();
        }

        $user = $this->findUser($request);
        if ($user == null || $user->cabang_id != auth()->user()->cabang_id && $userRole != 'pic' && $userRole != 'admin') {
            abort(404, 'USER TIDAK DITEMUKAN.');
        } else if ($user->slug == auth()->user()->slug) {
            return to_route('profile', $user->slug);
        }

        $profile = (object) [
            'nama' => $user->name ?? $user->username,
            'telepon' => $user->phone ?? '-',
            'alamat' => '-',
            'jenis_kelamin' => '-',
            'tempat_lahir' => '-',
            'tanggal_lahir' => '-',
            'mulai_kerja' => '-',
            'selesai_kerja' => '-',
        ];

        return view('operator.admin.user.ubah', compact('title', 'cabang', 'role', 'kkGamis', 'user', 'profile'));
    }

    public function update(Request $request)
    {
        // Normalize name and phone inputs for convenience
        if ($request->has('name') && !$request->has('nama')) {
            $request->merge(['nama' => $request->name]);
        }
        if ($request->has('phone') && !$request->has('telepon')) {
            $request->merge(['telepon' => $request->phone]);
        }

        $user = $this->findUser($request);
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'User tidak ditemukan', 'status' => 404], 404);
            }
            abort(404, 'User tidak ditemukan');
        }

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user)],
            'cabang_id' => 'nullable|integer',
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'gaji' => 'nullable|numeric|min:0',
        ], [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute sudah ada, silakan isi yang lain.',
            'max' => ':attribute tidak boleh lebih dari :max karakter.',
            'integer' => ':attribute harus berupa angka.',
        ]);

        $cabangId = $validated['cabang_id'] ?? null;
        if (empty($cabangId) || !\App\Models\Cabang::where('id', $cabangId)->exists()) {
            $cabangId = $user->cabang_id ?? auth()->user()->cabang_id ?? \App\Models\Cabang::first()->id ?? 1;
        }

        $roleSelected = $request->role ?? $user->roles->pluck('name')->first() ?? $user->role ?? 'pegawai_laundry';

        $userUpdate = $user->update([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'cabang_id' => $cabangId,
            'name' => $validated['nama'],
            'phone' => $validated['telepon'],
            'slug' => str()->slug($validated['username']),
            'gaji' => (int) ($validated['gaji'] ?? 0),
            'role' => $roleSelected,
        ]);

        $user->syncRoles([$roleSelected]);

        if ($userUpdate) {
            if ($request->expectsJson()) {
                return response()->json([
                    'data' => $this->mapUserCollection(collect([$user]), true)->first(),
                    'message' => 'User Berhasil Diperbarui',
                    'status' => 200
                ], 200);
            }
            return to_route('user')->with('success', 'User Berhasil Diperbarui');
        } else {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'User Gagal Diperbarui',
                    'status' => 400
                ], 400);
            }
            return to_route('user')->with('error', 'User Gagal Diperbarui');
        }
    }

    public function editPassword(Request $request)
    {
        $title = "Ubah Password User";
        $userRole = auth()->user()->roles[0]->name;

        if ($userRole == 'lurah') {
            abort(403, 'USER DOES NOT HAVE PERMISSION.');
        }

        $cabang = Cabang::where('id', auth()->user()->cabang_id)->withTrashed()->first();
        if ($cabang && $cabang->deleted_at) {
            abort(403, 'USER DOES NOT HAVE PERMISSION.');
        }

        $user = $this->findUser($request);
        if ($user == null || $user->cabang_id != auth()->user()->cabang_id && $userRole != 'pic' && $userRole != 'admin') {
            abort(404, 'USER TIDAK DITEMUKAN.');
        } else if ($user->slug == auth()->user()->slug) {
            return to_route('profile', $user->slug);
        }
        return view('operator.admin.user.ubahPassword', compact('title', 'user'));
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'required' => ':attribute harus diisi.',
            'confirmed' => 'Konfirmasi :attribute tidak sama.',
            'min' => 'minimal :min karakter.',
        ]);

        $user = $this->findUser($request);
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'User tidak ditemukan', 'status' => 404], 404);
            }
            abort(404, 'User tidak ditemukan');
        }

        $updatePassword = $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        if ($updatePassword) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Password User Berhasil Diganti',
                    'status' => 200
                ], 200);
            }
            return to_route('user')->with('success', 'Password User Berhasil Diganti');
        } else {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Password User Gagal Diganti',
                    'status' => 400
                ], 400);
            }
            return to_route('user')->with('error', 'Password User Gagal Diganti');
        }
    }

    public function delete(Request $request)
    {
        $cabang = Cabang::where('id', auth()->user()->cabang_id)->withTrashed()->first();
        if ($cabang && $cabang->deleted_at) {
            return response()->json([
                'message' => 'USER DOES NOT HAVE PERMISSION.',
                'status' => 403
            ], 403);
        }

        $user = $this->findUser($request);
        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan',
                'status' => 404
            ], 404);
        }

        $hapus = $user->delete();
        if ($hapus) {
            return response()->json([
                'message' => 'User Berhasil Dihapus',
                'status' => 200
            ], 200);
        } else {
            return response()->json([
                'message' => 'User Gagal Dihapus',
                'status' => 400
            ], 400);
        }
    }

    public function trash(Request $request)
    {
        $title = "Detail User Trash";
        $trash = true;
        $userRole = auth()->user()->roles[0]->name;
        
        $user = $this->findUser($request, true);

        if ($user == null || $user->cabang_id != auth()->user()->cabang_id && $userRole != 'lurah' && $userRole != 'pic' && $userRole != 'admin') {
            abort(404, 'USER TIDAK DITEMUKAN.');
        } else if ($user->slug == auth()->user()->slug) {
            return to_route('profile', $user->slug);
        }

        $profile = (object) [
            'nama' => $user->name ?? $user->username,
            'telepon' => $user->phone ?? '-',
            'alamat' => '-',
            'jenis_kelamin' => '-',
            'tempat_lahir' => '-',
            'tanggal_lahir' => '-',
            'mulai_kerja' => '-',
            'selesai_kerja' => '-',
        ];

        return view('operator.admin.user.lihat', compact('title', 'user', 'profile', 'trash'));
    }

    public function restore(Request $request)
    {
        $cabang = Cabang::where('id', auth()->user()->cabang_id)->withTrashed()->first();
        if ($cabang && $cabang->deleted_at) {
            return response()->json([
                'message' => 'USER DOES NOT HAVE PERMISSION.',
                'status' => 403
            ], 403);
        }

        $user = $this->findUser($request, true);
        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan',
                'status' => 404
            ], 404);
        }

        $pulih = $user->restore();
        if ($pulih) {
            return response()->json([
                'message' => 'User Berhasil Dipulihkan',
                'status' => 200
            ], 200);
        } else {
            return response()->json([
                'message' => 'User Gagal Dipulihkan',
                'status' => 400
            ], 400);
        }
    }

    public function destroy(Request $request)
    {
        $cabang = Cabang::where('id', auth()->user()->cabang_id)->withTrashed()->first();
        if ($cabang && $cabang->deleted_at) {
            return response()->json([
                'message' => 'USER DOES NOT HAVE PERMISSION.',
                'status' => 403
            ], 403);
        }

        $user = $this->findUser($request, true);
        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan',
                'status' => 404
            ], 404);
        }
        $userRole = $user->roles[0]->name ?? $user->role ?? 'pegawai_laundry';

        $user->removeRole($userRole);
        $hapusPermanen = $user->forceDelete();

        if ($hapusPermanen) {
            return response()->json([
                'message' => 'User Berhasil Dihapus Permanen',
                'status' => 200
            ], 200);
        } else {
            return response()->json([
                'message' => 'User Gagal Dihapus Permanen',
                'status' => 400
            ], 400);
        }
    }

    public function indexCabang(Request $request)
    {
        $title = "Users Management";

        $userRole = auth()->user()->roles[0]->name;
        if ($userRole != 'lurah' && $userRole != 'pic' && $userRole != 'admin') {
            abort(403, 'USER DOES NOT HAVE THE RIGHT ROLES.');
        }

        $cabang = Cabang::where('slug', $request->cabang)->withTrashed()->first();
        if ($cabang == null || $cabang->deleted_at) {
            abort(404, 'CABANG TIDAK DITEMUKAN ATAU SUDAH DIHAPUS.');
        }

        $role = Role::get();

        $manajer = User::where('cabang_id', $cabang->id)->whereHas('roles', function($q) { $q->where('name', 'operator'); })->get();
        $pegawai = User::where('cabang_id', $cabang->id)->whereHas('roles', function($q) { $q->where('name', 'pegawai_laundry'); })->get();
        $gamis = collect();

        $manajerTrash = User::where('cabang_id', $cabang->id)->whereHas('roles', function($q) { $q->where('name', 'operator'); })->onlyTrashed()->get();
        $pegawaiTrash = User::where('cabang_id', $cabang->id)->whereHas('roles', function($q) { $q->where('name', 'pegawai_laundry'); })->onlyTrashed()->get();
        $gamisTrash = collect();

        return view('operator.admin.user.cabang.index-cabang', [
            'title' => $title,
            'cabang' => $cabang,
            'role' => $role,
            'manajer' => $this->mapUserCollection($manajer),
            'pegawai' => $this->mapUserCollection($pegawai),
            'gamis' => $gamis,
            'manajerTrash' => $this->mapUserCollection($manajerTrash),
            'pegawaiTrash' => $this->mapUserCollection($pegawaiTrash),
            'gamisTrash' => $gamisTrash,
        ]);
    }

    public function createUserCabang(Request $request)
    {
        $userRole = auth()->user()->roles[0]->name;
        if ($userRole != 'pic' && $userRole != 'admin') {
            abort(403, 'USER DOES NOT HAVE THE RIGHT ROLES.');
        }

        $cabang = Cabang::where('deleted_at', null)->where('slug', $request->cabang)->get();
        if ($cabang->first() == null) {
            abort(404, 'CABANG TIDAK DITEMUKAN ATAU SUDAH DIHAPUS.');
        }

        $kkGamis = collect();
        $role = Role::where('name', '!=', 'lurah')->where('name', '!=', 'rw')->where('name', '!=', 'pic')->get();
        $title = "Tambah User";
        $isCabang = [true, $cabang[0]->nama, $cabang[0]->id];

        return view('operator.admin.user.tambah', compact('title', 'cabang', 'role', 'kkGamis', 'isCabang'));
    }

    public function import(Request $request)
    {
        try {
            Excel::import(new UserImport, $request->file('impor'));
            return to_route('user')->with('success', 'User Berhasil Ditambahkan');
        } catch(\Exception $ex) {
            Log::info($ex);
            return to_route('user')->with('error', 'User Gagal Ditambahkan');
        }
    }

    public function export(Request $request)
    {
        return Excel::download(new UserExport($request->cabang), 'Data Pegawai '.Carbon::now()->format('d-m-Y').'.xlsx');
    }
}
