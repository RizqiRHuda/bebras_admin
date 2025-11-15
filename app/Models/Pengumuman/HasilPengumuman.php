<?php
namespace App\Models\Pengumuman;

use App\Models\Pengumuman\KategoriPengumuman;
use App\Models\Pengumuman\TahunPengumuman;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilPengumuman extends Model
{
    use HasFactory;

    protected $table = 'hasil_pengumuman';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $cast = [
        'skor'         => 'float',
        'rank_percent' => 'float',
    ];

    public function tahun()
    {
        return $this->belongsTo(TahunPengumuman::class);
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriPengumuman::class);
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
              $q->where('nama', 'LIKE', "%{$keyword}%")
              ->orWhere('kota', 'LIKE', "%{$keyword}%")
              ->orWhere('bebras_biro', 'LIKE', "%{$keyword}%")
              ->orWhere('username', 'LIKE', "%{$keyword}%");
        });
    }
}
