<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Berita\BeritaForm;
use Illuminate\Support\Facades\DB;
use App\Models\Berita\ReviewBerita;


class ReviewBeritaController extends Controller
{
    public function index()
    {
        $berita = BeritaForm::whereIn('status', ['submitted', 'review'])
            ->with('user')
            ->with('review')
            ->get();

        return view('review_berita.page_review', compact('berita'));
    }

    public function getData()
    {
        $query = BeritaForm::with('user')->select('berita.*');

        return datatables()->eloquent($query)
            ->addIndexColumn()
            ->addColumn('penulis', function ($row) {
                return $row->user->name;
            })
            ->addColumn('tanggal', function ($row) {
                return $row->created_at->format('d-m-Y');
            })
         ->addColumn('aksi', function ($row) {

                    if ($row->status === 'published') {
                        return '<span class="badge bg-success">Sudah Disetujui</span>';
                    }

                    if ($row->status === 'rejected') {
                        return '
                            <a href="' . route('review_berita.detail', $row->id) . '"
                                class="btn btn-warning btn-sm">
                                Review Ulang (Ditolak)
                            </a>
                        ';
                    }

                    return '
                        <a href="' . route('review_berita.detail', $row->id) . '"
                            class="btn btn-primary btn-sm">
                            Review Berita
                        </a>
                    ';
                })

            ->rawColumns(['detail', 'aksi'])
            ->make(true);
    }

    public function tampilBerita($id)
    {
        $berita = BeritaForm::with(['user', 'review.reviewer'])
            ->where('id', $id)
            ->first();

        if (! $berita) {
            return redirect()->back()->with('error', 'Berita tidak ditemukan.');
        }

        return view('review_berita.tampil_berita', [
            'berita' => $berita,
        ]);
    }

    public function submitReview(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'note'   => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $berita = BeritaForm::findOrFail($id);
            ReviewBerita::create([
                'id_berita' => $berita->id,
                'user_id'   => auth()->id(), 
                'status'    => $request->status,
                'note'      => $request->note,
            ]);

            $berita->status = $request->status === 'approved'
                ? 'published'
                : 'rejected';

            $berita->save();

            DB::commit();

            return redirect()->route('review_berita.index')->with('success', 'Review berhasil diproses.');

        } catch (\Exception $e) {

            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

}
