<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman\HasilPengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * API Controller untuk Akses File Pengumuman
 * 
 * API ini HANYA menyediakan akses ke file (download & view URL)
 * Data lainnya diambil langsung dari database yang sama oleh projek publik
 */
class PengumumanController extends Controller
{

    /**
     * Download file Excel hasil pengumuman
     * GET /api/files/download/{id}
     * 
     * @param int $id - ID hasil_pengumuman dari database
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
                    'message' => 'File tidak tersedia untuk download. File ini mungkin tersimpan di platform eksternal (Google Sheets/OneDrive).'
                ], 404);
            }

            // Cek apakah file ada di storage
            if (!Storage::disk('public')->exists($hasil->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan di storage'
                ], 404);
            }

            // Return file untuk download
            $fileName = basename($hasil->file_path);
            
            return Storage::disk('public')->download($hasil->file_path, $fileName);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data hasil pengumuman tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View/Stream file untuk embed
     * GET /api/files/view/{id}
     * 
     * Mengembalikan file binary atau redirect ke URL eksternal
     * 
     * @param int $id - ID hasil_pengumuman dari database
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function getFileUrl($id)
    {
        try {
            $hasil = HasilPengumuman::findOrFail($id);

            // Untuk Google Sheets - redirect ke URL
            if ($hasil->platform === 'google_sheets') {
                return redirect($hasil->embed_url);
            } 
            // Untuk Excel Online - redirect ke URL
            elseif ($hasil->platform === 'excel_online') {
                return redirect($hasil->embed_url);
            }
            // Untuk OneDrive - redirect ke URL
            elseif ($hasil->platform === 'onedrive') {
                return redirect($hasil->embed_url);
            }
            // Untuk file upload Excel - stream file langsung
            elseif ($hasil->is_uploaded && $hasil->file_path) {
                if (!Storage::disk('public')->exists($hasil->file_path)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File tidak ditemukan di storage'
                    ], 404);
                }

                // Stream file dengan headers yang benar untuk preview
                $filePath = Storage::disk('public')->path($hasil->file_path);
                $fileName = basename($hasil->file_path);
                
                return response()->file($filePath, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'GET',
                ]);
                
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'URL tidak tersedia untuk hasil pengumuman ini'
                ], 404);
            }

            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data hasil pengumuman tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil URL file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get file info dalam format JSON (untuk metadata)
     * GET /api/files/info/{id}
     * 
     * @param int $id - ID hasil_pengumuman dari database
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFileInfo($id)
    {
        try {
            $hasil = HasilPengumuman::findOrFail($id);

            $data = [
                'id' => $hasil->id,
                'platform' => $hasil->platform,
                'is_uploaded' => $hasil->is_uploaded,
                'can_embed' => false,
                'view_url' => route('api.files.view', $hasil->id),
                'download_url' => null,
            ];

            // Untuk Google Sheets
            if ($hasil->platform === 'google_sheets') {
                $data['can_embed'] = true;
                $data['external_url'] = $hasil->embed_url;
            } 
            // Untuk Excel Online
            elseif ($hasil->platform === 'excel_online') {
                $data['can_embed'] = true;
                $data['external_url'] = $hasil->embed_url;
            }
            // Untuk OneDrive
            elseif ($hasil->platform === 'onedrive') {
                $data['can_embed'] = false;
                $data['external_url'] = $hasil->embed_url;
            }
            // Untuk file upload
            elseif ($hasil->is_uploaded && $hasil->file_path) {
                if (Storage::disk('public')->exists($hasil->file_path)) {
                    $data['can_embed'] = true;
                    $data['file_name'] = basename($hasil->file_path);
                    $data['file_size'] = Storage::disk('public')->size($hasil->file_path);
                    $data['download_url'] = route('api.files.download', $hasil->id);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'File tidak ditemukan di storage'
                    ], 404);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data hasil pengumuman tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil info file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if file exists dan info file
     * GET /api/files/check/{id}
     * 
     * @param int $id - ID hasil_pengumuman dari database
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkFile($id)
    {
        try {
            $hasil = HasilPengumuman::findOrFail($id);

            $exists = false;
            $fileInfo = null;

            // Check file upload
            if ($hasil->is_uploaded && $hasil->file_path) {
                $exists = Storage::disk('public')->exists($hasil->file_path);
                if ($exists) {
                    $fileInfo = [
                        'name' => basename($hasil->file_path),
                        'size' => Storage::disk('public')->size($hasil->file_path),
                        'path' => $hasil->file_path,
                        'url' => Storage::disk('public')->url($hasil->file_path),
                    ];
                }
            }
            // Check external URL
            elseif (!empty($hasil->embed_url)) {
                $exists = true;
                $fileInfo = [
                    'url' => $hasil->embed_url,
                    'platform' => $hasil->platform,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'exists' => $exists,
                    'platform' => $hasil->platform,
                    'is_uploaded' => $hasil->is_uploaded,
                    'file_info' => $fileInfo,
                ]
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data hasil pengumuman tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa file: ' . $e->getMessage()
            ], 500);
        }
    }
}
