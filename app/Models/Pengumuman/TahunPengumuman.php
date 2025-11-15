<?php
namespace App\Models\Pengumuman;

use App\Models\Pengumuman\HasilPengumuman;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunPengumuman extends Model
{
    use HasFactory;

    protected $table = "tahun_pengumuman";

    protected $fillable = ['tahun'];

    public function hasilPengumuman()
    {
        return $this->hasMany(HasilPengumuman::class);
    }

    public function filterTahun($q, $tahun)
    {
        return $q->where('tahun', $tahun);
    }
}
