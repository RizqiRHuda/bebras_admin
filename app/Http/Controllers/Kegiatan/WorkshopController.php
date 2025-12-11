<?php
namespace App\Http\Controllers\Kegiatan;

use App\Http\Controllers\Controller;
use App\Models\Workshop\TahunWorkshop;
use App\Models\Workshop\Workshop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class WorkshopController extends Controller
{
    private function breadCrumbs($currentLabel, $currentUrl = null)
    {
        return [
            ['label' => 'Home', 'route' => 'admin.dashboard'],
            ['label' => 'Workshop', 'url' => route('page_workhop.index')],
            ['label' => $currentLabel, 'url' => $currentUrl],
        ];
    }

    public function index()
    {
        $breadcrumbs = $this->breadCrumbs('Halaman Workshop');

        return view('kegiatan.workshop.index', compact('breadcrumbs'));
    }

    public function dataWorkshop()
    {
        $breadcrumbs = $this->breadCrumbs('Halaman Workshop');
        $data        = Workshop::with('tahun')->get();
        return view('kegiatan.workshop.page_workshop', compact('breadcrumbs', 'data'));
    }

    public function listWorkshop(Request $request)
    {
        $lists = Workshop::with('tahun')->select(['id', 'id_tahun', 'lokasi', 'gambar', 'tanggal']);

        return DataTables::of($lists)
            ->addIndexColumn()
            ->addColumn('tahun', function ($row) {
                return $row->tahun ? $row->tahun->tahun : '-';
            })
            ->addColumn('gambar', function ($row) {

                if (! $row->gambar) {
                    return '<span class="badge bg-secondary">Tidak ada</span>';
                }

                $url = $row->gambar;

                return '<img src="' . $url . '" alt="gambar" width="70" height="70"
                    style="object-fit:cover; border-radius:6px;">';
            })
            ->addColumn('tanggal', function ($row) {
                return $row->tanggal
                    ? Carbon::parse($row->tanggal)->format('d-m-Y')
                    : '-';
            })
            ->addColumn('aksi', function ($row) {
                $urlEdit = route('workshop.edit', $row->id);

                return '
                <a href="' . $urlEdit . '" class="btn btn-sm btn-warning">Edit</a>
                <button class="btn btn-sm btn-danger" onclick="hapusData(' . $row->id . ')">Hapus</button>
            ';
            })
            ->rawColumns(['aksi', 'gambar'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun'   => 'required|integer|digits:4',
            'title'   => 'required|string|max:255',
            'lokasi'  => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
            'gambar'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'konten'  => 'nullable',
        ]);

        DB::beginTransaction();

        try {

            // Cek apakah tahun sudah ada
            $tahunWorkshop = TahunWorkshop::firstOrCreate(
                ['tahun' => $request->tahun],
                ['slug' => Str::slug($request->tahun)]
            );

            // Upload gambar jika ada
            $gambarUrl  = null;
            $gambarPath = null;
            if ($request->hasFile('gambar')) {
                $gambarPath = $request->file('gambar')->store('workshop/gambar', 'public');
                $gambarUrl  = url('storage/' . $gambarPath);
            }

            // Konten JSON
            $konten = $request->konten ? ['html' => $request->konten] : [];

            // Simpan workshop
            Workshop::create([
                'id_tahun' => $tahunWorkshop->id,
                'title'    => $request->title,
                'lokasi'   => $request->lokasi,
                'tanggal'  => $request->tanggal,
                'gambar'   => $gambarUrl,
                'konten'   => $konten,
            ]);

            DB::commit();

            return redirect()
                ->route('workshop.index')
                ->with('success', 'Workshop berhasil ditambahkan!');

        } catch (\Throwable $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $data = Workshop::findOrFail($id);
        return view('kegiatan.workshop.index', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun'   => 'required|integer|digits:4',
            'title'   => 'required|string|max:255',
            'lokasi'  => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
            'gambar'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'konten'  => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            $workshop = Workshop::findOrFail($id);

            $tahunWorkshop = TahunWorkshop::firstOrCreate(
                ['tahun' => $request->tahun],
                ['slug' => Str::slug($request->tahun)]
            );

            $gambarUrl = $workshop->gambar;

            if ($request->hasFile('gambar')) {
                if ($workshop->gambar) {
                    $oldPath = str_replace(url('storage') . '/', '', $workshop->gambar);

                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                $gambarPath = $request->file('gambar')->store('workshop/gambar', 'public');
                $gambarUrl  = url('storage/' . $gambarPath);
            }

            $konten = $request->konten ? ['html' => $request->konten] : [];

            $tanggal = $request->filled('tanggal')
                ? $request->tanggal
                : $workshop->tanggal;

            $workshop->update([
                'id_tahun' => $tahunWorkshop->id,
                'title'    => $request->title,
                'lokasi'   => $request->lokasi,
                'tanggal'  => $tanggal,
                'gambar'   => $gambarUrl,
                'konten'   => $konten,
            ]);

            DB::commit();

            return redirect()
                ->route('page_workhop.index')
                ->with('success', 'Workshop berhasil diperbarui!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $data = Workshop::findOrFail($id);

        if ($data->gambar && Storage::disk('public')->exists($data->gambar)) {
            Storage::disk('public')->delete($data->gambar);
        }

        $data->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Workshop berhasil dihapus',
        ]);
    }

}
