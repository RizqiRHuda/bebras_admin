<?php
namespace App\Http\Controllers\Kegiatan;

use App\Http\Controllers\Controller;
use App\Imports\HasilPengumumanImport;
use App\Models\Pengumuman\KategoriPengumuman;
use App\Models\Pengumuman\TahunPengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;use Yajra\DataTables\Facades\DataTables;

class PengumumanController extends Controller
{
    public function index()
    {
        $kategori = KategoriPengumuman::all();
        return view('kegiatan.pengumuman.index_pengumuman', compact('kategori'));
    }

    public function getData(Request $request)
    {
        $data = DB::table('hasil_pengumuman as h')
            ->join('tahun_pengumuman as t', 't.id', '=', 'h.id_tahun')
            ->join('kategori_pengumuman as k', 'k.id', '=', 'h.id_kategori')
            ->select([
                DB::raw('MIN(h.id) as id'),
                'h.id_tahun',
                't.tahun',
                'k.nama_kategori as kategori',
            ])
            ->groupBy('h.id_tahun', 't.tahun', 'h.id_kategori', 'k.nama_kategori')
            ->orderBy('t.tahun', 'desc');

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun'       => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'id_kategori' => 'required|exists:kategori_pengumuman,id',
            'file_excel'  => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        DB::beginTransaction();

        try {
            $tahun = TahunPengumuman::create(['tahun' => $request->tahun]);

            Excel::import(new HasilPengumumanImport(
                $tahun->id,
                $validated['id_kategori']
            ), $request->file('file_excel'));

            DB::commit();
            return back()->with('success', 'Data hasil berhasil diimport!');

        } catch (\Throwable $th) {

            DB::rollBack();
            if ($th instanceof \Illuminate\Database\QueryException  &&
                $th->errorInfo[1] == 1062) {
                return back()->with('error', 'Data gagal diimport: terdapat data duplikat (username sudah ada).');
            }
            return back()->with('error', 'Terjadi kesalahan saat memproses data. Silakan periksa kembali file Anda.');
        }
    }

}
