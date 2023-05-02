<?php

namespace App\Http\Controllers;

use App\Models\PrizePool;
use App\Models\Race;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ModeratorController extends Controller
{
    
    public function race(Request $request) {

        $cars = json_decode(Controller::getCars(), true);
    
        $car1Model = $request->car1;
        $car2Model = $request->car2;
    
        $car1 = null;
        $car2 = null;   
    
        foreach ($cars as $car) {
            if ($car['id'] == $car1Model) {
                $car1 = $car;
            } elseif ($car['id'] == $car2Model) {
                $car2 = $car;
            }
        
            if ($car1 !== null && $car2 !== null) {
                break;
            }
        }

    
        if ($car1 === null || $car2 === null) {
            return response()->json([
                'message' => 'Invalid car models.'
            ]);
        }

    
        // if ($car1['top_speed'] > $car2['top_speed']) {
        //     return response()->json([
        //         'message' => $car1['car_model'] . ' wins! with id of ' . $car1['id']
        //     ]);
        // } elseif ($car2['top_speed'] > $car1['top_speed']) {
        //     return response()->json([
        //         'message' => $car2['car_model'] . ' wins! with id of ' . $car2['id']
        //     ]);
        // } else {
        //     return response()->json([
        //         'message' => 'Draw'
        //     ]);
        // }

        $race = Race::create([
            'car1' => $car1['id'],
            'car2' => $car2['id']
        ]);

        PrizePool::create([
            'race_id' => $race->id
        ]);

        return $race;
    }
    
}
