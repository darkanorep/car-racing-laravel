<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use GuzzleHttp\Client;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function getCars() {
        $cars = new Client([
            'base_uri' => 'http://127.0.0.1:8000/api/models',
            'headers' => [
                'Authorization' => 'Bearer 1|86gklHMcxpAPRnVhec2cuodQ4c2j87s2gTgcz1KI'
            ]
        ]);

        $response = $cars->request('GET');

        return $response->getBody()->getContents();
    }
}
