<?php
namespace App\Http\Controllers;

use App\Models\Latihan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class LatihanController extends Controller
{
    public function index()
    {
        return view('latihan.index');
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $latihan = Latihan::query();
            return DataTables::of($latihan)
                ->addIndexColumn()
                ->addColumn('gambar', function ($row) {
                    if ($row->gambar) {
                        return '<div class="d-flex justify-content-center">
                            <img src="' . asset('storage/' . $row->gambar) . '"
                                alt="' . $row->nama . '"
                                class="img-thumbnail shadow-sm rounded"
                                style="width: 80px; height: 80px; object-fit: cover; transition: transform 0.2s;"
                                onmouseover="this.style.transform=\'scale(1.1)\'"
                                onmouseout="this.style.transform=\'scale(1)\'">
                        </div>';
                    }
                    return '-';
                })
                ->addColumn('actions', function ($row) {
                    return '
                     <button class="btn btn-sm btn-info btn-detail" data-id="' . $row->id . '">Detail</button>
                    <button class="btn btn-sm btn-warning btn-edit" data-id="' . $row->id . '">Edit</button>
                    <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">Delete</button>
                ';
                })
                ->rawColumns(['gambar', 'actions'])
                ->make(true);
        }

    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'link'      => 'nullable|string',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $path = null;

            if ($request->hasFile('gambar')) {
                $path = $request->file('gambar')->store('latihan', 'public');
            }

            $latihan = Latihan::create([
                'nama'      => $request->nama,
                'deskripsi' => $request->deskripsi,
                'link'      => $request->link,
                'gambar'    => $path,
            ]);

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan!',
                'data'    => $latihan,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $latihan = Latihan::findOrFail($id);
        return response()->json($latihan);
    }

    public function update(Request $request, $id)
    {
        $latihan = Latihan::findOrFail($id);

        $validate = $request->validate([
            'nama'      => 'required',
            'deskripsi' => 'nullable',
            'link'      => 'nullable',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            if ($latihan->gambar && Storage::disk('public')->exists($latihan->gambar)) {
                Storage::disk('public')->delete($latihan->gambar);
            }
            $path               = $request->file('gambar')->store('latihan', 'public');
            $validate['gambar'] = $path;
        }

        $latihan->update($validate);

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $latihan = Latihan::findOrFail($id);
        if ($latihan->gambar && Storage::disk('public')->exists($latihan->gambar)) {
            Storage::disk('public')->delete($latihan->gambar);
        }

        $latihan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);
    }

    public function deskripsi($id)
    {
        $latihan = Latihan::findOrFail($id);
        return response()->json($latihan);
    }

}
