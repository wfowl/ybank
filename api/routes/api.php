<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
*/

Route::get('accounts/{id}', function ($id) {
    return App\Account::where('id', $id)
        ->with('country')->get();
});

Route::get('accounts/{id}/transactions', function ($id) {
    return App\Transaction::where('from', $id)->orWhere('to', $id)->get();
});

Route::post('accounts/{id}/transactions', function (Request $request, $id) {
    $validator = Validator::make(array_merge($request->all(), ['id' => $id]), [
        'amount' => ['required', 'min:1', 'numeric'],
        'id' => ['required', 'exists:accounts,id'],
        'to' => ['required', 'exists:accounts,id'],
        'details' => ['required']
    ]);

    if($validator->fails()) {
        return response()->json($validator->messages(), 422);
    }

    $to = $request->input('to');
    $amount = $request->input('amount');
    $details = $request->input('details');
    
    $to_account = App\Account::find($to);
    $from_account = App\Account::find($id);

    //Check if the user has enough money to transfer
    if($from_account->balance < $amount) {
        return response()->json(['amount' => ['You do not have enough money to transfer this amount.']], 422);
    }

    //Check if the recipient is different than sender
    if($to_account->id == $from_account->id) {
        return response()->json(['to' => ['You cannot send money to yourself.']], 422);
    }

    //calculate conversion rate
    $guzzle = new Client();
    $conversion_request = $guzzle->request('GET', 'https://api.exchangeratesapi.io/latest', [
        'query' => [
            'base' => $from_account->country->currency->name
        ]
    ]);

    if($to_account->country->currency->id != $from_account->country->currency->id) {
        //Pulling the conversion rate
        $conversion_rate = json_decode($conversion_request->getBody())->rates->{$to_account->country->currency->name};
    } else {
        $conversion_rate = 1;
    }
    

    //Determinining the amount to be transferred and rounding to the nearest hundredth
    $amount_to_deposit = round($amount * $conversion_rate, 2);

    $to_account->deposit($amount_to_deposit);
    $from_account->withdraw($amount);

    $transaction = new App\Transaction();
    $transaction->from = $id;
    $transaction->to = $to;
    $transaction->money_to = $amount_to_deposit;
    $transaction->money_from = $amount;
    $transaction->to_currency_id = $to_account->country->currency->id;
    $transaction->from_currency_id = $from_account->country->currency->id;
    $transaction->details = $details;
    $transaction->save();
});

Route::get('currencies', function () {
    return App\Currency::all();
});
