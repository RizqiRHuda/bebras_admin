<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BebrasChallenge extends Model
{
    protected $table = 'bebras_challenge';

    protected $guarded = ['id'];

    protected $casts = [
        'table_json' => 'array',
        'year'       => 'integer',
    ];
}
