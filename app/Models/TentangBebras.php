<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TentangBebras extends Model
{
    use HasFactory;

    protected $table    = 'tentang_bebras';
    protected $fillable = [
        'slug',
        'judul',
        'konten',
        'gambar',
        'urutan',
    ];
}
