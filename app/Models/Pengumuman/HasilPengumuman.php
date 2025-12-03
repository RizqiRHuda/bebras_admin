<?php
namespace App\Models\Pengumuman;

use App\Models\Pengumuman\KategoriPengumuman;
use App\Models\Pengumuman\TahunPengumuman;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HasilPengumuman extends Model
{
    use HasFactory;

    protected $table = 'hasil_pengumuman';

    protected $fillable = [
        'id_tahun',
        'id_kategori',
        'embed_url',
        'platform',
        'description',
        'file_path',
        'is_uploaded'
    ];

    protected $casts = [
        'is_uploaded' => 'boolean'
    ];

    public function tahun()
    {
        return $this->belongsTo(TahunPengumuman::class, 'id_tahun');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriPengumuman::class, 'id_kategori');
    }

    public function scopeFilter($q, $idTahun = null, $idKategori = null)
    {
        if ($idTahun) {
            $q->where('id_tahun', $idTahun);
        }

        if ($idKategori) {
            $q->where('id_kategori', $idKategori);
        }

        return $q;
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
              $q->where('description', 'LIKE', "%{$keyword}%")
              ->orWhere('platform', 'LIKE', "%{$keyword}%");
        });
    }

    /**
     * Deteksi platform berdasarkan URL
     */
    public static function detectPlatform($url)
    {
        if (str_contains($url, 'docs.google.com/spreadsheets')) {
            return 'google_sheets';
        } elseif (str_contains($url, 'excel.office.com') || str_contains($url, 'office.live.com') || str_contains($url, 'officeapps.live.com')) {
            return 'excel_online';
        } elseif (str_contains($url, 'onedrive.live.com') || str_contains($url, '1drv.ms') || str_contains($url, 'sharepoint.com')) {
            return 'onedrive';
        }
        return 'other';
    }

    /**
     * Konversi URL ke format embed yang benar
     * Google Sheets: bisa di-embed
     * OneDrive/Excel Online: redirect langsung (tidak bisa di-embed karena CORS)
     */
    public static function convertToEmbedUrl($url, $platform)
    {
        switch ($platform) {
            case 'google_sheets':
                // Google Sheets bisa di-embed langsung
                if (preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
                    // Gunakan format preview/embed
                    return "https://docs.google.com/spreadsheets/d/{$matches[1]}/edit?usp=sharing&rm=minimal";
                }
                return $url;
            
            case 'excel_online':
            case 'onedrive':
                // OneDrive/Excel Online tidak support iframe embed dari external domain
                // Return original URL untuk direct link
                return $url;
            
            default:
                return $url;
        }
    }

    /**
     * Get file URL untuk download
     */
    public function getFileUrlAttribute()
    {
        if ($this->is_uploaded && $this->file_path) {
            return Storage::disk('public')->url($this->file_path);
        }
        return null;
    }

    /**
     * Delete file when model deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if ($model->is_uploaded && $model->file_path) {
                Storage::disk('public')->delete($model->file_path);
            }
        });
    }
}
