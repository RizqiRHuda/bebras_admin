<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman\TahunPengumuman;
use App\Models\Pengumuman\HasilPengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    /**
     * Mendapatkan daftar tahun pengumuman yang tersedia
     * Untuk ditampilkan di navbar
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTahunList()
    {
        try {
            $tahunList = TahunPengumuman::orderBy('tahun', 'desc')
                ->pluck('tahun')
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => $tahunList,
                'message' => 'Daftar tahun pengumuman berhasil diambil'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar tahun: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan hasil pengumuman berdasarkan tahun
     * Mengembalikan data dengan format embed yang sesuai per platform
     * 
     * @param string $tahun
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHasilByTahun($tahun)
    {
        try {
            // Cari tahun pengumuman
            $tahunPengumuman = TahunPengumuman::where('tahun', $tahun)->first();

            if (!$tahunPengumuman) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tahun pengumuman tidak ditemukan'
                ], 404);
            }

            // Ambil hasil pengumuman untuk tahun tersebut dengan relasi kategori
            $hasilPengumuman = HasilPengumuman::where('id_tahun', $tahunPengumuman->id)
                ->with('kategori')
                ->get()
                ->map(function ($hasil) {
                    return [
                        'id' => $hasil->id,
                        'description' => $hasil->description,
                        'kategori' => $hasil->kategori ? $hasil->kategori->nama_kategori : null,
                        'platform' => $hasil->platform,
                        'embed_url' => $hasil->embed_url,
                        'is_uploaded' => $hasil->is_uploaded,
                        
                        // Informasi tambahan untuk handling di frontend
                        'can_embed' => $this->canEmbed($hasil->platform, $hasil->is_uploaded),
                        'download_url' => $hasil->is_uploaded ? route('api.pengumuman.download', $hasil->id) : null,
                        'view_url' => $this->getViewUrl($hasil),
                        
                        // Info file untuk uploaded files
                        'file_name' => $hasil->is_uploaded && $hasil->file_path ? basename($hasil->file_path) : null,
                        'file_size' => $hasil->is_uploaded && $hasil->file_path && Storage::disk('public')->exists($hasil->file_path) 
                            ? Storage::disk('public')->size($hasil->file_path) 
                            : null,
                        
                        'created_at' => $hasil->created_at,
                        'updated_at' => $hasil->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'tahun' => $tahun,
                    'hasil_pengumuman' => $hasilPengumuman
                ],
                'message' => 'Data hasil pengumuman berhasil diambil'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data hasil pengumuman: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download file hasil pengumuman
     * Khusus untuk file yang di-upload
     * 
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function downloadFile($id)
    {
        try {
            $hasil = HasilPengumuman::findOrFail($id);

            // Pastikan ini adalah file yang di-upload
            if (!$hasil->is_uploaded || !$hasil->file_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak tersedia untuk download'
                ], 404);
            }

            // Cek apakah file ada di storage (gunakan disk 'public')
            if (!Storage::disk('public')->exists($hasil->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan di storage',
                    'debug' => [
                        'file_path' => $hasil->file_path,
                        'full_path' => Storage::disk('public')->path($hasil->file_path)
                    ]
                ], 404);
            }

            // Return file untuk download
            $fileName = basename($hasil->file_path);
            
            return Storage::disk('public')->download($hasil->file_path, $fileName);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Cek apakah platform bisa di-embed
     * 
     * @param string|null $platform
     * @param bool $isUploaded
     * @return bool
     */
    private function canEmbed($platform, $isUploaded)
    {
        // Google Sheets bisa di-embed dengan iframe
        if ($platform === 'google_sheets') {
            return true;
        }

        // File upload TIDAK bisa di-embed dengan iframe
        // Excel file tidak bisa di-render di browser secara langsung
        // Hanya bisa di-download
        if ($isUploaded) {
            return true;
        }

        // OneDrive dan Excel Online tidak bisa di-embed karena CORS
        // Akan menggunakan redirect link
        if (in_array($platform, ['onedrive', 'excel_online', 'uploaded'])) {
            return false;
        }

        return false;
    }

    /**
     * Helper: Dapatkan URL untuk view/redirect
     * 
     * @param HasilPengumuman $hasil
     * @return string|null
     */
    private function getViewUrl($hasil)
    {
        // Untuk OneDrive dan Excel Online, return URL langsung untuk redirect
        if (in_array($hasil->platform, ['onedrive', 'excel_online'])) {
            return $hasil->embed_url;
        }

        // Untuk Google Sheets, return embed URL (bisa di-embed dengan iframe)
        if ($hasil->platform === 'google_sheets') {
            return $hasil->embed_url;
        }

        // Untuk file upload, return NULL
        // File Excel tidak bisa di-preview di browser, hanya bisa di-download
        // Frontend harus menampilkan info file dan button download
        if ($hasil->is_uploaded && $hasil->file_path) {
            return Storage::disk('public')->url($hasil->file_path);;
        }

        return null;
    }
}
