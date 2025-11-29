<?php

namespace App\Models\Berita;

use App\Models\User;

use App\Models\Berita\ReviewBerita;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BeritaForm extends Model
{
    use HasFactory;

    protected $table = 'berita';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'konten',
        'gambar',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function review()
    {
        return $this->hasMany(ReviewBerita::class, 'id_berita', 'id');
    }
}
