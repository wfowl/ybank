<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    $account = DB::table('accounts')
             ->where('id',$id)
             ->get();

    return $account;
});

Route::get('accounts/{id}/transactions', function ($id) {
    $account = DB::table('transactions')
             ->where('from', $id)
             ->orWhere('to', $id)
             ->get();

    return $account;
});

Route::post('accounts/{id}/transactions', function (Request $request, $id) {
    $to = $request->input('to');
    $amount = $request->input('amount');
    $details = $request->input('details');

    $account = DB::table('accounts')
             ->where('id',$id)
             ->decrement('balance', $amount);
    
    $account = DB::table('accounts')
             ->where('id',$to)
             ->increment('balance',$amount);

    DB::table('transactions')->insert(
        [
            'from' => $id,
            'to' => $to,
            'amount' => $amount,
            'details' => $details
        ]
    );
});

Route::get('currencies', function () {
    $account = DB::table('currencies')
              ->get();

    return $account;
});
