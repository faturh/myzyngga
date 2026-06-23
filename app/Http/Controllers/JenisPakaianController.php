<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cabang;
use App\Models\JenisPakaian;
use Illuminate\Http\Request;
use App\Models\HargaJenisLayanan;
use App\Exports\JenisPakaianExport;
use App\Imports\JenisPakaianImport;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Layanan\JenisPakaianRequest;

class JenisPakaianController extends Controller
{
    public function index()
    {
        $title = "Jenis Pakaian";
        $userCabang = auth()->user()->cabang_id;
        $userRole = auth()->user()->roles[0]->name;
        $cabang = Cabang::where('id', $userCabang)->withTrashed()->first();

        if ($userRole != 'manajer_laundry') {
            return abort(403);
        }

        $jenisPakaian = JenisPakaian::orderBy('nama', 'asc')->get();
        $jenisPakaianTrash = collect();

        return view('operator.dashboard.jenis-pakaian.index', compact('title', 'jenisPakaian', 'jenisPakaianTrash', 'cabang'));
    }

    public function store(JenisPakaianRequest $request)
    {
        $validated = $request->validated();
        $userRole = auth()->user()->roles[0]->name;

        $tambah = JenisPakaian::create($validated);

        if ($userRole == 'manajer_laundry') {
            if ($tambah) {
                return to_route('jenis-pakaian')->with('success', 'Jenis Pakaian Berhasil Ditambahkan');
            } else {
                return to_route('jenis-pakaian')->with('error', 'Jenis Pakaian Gagal Ditambahkan');
            }
        } else if ($userRole == 'pic') {
            if ($tambah) {
                return back()->with('success', 'Jenis Pakaian Berhasil Ditambahkan');
            } else {
                return back()->with('error', 'Jenis Pakaian Gagal Ditambahkan');
            }
        }
    }

    public function show(Request $request)
    {
        $jenisPakaian = JenisPakaian::findOrFail($request->id);
        return $jenisPakaian;
    }

    public function edit(Request $request)
    {
        $jenisPakaian = JenisPakaian::findOrFail($request->id);
        return $jenisPakaian;
    }

    public function update(JenisPakaianRequest $request)
    {
        $validated = $request->validated();
        $userRole = auth()->user()->roles[0]->name;
        $perbarui = JenisPakaian::where('id', $request->id)->update($validated);

        if ($userRole == 'manajer_laundry') {
            if ($perbarui) {
                return to_route('jenis-pakaian')->with('success', 'Jenis Pakaian Berhasil Diperbarui');
            } else {
                return to_route('jenis-pakaian')->with('error', 'Jenis Pakaian Gagal Diperbarui');
            }
        } else if ($userRole == 'pic') {
            if ($perbarui) {
                return back()->with('success', 'Jenis Pakaian Berhasil Diperbarui');
            } else {
                return back()->with('error', 'Jenis Pakaian Gagal Diperbarui');
            }
        }
    }

    public function delete(Request $request)
    {
        HargaJenisLayanan::where('jenis_pakaian_id', $request->id)->delete();
        $hapus = JenisPakaian::where('id', $request->id)->delete();
        if ($hapus) {
            abort(200, 'Jenis Pakaian Berhasil Dihapus');
        } else {
            abort(400, 'Jenis Pakaian Gagal Dihapus');
        }
    }

    public function restore(Request $request)
    {
        abort(200, 'Jenis Pakaian Berhasil Dipulihkan');
    }

    public function destroy(Request $request)
    {
        HargaJenisLayanan::where('jenis_pakaian_id', $request->id)->delete();
        $hapusPermanen = JenisPakaian::where('id', $request->id)->delete();
        if ($hapusPermanen) {
            abort(200, 'Jenis Pakaian Berhasil Dihapus');
        } else {
            abort(400, 'Jenis Pakaian Gagal Dihapus');
        }
    }

    public function import(Request $request)
    {
        $userRole = auth()->user()->roles[0]->name;
        try {
            Excel::import(new JenisPakaianImport, $request->file('impor'));
            if ($userRole == 'pic') {
                return to_route('layanan-cabang.cabang', $request->cabang)->with('success', 'Jenis Pakaian Berhasil Ditambahkan');
            } else if ($userRole == 'manajer_laundry') {
                return to_route('jenis-pakaian')->with('success', 'Jenis Pakaian Berhasil Ditambahkan');
            }
        } catch(\Exception $ex) {
            Log::info($ex);
            if ($userRole == 'pic') {
                return to_route('layanan-cabang.cabang', $request->cabang)->with('error', 'Jenis Pakaian Gagal Ditambahkan');
            } else if ($userRole == 'manajer_laundry') {
                return to_route('jenis-pakaian')->with('error', 'Jenis Pakaian Gagal Ditambahkan');
            }
        }
    }

    public function export(Request $request)
    {
        return Excel::download(new JenisPakaianExport(), 'Data Jenis Pakaian '.Carbon::now()->format('d-m-Y').'.xlsx');
    }
}

