<?php
namespace App\Http\Controllers;

use App\Models\MenuSoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SoalController extends Controller
{
    private function breadCrumbs($currentLabel, $currentUrl = null)
    {
        return [
            ['label' => 'Home', 'route' => 'admin.dashboard'],
            ['label' => 'Soal Bebras', 'url' => route('soal_bebras.index')],
            ['label' => $currentLabel, 'url' => $currentUrl],
        ];
    }

    public function index(Request $request)
    {
        $breadcrumbs = $this->breadCrumbs('Halaman Soal Bebras');

        if ($request->ajax()) {
            $data = MenuSoal::with('parent')
                ->orderBy('urutan', 'asc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('parent', function ($row) {
                    return $row->parent ? $row->parent->nama_menu : '-';
                })
                ->addColumn('judul', function ($row) {
                    return $row->judul ?? '-';
                })
                ->addColumn('body', function ($row) {
                    return $row->body ? \Str::limit(strip_tags($row->body), 50) : '-';
                })
                ->make(true);
        }

        return view('soal.soal_index', compact('breadcrumbs'));
    }

    public function create()
    {
        $breadcrumbs = $this->breadcrumbs('Tambah Halaman Soal Bebras');
        $menuList    = MenuSoal::orderBy('urutan', 'asc')->get();

        return view('soal.form_soal', compact('breadcrumbs', 'menuList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:menu_soal,id',
            'nama_menu' => 'required|string|max:255',
            'slug'      => 'required|string|max:255|unique:menu_soal,slug',
            'judul'     => 'nullable|string|max:255',
            'body'      => 'nullable|string',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png',
            'urutan'    => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            $path = null;

            if ($request->hasFile('gambar')) {
                $path = $request->file('gambar')->store('soal-bebras', 'public');
            }

            MenuSoal::create([
                'parent_id' => $validated['parent_id'] ?? null,
                'nama_menu' => $validated['nama_menu'],
                'slug'      => $validated['slug'],
                'judul'     => $validated['judul'] ?? null,
                'body'      => $validated['body'] ?? null,
                'gambar'    => $path,
                'urutan'    => $validated['urutan'] ?? 0,
            ]);

            DB::commit();

            return redirect()
                ->route('soal_bebras.index')
                ->with('success', 'Data berhasil ditambahkan');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $breadcrumbs = $this->breadCrumbs('Edit Halaman Soal Bebras');
        $data        = MenuSoal::with('parent')->findOrFail($id);
        $menuList    = MenuSoal::where('id', '!=', $id)
            ->whereNull('parent_id')
            ->orderBy('urutan', 'desc')->get();
        return view('soal.form_soal', compact('data', 'breadcrumbs', 'menuList'));
    }

    public function update(Request $request, $id)
    {
        $data = MenuSoal::findOrFail($id);

        $validated = $request->validate([
            'parent_id' => 'nullable|exists:menu_soal,id',
            'nama_menu' => 'nullable|string|max:255',
            'slug'      => 'nullable|string|max:255|unique:menu_soal,slug,' . $id,
            'judul'     => 'nullable|string|max:255',
            'body'      => 'nullable|string',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png',
            'urutan'    => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            if ($request->hasFile('gambar')) {
                if ($data->gambar && Storage::disk('public')->exists($data->gambar)) {
                    Storage::disk('public')->delete($data->gambar);
                }

                $validated['gambar'] = $request->file('gambar')->store('soal-bebras', 'public');
            } else {
                unset($validated['gambar']);
            }

            $data->update($validated);

            DB::commit();

            return redirect()->route('soal_bebras.index')->with('success', 'Data berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $breadcrumbs = $this->breadCrumbs('Detail Soal Bebras');

        $menu = MenuSoal::with('parent')->findOrFail($id);

        return view('soal.detail_soal', compact('breadcrumbs', 'menu'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $data = MenuSoal::findOrFail($id);
            if ($data->gambar && Storage::disk('public')->exists($data->gambar)) {
                Storage::disk('public')->delete($data->gambar);
            }
            $data->delete();
            DB::commit();
            return redirect()
                ->route('soal_bebras.index')
                ->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

}
