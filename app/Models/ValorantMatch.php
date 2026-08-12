<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ValorantMatch extends Model
{
    use HasFactory;

    // Liberando as colunas para o banco de dados
    protected $fillable = [
        'match_id',
        'map',
        'agent',
        'kills',
        'deaths',
        'assists',
        'result'
    ];
}
