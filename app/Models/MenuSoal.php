<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuSoal extends Model
{
    use HasFactory;

    protected $table = 'menu_soal';

    protected $fillable = [
        'parent_id',
        'nama_menu',
        'slug',
        'judul',
        'body',
        'gambar',
        'urutan',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuSoal::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuSoal::class, 'parent_id')->orderBy('urutan');
    }
}
