<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    use HasFactory;

    protected $fillable = [
        'race_id',
        'bet_car_id',
        'user_id',
        'bet_amount'
    ];

    public function races() {
        return $this->belongsTo(Race::class);
    }

}
