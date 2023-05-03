<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Race extends Model
{
    use HasFactory;

    protected $fillable = [
        'car1',
        'car2',
        'is_finish',
        'remarks',
        'car_id_winner'
    ];

    public function winners() {
        return $this->hasMany(Bet::class);
    }

}
