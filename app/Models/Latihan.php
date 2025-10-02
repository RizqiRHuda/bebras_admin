<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Latihan extends Model
{
    protected $table    = 'latihan';
    protected $fillable = ['nama', 'deskripsi', 'link', 'gambar'];
}
