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

class UserController extends Controller
{
    private function mapUserCollection($users)
    {
        return $users->map(function ($u) {
            return (object) [
                'id' => $u->id,
                'nama' => $u->name ?? $u->username,
                'slug' => $u->slug,
                'telepon' => $u->phone ?? '-',
                'created_at' => $u->created_at,
                'deleted_at' => $u->deleted_at,
                'nama_cabang' => $u->cabang?->nama ?? '-',
                'user' => (object) [
                    'email' => $u->email,
                    'roles' => $u->roles,
                ]
            ];
        });
    }

    public function index()
    {
        $title = "Users Management";
        $userRole = auth()->user()->roles[0]->name;
        $cabang = Cabang::get();
        $role = Role::get();

        if ($userRole == 'lurah' || $userRole == 'pic') {
            $manajer = User::whereHas('roles', function($q) { $q->where('name', 'manajer_laundry'); })->with('cabang')->get();
            $pegawai = User::whereHas('roles', function($q) { $q->where('name', 'pegawai_laundry'); })->with('cabang')->get();
            $gamis = collect();

            $manajerTrash = User::whereHas('roles', function($q) { $q->where('name', 'manajer_laundry'); })->onlyTrashed()->with('cabang')->get();
            $pegawaiTrash = User::whereHas('roles', function($q) { $q->where('name', 'pegawai_laundry'); })->onlyTrashed()->with('cabang')->get();
            $gamisTrash = collect();

            return view('operator.dashboard.user.index', [
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

        } elseif ($userRole == 'manajer_laundry') {
            $cabangId = auth()->user()->cabang_id;
            $pegawai = User::where('cabang_id', $cabangId)->whereHas('roles', function($q) { $q->where('name', 'pegawai_laundry'); })->with('cabang')->get();
            $gamis = collect();

            $pegawaiTrash = User::where('cabang_id', $cabangId)->whereHas('roles', function($q) { $q->where('name', 'pegawai_laundry'); })->onlyTrashed()->with('cabang')->get();
            $gamisTrash = collect();

            return view('operator.dashboard.user.index', [
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
        $user = User::where('slug', $request->user)->first();

        if ($user == null || $user->cabang_id != auth()->user()->cabang_id && $userRole != 'lurah' && $userRole != 'pic') {
            abort(404, 'USER TIDAK DITEMUKAN.');
        } else if ($user->slug == auth()->user()->slug ) {
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

        return view('operator.dashboard.user.lihat', compact('title', 'user', 'profile', 'trash'));
    }

    public function create()
    {
        $title = "Tambah User";
        $userRole = auth()->user()->roles[0]->name;

        if ($userRole == 'lurah') {
            abort(403, 'USER DOES NOT HAVE PERMISSION.');
        }

        $cabang = Cabang::where('id', auth()->user()->cabang_id)->withTrashed()->first();
        if ($cabang && $cabang->deleted_at) {
            abort(403, 'USER DOES NOT HAVE PERMISSION.');
        }

        $kkGamis = collect();
        $isCabang = [false];

        if ($userRole == 'pic') {
            $role = Role::where('name', '!=', 'lurah')->where('name', '!=', 'rw')->where('name', '!=', 'pic')->get();
            $cabang = Cabang::where('deleted_at', null)->get();
        } else if ($userRole == 'manajer_laundry') {
            $role = Role::where('name', '!=', 'lurah')->where('name', '!=', 'manajer_laundry')->where('name', '!=', 'rw')->where('name', '!=', 'pic')->get();
            $cabang = Cabang::where('deleted_at', null)->where('id', auth()->user()->cabang_id)->get();
        }
        return view('operator.dashboard.user.tambah', compact('title', 'cabang', 'role', 'kkGamis', 'isCabang'));
    }

    public function store(Request $request)
    {
        $validatedUser = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'cabang_id' => 'nullable|integer',
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
        ], [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute sudah ada, silakan isi yang lain.',
            'max' => ':attribute tidak boleh lebih dari :max karakter.',
            'integer' => ':attribute harus berupa angka.',
            'confirmed' => 'Konfirmasi :attribute tidak sama.',
        ]);

        $user = User::create([
            'username' => $validatedUser['username'],
            'email' => $validatedUser['email'],
            'password' => Hash::make($validatedUser['password']),
            'cabang_id' => $validatedUser['cabang_id'],
            'name' => $validatedUser['nama'],
            'phone' => $validatedUser['telepon'],
            'slug' => str()->slug($validatedUser['username']),
        ]);

        $user->assignRole($request->role);

        if ($user) {
            return to_route('user')->with('success', 'User Berhasil Ditambahkan');
        } else {
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

        if ($userRole == 'pic') {
            $role = Role::where('name', '!=', 'lurah')->where('name', '!=', 'rw')->where('name', '!=', 'pic')->get();
            $cabang = Cabang::where('deleted_at', null)->get();
        } else if ($userRole == 'manajer_laundry') {
            $role = Role::where('name', '!=', 'lurah')->where('name', '!=', 'manajer_laundry')->where('name', '!=', 'rw')->where('name', '!=', 'pic')->get();
            $cabang = Cabang::where('deleted_at', null)->where('id', auth()->user()->cabang_id)->get();
        }

        $user = User::where('slug', $request->user)->first();
        if ($user == null || $user->cabang_id != auth()->user()->cabang_id && $userRole != 'pic') {
            abort(404, 'USER TIDAK DITEMUKAN.');
        } else if ($user->slug == auth()->user()->slug ) {
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

        return view('operator.dashboard.user.ubah', compact('title', 'cabang', 'role', 'kkGamis', 'user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = User::where('slug', $request->user)->firstOrFail();
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user)],
            'cabang_id' => 'nullable|integer',
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
        ], [
            'required' => ':attribute harus diisi.',
            'unique' => ':attribute sudah ada, silakan isi yang lain.',
            'max' => ':attribute tidak boleh lebih dari :max karakter.',
            'integer' => ':attribute harus berupa angka.',
        ]);

        $userUpdate = $user->update([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'cabang_id' => $validated['cabang_id'],
            'name' => $validated['nama'],
            'phone' => $validated['telepon'],
            'slug' => str()->slug($validated['username']),
        ]);

        $user->syncRoles([$request->role]);

        if ($userUpdate) {
            return to_route('user')->with('success', 'User Berhasil Diperbarui');
        } else {
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

        $user = User::where('slug', $request->user)->first();
        if ($user == null || $user->cabang_id != auth()->user()->cabang_id && $userRole != 'pic') {
            abort(404, 'USER TIDAK DITEMUKAN.');
        } else if ($user->slug == auth()->user()->slug ) {
            return to_route('profile', $user->slug);
        }
        return view('operator.dashboard.user.ubahPassword', compact('title', 'user'));
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

        $updatePassword = User::where('slug', $request->slug)->update([
            'password' => Hash::make($validated['password']),
        ]);

        if ($updatePassword) {
            return to_route('user')->with('success', 'Password User Berhasil Diganti');
        } else {
            return to_route('user')->with('error', 'Password User Gagal Diganti');
        }
    }

    public function delete(Request $request)
    {
        $cabang = Cabang::where('id', auth()->user()->cabang_id)->withTrashed()->first();
        if ($cabang && $cabang->deleted_at) {
            abort(403, 'USER DOES NOT HAVE PERMISSION.');
        }

        $hapus = User::where('slug', $request->slug)->delete();
        if ($hapus) {
            abort(200, 'User Berhasil Dihapus');
        } else {
            abort(400, 'User Gagal Dihapus');
        }
    }

    public function trash(Request $request)
    {
        $title = "Detail User Trash";
        $trash = true;
        $userRole = auth()->user()->roles[0]->name;
        $user = User::where('slug', $request->user)->onlyTrashed()->first();

        if ($user == null || $user->cabang_id != auth()->user()->cabang_id && $userRole != 'lurah' && $userRole != 'pic') {
            abort(404, 'USER TIDAK DITEMUKAN.');
        } else if ($user->slug == auth()->user()->slug ) {
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

        return view('operator.dashboard.user.lihat', compact('title', 'user', 'profile', 'trash'));
    }

    public function restore(Request $request)
    {
        $cabang = Cabang::where('id', auth()->user()->cabang_id)->withTrashed()->first();
        if ($cabang && $cabang->deleted_at) {
            abort(403, 'USER DOES NOT HAVE PERMISSION.');
        }

        $pulih = User::where('slug', $request->slug)->restore();
        if ($pulih) {
            abort(200, 'User Berhasil Dihapus');
        } else {
            abort(400, 'User Gagal Dihapus');
        }
    }

    public function destroy(Request $request)
    {
        $cabang = Cabang::where('id', auth()->user()->cabang_id)->withTrashed()->first();
        if ($cabang && $cabang->deleted_at) {
            abort(403, 'USER DOES NOT HAVE PERMISSION.');
        }

        $user = User::where('slug', $request->slug)->onlyTrashed()->first();
        $userRole = $user->roles[0]->name;

        $user->removeRole($userRole);
        $hapusPermanen = $user->forceDelete();

        if ($hapusPermanen) {
            abort(200, 'User Berhasil Dihapus');
        } else {
            abort(400, 'User Gagal Dihapus');
        }
    }

    public function indexCabang(Request $request)
    {
        $title = "Users Management";

        $userRole = auth()->user()->roles[0]->name;
        if ($userRole != 'lurah' && $userRole != 'pic') {
            abort(403, 'USER DOES NOT HAVE THE RIGHT ROLES.');
        }

        $cabang = Cabang::where('slug', $request->cabang)->withTrashed()->first();
        if ($cabang == null || $cabang->deleted_at) {
            abort(404, 'CABANG TIDAK DITEMUKAN ATAU SUDAH DIHAPUS.');
        }

        $role = Role::get();

        $manajer = User::where('cabang_id', $cabang->id)->whereHas('roles', function($q) { $q->where('name', 'manajer_laundry'); })->get();
        $pegawai = User::where('cabang_id', $cabang->id)->whereHas('roles', function($q) { $q->where('name', 'pegawai_laundry'); })->get();
        $gamis = collect();

        $manajerTrash = User::where('cabang_id', $cabang->id)->whereHas('roles', function($q) { $q->where('name', 'manajer_laundry'); })->onlyTrashed()->get();
        $pegawaiTrash = User::where('cabang_id', $cabang->id)->whereHas('roles', function($q) { $q->where('name', 'pegawai_laundry'); })->onlyTrashed()->get();
        $gamisTrash = collect();

        return view('operator.dashboard.user.cabang.index-cabang', [
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
        if ($userRole != 'pic') {
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

        return view('operator.dashboard.user.tambah', compact('title', 'cabang', 'role', 'kkGamis', 'isCabang'));
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

