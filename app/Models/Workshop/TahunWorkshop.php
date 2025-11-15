<?php

namespace App\Models\Workshop;

use App\Models\Workshop\Workshop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TahunWorkshop extends Model
{
    use HasFactory;

    protected $table = 'tahun_workshop';

    protected $guarded = ['id'];

    public function workshop ()
    {
        return $this->hasMany(Workshop::class, 'id_tahun');
    }
}
