<?php
namespace App\Models;

use App\Models\Kontak;
use Illuminate\Database\Eloquent\Model;

class KontakDetail extends Model
{
      protected $table = 'kontak_detail'; 
    protected $fillable = ['tipe', 'nilai'];

    public function kontak()
    {
        return $this->belongsTo(Kontak::class, 'kontak_id');
    }
}
