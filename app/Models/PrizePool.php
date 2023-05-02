<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrizePool extends Model
{
    use HasFactory;

    protected $fillable = [
        'race_id',
        'bet_id',
        'prize_pool'
    ];
}
