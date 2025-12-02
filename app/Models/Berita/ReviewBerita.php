<?php
namespace App\Models\Berita;

use App\Models\User;
use App\Models\BeritaForm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewBerita extends Model
{
    use HasFactory;

    protected $table = 'review_berita';

    protected $fillable = [
        'id_berita',
        'user_id',
        'status',
        'note',
    ];

    public function berita()
    {
        return $this->belongsTo(BeritaForm::class, 'id_berita', 'id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
