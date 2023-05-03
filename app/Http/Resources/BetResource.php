<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'raca_id' => $this->race_id,
            'bet_car_id' => $this->bet_car_id,
            'user_id' => $this->user_id,
            'bet_amount' => $this->bet_amount,
        ];
    }
}
