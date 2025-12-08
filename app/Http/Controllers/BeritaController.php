<?php
namespace App\Http\Controllers;

use App\Models\Berita\BeritaForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    private function breadCrumbs($currentLabel, $currentUrl = null)
    {
        return [
            ['label' => 'Home', 'route' => 'user.dashboard'],
            ['label' => 'Bebras Berita', 'url' => route('berita.index')],
            ['label' => $currentLabel, 'url' => $currentUrl],
        ];
    }

    public function index()
    {
        $breadcrumbs = $this->breadCrumbs('Halaman Berita');
        return view('user.page_berita', compact('breadcrumbs'));
    }

    public function dataBerita()
    {
        $berita = BeritaForm::with(['review' => function ($q) {
            $q->latest();
        }, 'user'])->get();

        return datatables()->of($berita)
            ->addIndexColumn()
            ->addColumn('judul', fn($row) => $row->title)
            ->addColumn('gambar', function ($row) {
                if (! $row->gambar) {
                    return '<span class="badge bg-secondary">Tidak ada</span>';
                }

                return '<img src="' . $row->gambar . '" width="80" />';
            })

            ->addColumn('status', fn($row) => ucfirst($row->status))
            ->addColumn('tanggal', function ($row) {
                return $row->created_at
                    ? $row->created_at->format('d/m/Y')
                    : '-';
            })
            ->addColumn('note', function ($row) {
                $review = $row->review->first();
                return $review ? ($review->note ?? '-') : '-';
            })
            ->addColumn('aksi', function ($row) {

                $editDisabled   = '';
                $deleteDisabled = '';
                $reviewButton   = '';
                if (auth()->user()->roles->first()->name == 'user') {
                    if (in_array($row->status, ['published', 'approved'])) {
                        $editDisabled   = 'disabled';
                        $deleteDisabled = 'disabled';
                    }

                    return '
                    <button class="btn btn-sm btn-warning" ' . $editDisabled . ' onclick="editData(' . $row->id . ')">Edit</button>
                    <button class="btn btn-sm btn-danger" ' . $deleteDisabled . ' onclick="hapusData(' . $row->id . ')">Hapus</button>
                ';
                }

                if (auth()->user()->role == 'admin') {
                    if (! in_array($row->status, ['approved', 'rejected'])) {
                        $reviewButton = '<button class="btn btn-success btn-sm review-btn" data-id="' . $row->id . '">Review</button>';
                    }

                    return $reviewButton;
                }

            })
            ->rawColumns(['gambar', 'aksi'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumbs = $this->breadCrumbs('Halaman Berita');
        return view('user.form_berita', compact('breadcrumbs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'  => 'required|string|max:255',
            'konten' => 'required',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $slug         = Str::slug($request->title);
            $originalSlug = $slug;
            $counter      = 1;

            while (BeritaForm::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            $gambarUrl = null;

            $gambarPath = null;
            if ($request->hasFile('gambar')) {
                $gambarPath = $request->file('gambar')->store('berita', 'public');
                $gambarUrl  = url('storage/' . $gambarPath);
            }

            BeritaForm::create([
                'user_id' => auth()->id(),
                'title'   => $request->title,
                'slug'    => $slug,
                'konten'  => $request->konten,
                'gambar'  => $gambarUrl,
                'status'  => 'submitted',
            ]);

            DB::commit();

            return redirect()
                ->route('berita.index')
                ->with('success', 'Berita berhasil dibuat!');
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
        $data        = BeritaForm::findOrFail($id);
        $breadcrumbs = $this->breadCrumbs('Edit Berita', route('berita.edit', $id));

        return view('user.form_berita', compact('data', 'breadcrumbs'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'  => 'required|string|max:255',
            'konten' => 'required',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $berita = BeritaForm::findOrFail($id);
            $slug   = Str::slug($request->title);
            if ($slug !== $berita->slug) {
                $originalSlug = $slug;
                $counter      = 1;
                while (BeritaForm::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                    $slug = $originalSlug . '-' . $counter++;
                }
            } else {
                $slug = $berita->slug;
            }

            if ($request->hasFile('gambar')) {

                if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
                    Storage::disk('public')->delete($berita->gambar);
                }

                $gambarPath = $request->file('gambar')->store('berita', 'public');
            } else {

                $gambarPath = $berita->gambar;
            }

            $berita->update([
                'title'  => $request->title,
                'slug'   => $slug,
                'konten' => $request->konten,
                'gambar' => $gambarPath,
            ]);

            DB::commit();

            return redirect()
                ->route('berita.index')
                ->with('success', 'Berita berhasil diperbarui!');

        } catch (\Throwable $e) {

            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $data = BeritaForm::findOrFail($id);

        if ($data->gambar && Storage::disk('public') - exists($data->gambar)) {
            Storage::disk('public')->delete($data->gambar);
        }

        $data->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Berita berhasil dihapus',
        ]);
    }
}
