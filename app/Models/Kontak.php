<?php

namespace App\Models;

use App\Models\KontakDetail;
use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
      protected $table = 'kontak'; 
    protected $fillable = ['nama', 'institusi', 'alamat'];

    public function details()
    {
        return $this->hasMany(KontakDetail::class, 'kontak_id');
    }
}
