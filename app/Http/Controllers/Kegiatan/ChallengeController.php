<?php
namespace App\Http\Controllers\Kegiatan;

use App\Http\Controllers\Controller;
use App\Models\BebrasChallenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class ChallengeController extends Controller
{
    private function breadCrumbs($currentLabel, $currentUrl = null)
    {
        return [
            ['label' => 'Home', 'route' => 'admin.dashboard'],
            ['label' => 'Bebras Challenge', 'url' => route('challenge.index')],
            ['label' => $currentLabel, 'url' => $currentUrl],
        ];
    }

    public function index()
    {
        $breadcrumbs = $this->breadCrumbs('Halaman Bebras Challenge');
        return view('kegiatan.challenge.index', compact('breadcrumbs'));
    }

    public function getData(Request $request)
    {
        $data = BebrasChallenge::select(['id', 'tahun', 'title', 'gambar']);

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('tahun', function ($row) {
                return $row->tahun ? $row->tahun : '-';
            })
            ->addColumn('gambar', function ($row) {
                if (! $row->gambar) {
                    return '<span class="badge bg-secondary">Tidak ada</span>';
                }

                $url = asset('storage/' . $row->gambar);
                return '<img src="' . $url . '" alt="gambar" width="70" height="70"
                    style="object-fit:cover; border-radius:6px;">';
            })

            ->addColumn('aksi', function ($row) {
                $urlEdit = route('challenge.edit', $row->id);
                return '
                <a href="' . $urlEdit . '" class="btn btn-sm btn-warning">Edit</a>
                <button class="btn btn-sm btn-danger" onclick="hapusData(' . $row->id . ')">Hapus</button>
            ';
            })

            ->rawColumns(['gambar', 'aksi'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumbs = $this->breadCrumbs('Halaman Bebras Challenge');
  
        return view('kegiatan.challenge.form', compact( 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun'      => 'required|digits:4|integer',
            'title'      => 'required|string|max:255',
            'tagline'    => 'nullable|string',
            'gambar'     => 'nullable|image|max:2048',
            'table_json' => 'nullable',
            'content'    => 'required',
        ]);

        // dd($validated);

        DB::beginTransaction();

        try {
            $slug = \Str::slug($validated['title']);

            $path = null;
            if ($request->hasFile('gambar')) {
                $path = $request->file('gambar')->store('bebras_challenge', 'public');
            }

            $data = BebrasChallenge::create([
                'tahun'      => $validated['tahun'],
                'title'      => $validated['title'],
                'slug'       => $slug,
                'tagline'    => $validated['tagline'] ?? null,
                'gambar'     => $path,
                'table_json' => $request->table_json ? json_decode($request->table_json, true) : null,
                'content'    => $validated['content'],
            ]);

            DB::commit();

            return redirect()->route('challenge.index')->with('success', 'Data Bebras Challenge berhasil ditambahkan!');
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error("Error Store Bebras Challenge: " . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        $breadcrumbs = $this->breadCrumbs('Halaman Bebras Challenge');
        $data        = BebrasChallenge::findOrFail($id);
        return view('kegiatan.challenge.form', compact('data', 'breadcrumbs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun'      => 'required|integer|digits:4',
            'title'      => 'required|string|max:255',
            'tagline'    => 'nullable|string',
            'gambar'     => 'nullable|image|max:2048',
            'table_json' => 'nullable',
            'content'    => 'required',
        ]);

        DB::beginTransaction();
        try {

            $data = BebrasChallenge::findOrFail($id);
            $slug = Str::slug($request->title);

            $path = $data->gambar;

            if ($request->hasFile('gambar')) {
                if ($data->gambar && Storage::disk('public')->exists($data->gambar)) {
                    Storage::disk('public')->delete($data->gambar);
                }

                $path = $request->file('gambar')->store('bebras_challenge/gambar', 'public');
            }
            $data->update([
                'tahun'      => $request->tahun,
                'title'      => $request->title,
                'slug'       => $slug,
                'tagline'    => $request->tagline,
                'gambar'     => $path,
                'table_json' => $request->table_json,
                'content'    => $request->content,
            ]);

            DB::commit();

            return redirect()
                ->route('challenge.index')
                ->with('success', 'Data challenge berhasil diperbarui!');
        } catch (\Throwable $th) {

            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat mengupdate data.');
        }
    }

    public function destroy($id)
    {
        try {
            $data = BebrasChallenge::findOrFail($id);
            if ($data->gambar && Storage::disk('public')->exists($data->gambar)) {
                Storage::disk('public')->delete($data->gambar);
            }
            $data->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data challenge berhasil dihapus.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus data.',
            ], 500);
        }
    }

}
