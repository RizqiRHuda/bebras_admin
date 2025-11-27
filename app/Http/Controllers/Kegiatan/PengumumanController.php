<?php
namespace App\Http\Controllers\Kegiatan;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman\HasilPengumuman;
use App\Models\Pengumuman\KategoriPengumuman;
use App\Models\Pengumuman\TahunPengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PengumumanController extends Controller
{
    public function index()
    {
        $kategori = KategoriPengumuman::all();
        return view('kegiatan.pengumuman.index_pengumuman', compact('kategori'));
    }

    public function getData(Request $request)
    {
        $data = HasilPengumuman::with(['tahun', 'kategori'])
            ->select('hasil_pengumuman.*', 't.tahun as tahun_value', 'k.nama_kategori as kategori_value')
            ->join('tahun_pengumuman as t', 't.id', '=', 'hasil_pengumuman.id_tahun')
            ->join('kategori_pengumuman as k', 'k.id', '=', 'hasil_pengumuman.id_kategori')
            ->orderBy('t.tahun', 'desc');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('tahun', function($row) {
                return $row->tahun_value ?? '-';
            })
            ->addColumn('kategori', function($row) {
                return $row->kategori_value ?? '-';
            })
            ->addColumn('platform', function($row) {
                if ($row->is_uploaded) {
                    return '<span class="badge bg-dark"><i class="bi bi-cloud-upload"></i> Uploaded File</span>';
                }
                
                $badges = [
                    'google_sheets' => '<span class="badge bg-success">Google Sheets</span>',
                    'excel_online' => '<span class="badge bg-primary">Excel Online</span>',
                    'onedrive' => '<span class="badge bg-info">OneDrive</span>',
                    'other' => '<span class="badge bg-secondary">Other</span>',
                ];
                return $badges[$row->platform] ?? $row->platform;
            })
            ->editColumn('description', function($row) {
                return $row->description ? Str::limit($row->description, 50) : '-';
            })
            ->addColumn('action', function($row) {
                return '
                    <div class="d-flex gap-1 justify-content-center">
                        <a href="' . route('pengumuman.show', $row->id) . '" 
                           class="btn btn-sm btn-info text-white" 
                           title="Lihat Embed">
                            <i class="bi bi-eye-fill"></i>
                        </a>
                        <a href="' . route('pengumuman.edit', $row->id) . '" 
                           class="btn btn-sm btn-warning text-white" 
                           title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete" 
                                data-id="' . $row->id . '" 
                                title="Hapus">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['platform', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        // Validasi berbeda untuk upload vs embed
        if ($request->hasFile('excel_file')) {
            $validated = $request->validate([
                'tahun'       => 'required|integer|min:2000|max:' . (date('Y') + 1),
                'id_kategori' => 'required|exists:kategori_pengumuman,id',
                'excel_file'  => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
                'description' => 'nullable|string|max:500',
            ]);
        } else {
            $validated = $request->validate([
                'tahun'       => 'required|integer|min:2000|max:' . (date('Y') + 1),
                'id_kategori' => 'required|exists:kategori_pengumuman,id',
                'embed_url'   => 'required|url',
                'description' => 'nullable|string|max:500',
            ]);
        }

        DB::beginTransaction();

        try {
            $tahun = TahunPengumuman::firstOrCreate(['tahun' => $request->tahun]);

            if ($request->hasFile('excel_file')) {
                // Upload File
                $file = $request->file('excel_file');
                $filename = time() . '_' . Str::slug($request->tahun . '_' . $request->id_kategori) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('pengumuman', $filename, 'public');

                HasilPengumuman::create([
                    'id_tahun'    => $tahun->id,
                    'id_kategori' => $validated['id_kategori'],
                    'file_path'   => $path,
                    'is_uploaded' => true,
                    'platform'    => 'uploaded',
                    'description' => $validated['description'] ?? null,
                ]);

                $message = 'File Excel berhasil diupload!';
            } else {
                // Embed URL
                $platform = HasilPengumuman::detectPlatform($validated['embed_url']);
                $embedUrl = HasilPengumuman::convertToEmbedUrl($validated['embed_url'], $platform);

                HasilPengumuman::create([
                    'id_tahun'    => $tahun->id,
                    'id_kategori' => $validated['id_kategori'],
                    'embed_url'   => $embedUrl,
                    'platform'    => $platform,
                    'is_uploaded' => false,
                    'description' => $validated['description'] ?? null,
                ]);

                $message = 'Link embed berhasil disimpan!';
            }

            DB::commit();
            return back()->with('success', $message);

        } catch (\Throwable $th) {
            DB::rollBack();
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }

    public function show($id)
    {
        $hasil = HasilPengumuman::with(['tahun', 'kategori'])->findOrFail($id);
        return view('kegiatan.pengumuman.show_pengumuman', compact('hasil'));
    }

    public function edit($id)
    {
        try {
            $hasil = HasilPengumuman::with(['tahun', 'kategori'])->findOrFail($id);
            $kategori = KategoriPengumuman::all();
            return view('kegiatan.pengumuman.edit_pengumuman', compact('hasil', 'kategori'));
        } catch (\Throwable $th) {
            return redirect()->route('pengumuman.index')->with('error', 'Data tidak ditemukan!');
        }
    }

    public function update(Request $request, $id)
    {
        $hasil = HasilPengumuman::findOrFail($id);
        $updateType = $request->input('update_type', 'info');

        // Validasi berbeda berdasarkan update_type
        if ($updateType === 'file' && $request->hasFile('excel_file_edit')) {
            $validated = $request->validate([
                'tahun_edit'       => 'required|integer|min:2000|max:' . (date('Y') + 1),
                'id_kategori_edit' => 'required|exists:kategori_pengumuman,id',
                'excel_file_edit'  => 'required|file|mimes:xlsx,xls,csv|max:10240',
                'description_edit' => 'nullable|string|max:500',
            ]);
        } elseif ($updateType === 'embed') {
            $validated = $request->validate([
                'tahun_edit'       => 'required|integer|min:2000|max:' . (date('Y') + 1),
                'id_kategori_edit' => 'required|exists:kategori_pengumuman,id',
                'embed_url_edit'   => 'required|url',
                'description_edit' => 'nullable|string|max:500',
            ]);
        } else {
            // Only update info (tahun, kategori, deskripsi)
            $validated = $request->validate([
                'tahun_edit'       => 'required|integer|min:2000|max:' . (date('Y') + 1),
                'id_kategori_edit' => 'required|exists:kategori_pengumuman,id',
                'description_edit' => 'nullable|string|max:500',
            ]);
        }

        DB::beginTransaction();

        try {
            $tahun = TahunPengumuman::firstOrCreate(['tahun' => $request->tahun_edit]);
            $oldFilePath = $hasil->file_path;

            if ($updateType === 'file' && $request->hasFile('excel_file_edit')) {
                // Hapus file lama jika ada
                if ($hasil->is_uploaded && $oldFilePath) {
                    Storage::disk('public')->delete($oldFilePath);
                }

                // Upload File baru
                $file = $request->file('excel_file_edit');
                $filename = time() . '_' . Str::slug($request->tahun_edit . '_' . $request->id_kategori_edit) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('pengumuman', $filename, 'public');

                $hasil->update([
                    'id_tahun'    => $tahun->id,
                    'id_kategori' => $validated['id_kategori_edit'],
                    'file_path'   => $path,
                    'is_uploaded' => true,
                    'platform'    => 'uploaded',
                    'embed_url'   => null,
                    'description' => $validated['description_edit'] ?? null,
                ]);

                $message = 'File Excel berhasil diupdate!';
            } elseif ($updateType === 'embed') {
                // Hapus file lama jika sebelumnya upload file
                if ($hasil->is_uploaded && $oldFilePath) {
                    Storage::disk('public')->delete($oldFilePath);
                }

                // Update Embed URL
                $platform = HasilPengumuman::detectPlatform($validated['embed_url_edit']);
                $embedUrl = HasilPengumuman::convertToEmbedUrl($validated['embed_url_edit'], $platform);

                $hasil->update([
                    'id_tahun'    => $tahun->id,
                    'id_kategori' => $validated['id_kategori_edit'],
                    'embed_url'   => $embedUrl,
                    'platform'    => $platform,
                    'is_uploaded' => false,
                    'file_path'   => null,
                    'description' => $validated['description_edit'] ?? null,
                ]);

                $message = 'Link embed berhasil diupdate!';
            } else {
                // Update only info (tahun, kategori, deskripsi) - tidak mengubah file/URL
                $hasil->update([
                    'id_tahun'    => $tahun->id,
                    'id_kategori' => $validated['id_kategori_edit'],
                    'description' => $validated['description_edit'] ?? null,
                ]);

                $message = 'Data berhasil diupdate!';
            }

            DB::commit();
            return redirect()->route('pengumuman.index')->with('success', $message);

        } catch (\Throwable $th) {
            DB::rollBack();
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $hasil = HasilPengumuman::findOrFail($id);
            
            // Hapus file jika data merupakan uploaded file
            if ($hasil->is_uploaded && $hasil->file_path) {
                Storage::disk('public')->delete($hasil->file_path);
            }
            
            $hasil->delete();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus!']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $th->getMessage()], 500);
        }
    }

}
