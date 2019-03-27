<?php

use Illuminate\Http\Request;

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

Route::post('/customer', function (Request $request) {
    $user = \App\Models\Customer::create($request->all());
    $user->dialog_config()->create(['type' => 'confirmation_of_attendance']);
    return response()->json($user, 201);
});

Route::get('/sendMessage/{id}', function($id) {
    $user = \App\Models\Customer::with('dialog_config')->find($id);
    $user->dialog_config()->update(['type' => 'confirmation_of_attendance']);
    $user->notify(new \App\Notifications\ConfirmationOfAttendance());
    return response()->json([
        'message' => 'Notificação enviada!'
    ]);
});

Route::post('webhook', 'ChatWebhookController@main');

Route::group([
        'prefix' => 'auth', 
        'namespace' => 'Auth', 
        'as' => 'auth.'
    ], function () {
        Route::post('login', 'AuthController@login')->name('login');
        Route::post('logout', 'AuthController@logout')->name('logout');
        Route::post('refresh', 'AuthController@refresh')->name('refresh');
        Route::post('me', 'AuthController@me')->name('me');
});

Route::group([
        'middlaware' => 'auth:api',
        'prefix' => 'v1',
        'namespace' => 'v1',
    ],
    function () {
        Route::apiResource('cars', 'CarsController');
});
