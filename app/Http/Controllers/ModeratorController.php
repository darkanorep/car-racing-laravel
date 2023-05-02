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

        $race = Race::create([
            'car1' => $car1['id'],
            'car2' => $car2['id']
        ]);

        PrizePool::create([
            'race_id' => $race->id
        ]);

        return $race;
    }

    public function start($id) {
        $cars = json_decode(Controller::getCars(), true);
        $race = Race::find($id);

        $car1 = $race->car1;
        $car2 = $race->car2;

        $carModel1 = null;
        $carModel2 = null;

        foreach ($cars as $car) {
            if ($car1 == $car['id']) {
                $carModel1 = $car;
            } elseif ($car2 == $car['id']) {
                $carModel2 = $car;
            }
        }

        if ($race->is_finish == false) {
            if ($carModel1['top_speed'] > $carModel2['top_speed']) {
                $race->update([
                    'remarks' => $carModel1['car_model'] . ' wins!',
                    'is_finish' => true,
                ]);
                return response()->json([
                    'message' => $carModel1['car_model'] . ' wins!',
                ]);
            } elseif ($carModel2['top_speed'] > $carModel1['top_speed']) {
                $race->update([
                    'remarks' => $carModel2['car_model'] . ' wins!',
                    'is_finish' => true,
                ]);
                return response()->json([
                    'message' => $carModel2['car_model'] . ' wins!'
                ]);
            } else {
                $race->update([
                    'remarks' => 'Draw',
                    'is_finish' => true,
                ]);
                return response()->json([
                    'message' => 'Draw'
                ]);
            }
        } else {
            return response()->json([
                'message' => 'Race already finished'
            ]);
        }

        $race->save();

        
        
    }
    
}
