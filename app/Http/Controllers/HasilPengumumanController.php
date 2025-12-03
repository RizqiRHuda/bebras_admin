<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman\HasilPengumuman;
use App\Models\Pengumuman\TahunPengumuman;
use App\Models\Pengumuman\KategoriPengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HasilPengumumanController extends Controller
{
    /**
     * Display a listing of hasil pengumuman
     */
    public function index()
    {
        $hasilPengumuman = HasilPengumuman::with(['tahun', 'kategori'])
            ->latest()
            ->paginate(10);

        return view('admin.pengumuman.hasil.index', compact('hasilPengumuman'));
    }

    /**
     * Show the form for creating new hasil pengumuman
     */
    public function create()
    {
        $tahunList = TahunPengumuman::orderBy('tahun', 'desc')->get();
        $kategoriList = KategoriPengumuman::all();

        return view('admin.pengumuman.hasil.create', compact('tahunList', 'kategoriList'));
    }

    /**
     * Store a newly created hasil pengumuman
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_tahun' => 'required|exists:tahun_pengumuman,id',
            'id_kategori' => 'required|exists:kategori_pengumuman,id',
            'description' => 'required|string|max:500',
            'type' => 'required|in:link,upload',
            'embed_url' => 'required_if:type,link|url|nullable',
            'file' => 'required_if:type,upload|file|mimes:xlsx,xls|max:10240|nullable',
        ]);

        $data = [
            'id_tahun' => $validated['id_tahun'],
            'id_kategori' => $validated['id_kategori'],
            'description' => $validated['description'],
        ];

        if ($validated['type'] === 'link') {
            // Handle link embed
            $platform = HasilPengumuman::detectPlatform($validated['embed_url']);
            $embedUrl = HasilPengumuman::convertToEmbedUrl($validated['embed_url'], $platform);

            $data['platform'] = $platform;
            $data['embed_url'] = $embedUrl;
            $data['is_uploaded'] = false;
            $data['file_path'] = null;
        } else {
            // Handle file upload
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('pengumuman', $fileName, 'public');

            $data['platform'] = 'upload';
            $data['embed_url'] = null;
            $data['is_uploaded'] = true;
            $data['file_path'] = $filePath;
        }

        HasilPengumuman::create($data);

        return redirect()->route('admin.pengumuman.hasil.index')
            ->with('success', 'Hasil pengumuman berhasil ditambahkan');
    }

    /**
     * Show the form for editing hasil pengumuman
     */
    public function edit(HasilPengumuman $hasil)
    {
        $tahunList = TahunPengumuman::orderBy('tahun', 'desc')->get();
        $kategoriList = KategoriPengumuman::all();

        return view('admin.pengumuman.hasil.edit', compact('hasil', 'tahunList', 'kategoriList'));
    }

    /**
     * Update hasil pengumuman
     */
    public function update(Request $request, HasilPengumuman $hasil)
    {
        $validated = $request->validate([
            'id_tahun' => 'required|exists:tahun_pengumuman,id',
            'id_kategori' => 'required|exists:kategori_pengumuman,id',
            'description' => 'required|string|max:500',
            'type' => 'required|in:link,upload',
            'embed_url' => 'required_if:type,link|url|nullable',
            'file' => 'nullable|file|mimes:xlsx,xls|max:10240',
        ]);

        $data = [
            'id_tahun' => $validated['id_tahun'],
            'id_kategori' => $validated['id_kategori'],
            'description' => $validated['description'],
        ];

        if ($validated['type'] === 'link') {
            // Delete old file if exists
            if ($hasil->is_uploaded && $hasil->file_path) {
                Storage::disk('public')->delete($hasil->file_path);
            }

            // Handle link embed
            $platform = HasilPengumuman::detectPlatform($validated['embed_url']);
            $embedUrl = HasilPengumuman::convertToEmbedUrl($validated['embed_url'], $platform);

            $data['platform'] = $platform;
            $data['embed_url'] = $embedUrl;
            $data['is_uploaded'] = false;
            $data['file_path'] = null;
        } else {
            // Handle file upload
            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($hasil->file_path) {
                    Storage::disk('public')->delete($hasil->file_path);
                }

                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('pengumuman', $fileName, 'public');

                $data['platform'] = 'upload';
                $data['embed_url'] = null;
                $data['is_uploaded'] = true;
                $data['file_path'] = $filePath;
            }
        }

        $hasil->update($data);

        return redirect()->route('admin.pengumuman.hasil.index')
            ->with('success', 'Hasil pengumuman berhasil diupdate');
    }

    /**
     * Remove hasil pengumuman
     */
    public function destroy(HasilPengumuman $hasil)
    {
        // File akan otomatis dihapus oleh model boot method
        $hasil->delete();

        return redirect()->route('admin.pengumuman.hasil.index')
            ->with('success', 'Hasil pengumuman berhasil dihapus');
    }
}
