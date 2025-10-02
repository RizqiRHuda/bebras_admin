<?php
namespace App\Http\Controllers;

use App\Models\Kontak;
use App\Models\KontakDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KontakController extends Controller
{
    public function index()
    {
        return view('kontak.index');
    }

    public function list()
    {
        $kontaks = Kontak::query();
        return DataTables::of($kontaks)
            ->addIndexColumn()
            ->addColumn('actions', function ($row) {
                return '
                <button class="btn btn-sm btn-info btn-detail" data-id="' . $row->id . '">Detail</button>
                <button class="btn btn-sm btn-warning btn-edit" data-id="' . $row->id . '">Edit</button>
                <button class="btn btn-sm btn-danger btn-hapus" data-id="' . $row->id . '">Hapus</button>
            ';
            })
            ->rawColumns(['actions'])

            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'           => 'required|string|max:255',
            'institusi'      => 'nullable|string|max:255',
            'alamat'         => 'nullable|string',
            'detail'         => 'nullable|array',
            'detail.*.tipe'  => 'required_with:detail|in:email,url,telepon,fax,lainnya',
            'detail.*.nilai' => 'required_with:detail|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $kontak = Kontak::create([
                'nama'      => $request->nama,
                'institusi' => $request->institusi ? $request->institusi : "-",
                'alamat'    => $request->alamat ? $request->alamat : "-",
            ]);

            if ($request->has('detail')) {
                foreach ($request->detail as $d) {
                    $kontak->details()->create([
                        'tipe'  => $d['tipe'],
                        'nilai' => $d['nilai'],
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kontak berhasil disimpan',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan kontak: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function detail($id)
    {
        $kontak = Kontak::with('details')->find($id);

        if (! $kontak) {
            return response()->json(['details' => []], 404);
        }

        return response()->json(['details' => $kontak->details]);
    }

    public function edit($id)
    {
        $kontak = Kontak::with('details')->findOrFail($id);
        return response()->json([
            'id'        => $kontak->id,
            'nama'      => $kontak->nama,
            'institusi' => $kontak->institusi,
            'alamat'    => $kontak->alamat,
            'details'   => $kontak->details,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nama'           => 'required|string|max:255',
            'institusi'      => 'nullable|string|max:255',
            'alamat'         => 'nullable|string',
            'detail.*.tipe'  => 'required|string',
            'detail.*.nilai' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $kontak = Kontak::findOrFail($id);

            // Update kontak utama
            $kontak->update([
                'nama'      => $request->nama,
                'institusi' => $request->institusi,
                'alamat'    => $request->alamat,
            ]);

            $existingDetailIds  = $kontak->details()->pluck('id')->toArray();
            $submittedDetailIds = [];

            if ($request->has('detail')) {
                foreach ($request->detail as $d) {
                    if (! empty($d['id'])) {
                        // Update detail lama
                        $detail = KontakDetail::find($d['id']);
                        if ($detail) {
                            $detail->update([
                                'tipe'  => $d['tipe'],
                                'nilai' => $d['nilai'],
                            ]);
                            $submittedDetailIds[] = $detail->id;
                        }
                    } else {
                        // Tambah detail baru
                        $newDetail = $kontak->details()->create([
                            'tipe'  => $d['tipe'],
                            'nilai' => $d['nilai'],
                        ]);
                        $submittedDetailIds[] = $newDetail->id;
                    }
                }
            }

            // Hapus detail yang tidak ada di modal (dihapus user)
            $toDelete = array_diff($existingDetailIds, $submittedDetailIds);
            if (! empty($toDelete)) {
                KontakDetail::whereIn('id', $toDelete)->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data kontak berhasil diperbarui',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $kontak = Kontak::findOrFail($id);
            $kontak->details()->delete();
            $kontak->delete();
            return response()->json([
                'success' => true,
                'message' => 'Kontak berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

}
