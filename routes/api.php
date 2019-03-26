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
    $user->notify(new \App\Notifications\ConfirmationOfAttendance());
    return response()->json([
        'message' => 'Notificação enviada!'
    ]);
});

use App\Notifications\ConfirmationOfAttendanceResponse;
use App\Notifications\AttendanceErrorResponse;

Route::any('/webhook', function(Request $request) {
    // return;

    // \Log::info($request->all());
    $userPhone = explode('@', $request->input('messages.0.author'))[0];
    $message = $request->input('messages.0.body');
    $user = App\Models\Customer::where('phone', $userPhone)->first();
    if($user && $user->dialog_config) {
        switch($user->dialog_config->type) {
            case 'confirmation_of_attendance': 
                if($message == 'sim' || $message == 'Sim') {
                    $user->notify(new ConfirmationOfAttendanceResponse('Perfeito, nos vemos lá'));
                    $user->dialog_config()->update(['type' => 'commom_dialog']);
                } elseif ($message == 'não' || $message == 'Não') {
                    $user->notify(new ConfirmationOfAttendanceResponse('Ok, tudo bem!'));
                    $user->dialog_config()->update(['type' => 'commom_dialog']);
                } else {
                    $user->notify(new AttendanceErrorResponse());
                }
        }
        // \Log::info($user->dialog_config->type);
        // if()
    }
    return response()->json([
        'message' => 'Salvo'
    ]);
});

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
