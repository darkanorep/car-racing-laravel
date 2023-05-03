<?php

namespace App\Http\Controllers;

use App\Models\Bet;
use App\Models\Race;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Requests\BetRequest;
use App\Http\Requests\DepositRequest;
use App\Http\Resources\WalletResource;
use App\Models\PrizePool;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        $cars = json_decode(Controller::getCars(), true);

        $car1 = null;
        $car2 = null;

        $currentRace = Race::where('id', $id)->first();

        if(!$currentRace) {
            return response()->json([
                'message' => 'Race not found'
            ], 404);
        }

        foreach ($cars as $car) {
            if ($car['id'] == $currentRace->car1) {
                $car1 = $car;
            } elseif ($car['id'] == $currentRace->car2) {
                $car2 = $car;
            }
        }

        $race = Race::where('id', $id)->first();
        $winners = $race->winners->where('bet_car_id', $race->car_id_winner);

        if($currentRace->is_finish == true) {
            $prize_pool = PrizePool::where('race_id', $id)->first();
            return response()->json([
                'message' => 'This race has already finished.',
                'attributes' => [
                    'message' => $car1['car_model'] . ' vs ' . $car2['car_model'],
                    'prize pool' => '$'.$prize_pool->prize_pool,
                    'winners' => $winners
                ]
            ]);
        }

        return response()->json([
            'message' => 'This race start soon.',
            'attributes' => [
                'message' => $car1['car_model'] . ' vs ' . $car2['car_model'],
                'prize pool' => '$0'
            ]
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function deposit(DepositRequest $request) {

        $wallet  = Wallet::where('user_id', auth()->user()->id)->first();
        $wallet->balance += $request->amount;
        $wallet->save();

        return $this->depositReciept(new WalletResource($wallet));
        
    }

    public function checkBalance() {

        return new WalletResource(Wallet::with('user')->where('user_id', auth()->user()->id)->first());
    }

    private function depositReciept($wallet) {
        return response()->json([
            'message' => 'Deposit Successful',
            'data' => new WalletResource($wallet)
        ]);
    }

    public function bet(BetRequest $request, $id)
    {
        $balance = Wallet::with('user')->where('user_id', auth()->user()->id)->first()->balance;

        $race = Race::where('id', $id)->first();
        
        $betCar = null;

        if ($request->car_1 == true) {
            $betCar = $race->car1 ?? null;
        } elseif ($request->car_2 == true) {
            $betCar = $race->car2  ?? null;
        }

        if (!$race) {
            return response()->json([
                'message' => 'Race not found'
            ], 404);
        }

        if ($race->is_finish == true) {
            return response([
                'messasge' => 'This race has already finished.'
            ]);
        }

        $existingBet = Bet::where('race_id', $race->id)
                        ->where('user_id', auth()->user()->id)
                        ->first();

        if ($existingBet) {
            return response()->json([
                'message' => 'You have already placed a bet on this race.'
            ], 422);
        }

        if ($request->car_1 && $request->car_2) {
            return response()->json([
                'message' => 'Something went wrong, please choose only one car.'
            ], 422);
        }

        $bet = $request->bet_amount;
        if ($bet > $balance) {
            return response()->json([
                'message' => 'Insufficient funds'
            ], 422);
        }
        $balance -= $bet;
        $wallet = Wallet::where('user_id', auth()->user()->id)->first();
        $wallet->balance = $balance;
        $wallet->save();

        $betInfo =  Bet::create([
            'race_id' => $race->id,
            'bet_car_id' => $betCar,
            'user_id' => auth()->user()->id,
            'bet_amount' => $bet
        ]);

        $prize_pool = PrizePool::where('race_id', $race->id)->first();
        $prize_pool->prize_pool += $bet;
        $prize_pool->save();

        return $betInfo;
    }

}
