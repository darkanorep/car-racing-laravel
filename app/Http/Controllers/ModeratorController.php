<?php

namespace App\Http\Controllers;

use App\Exceptions\RaceException;
use App\Http\Resources\RaceResource;
use App\Models\Bet;
use App\Models\Race;
use App\Models\User;
use App\Models\Wallet;
use GuzzleHttp\Client;
use App\Models\PrizePool;
use Illuminate\Http\Request;
use App\Response\Status;
use App\Response\Response;

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
            return (new Response())->invalid('car', Status::BAD_REQUEST);
        }

        $race = new RaceResource(Race::create([
            'car1' => $car1['id'],
            'car2' => $car2['id']
        ]));

        PrizePool::create([
            'race_id' => $race->id
        ]);

        return $race;
    }

    public function start($id) {
        $cars = json_decode(Controller::getCars(), true);
        
        $race = Race::where('id', $id)->first();
        $response = new Response();
        !$race ? $response->not_found('race', Status::NOT_FOUND) : '';
        
        if ($race->is_finish == false) {
            $car1 = null;
            $car2 = null;
            
            foreach ($cars as $car) {
                if ($race->car1 == $car['id']) {
                    $car1 = $car;
                } elseif ($race->car2 == $car['id']) {
                    $car2 = $car;
                }
            }
            
            if ($car1 && $car2) {
                if ($car1['top_speed'] > $car2['top_speed']) {
                    $race->update([
                        'remarks' => $car1['car_model'] . ' wins!',
                        'car_id_winner' => $car1['id'],
                        'is_finish' => true,
                    ]);
                } elseif ($car2['top_speed'] > $car1['top_speed']) {
                    $race->update([
                        'remarks' => $car2['car_model'] . ' wins!',
                        'car_id_winner' => $car2['id'],
                        'is_finish' => true,
                    ]);
                } else {
                    $race->update([
                        'remarks' => 'Draw',
                        'is_finish' => true,
                    ]);
                }
                
                $winners = $race->winners->where('bet_car_id', $race->car_id_winner);

                $this->distributePrize($winners, PrizePool::where('race_id', $race->id)->first()->prize_pool);
                
                return response()->json([
                    'message' => $race->remarks,
                    'winners' => $winners,
                ]);
            } else {
                return $response->invalid('race', Status::BAD_REQUEST);
            }
        } else {
            return $response->success('Already finished', Status::OK);
        }
    }

    public function distributePrize($winners, $totalPrizeMoney) {
        $numWinners = count($winners);
        $totalBets = 0;
        foreach ($winners as $winner) {
            $totalBets += $winner->bet_amount;
        }
        $moderatorCommission = $totalPrizeMoney * 0.1;
        $prizePool = $totalPrizeMoney - $moderatorCommission;
        $percentageShare = $prizePool / $totalBets;
        foreach ($winners as $winner) {
            $prize = $winner->bet_amount * $percentageShare;
            $user = Wallet::where('user_id', $winner->user_id)->first();
            $user->balance += $prize;
            $user->save();
        }
 
        $moderator = User::where('role', 'moderator')->first();
        $moderatorWallet = Wallet::where('user_id', $moderator->id)->first();
        $moderatorWallet->balance += $moderatorCommission;
        $moderatorWallet->save();
    }
    
}
