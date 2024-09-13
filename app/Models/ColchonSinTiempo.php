<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColchonSinTiempo extends Model
{
    use HasFactory;
    protected $table = 'colchones_sin_tiempo';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'referencia',
    ];
}
