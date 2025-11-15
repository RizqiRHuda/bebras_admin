<?php
namespace App\Models\Pengumuman;

use App\Models\Pengumuman\HasilPengumuman;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPengumuman extends Model
{
    use HasFactory;

    protected $table = 'kategori_pengumuman';

    protected $fillable = ['nama_kategori', 'deskripsi'];

    public function hasilPengumuman()
    {
        return $this->hasMany(HasilPengumuman::class);
    }

    public function filterNama($q, $nama)
    {
        return $q->where('nama_kategori', $nama);
    }
}
