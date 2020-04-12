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
    return App\Account::where('id', $id)
        ->with('country')->get();
});

Route::get('accounts/{id}/transactions', function ($id) {
    return App\Transaction::where('from', $id)->orWhere('to', $id)->get();
});

Route::post('accounts/{id}/transactions', function (Request $request, $id) {
    $validator = Validator::make(array_merge($request->all(), ['id' => $id]), [
        'amount' => ['required', 'min:0', 'numeric'],
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

    $to_account->deposit($amount);
    $from_account->withdraw($amount);

    $transaction = new App\Transaction();
    $transaction->from = $id;
    $transaction->to = $to;
    $transaction->amount = $amount;
    $transaction->details = $details;
    $transaction->save();
});

Route::get('currencies', function () {
    return App\Currency::all();
});
