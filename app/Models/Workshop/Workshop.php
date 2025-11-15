<?php
namespace App\Models\Workshop;

use App\Models\Workshop\TahunWorkshop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;

    protected $table = 'workshop';

    protected $guarded = ['id'];

    protected $casts = [
        'konten'  => 'array',
        'tanggal' => 'date',
    ];

    public function tahun()
    {
        return $this->belongsTo(TahunWorkshop::class, 'id_tahun');
    }
}
