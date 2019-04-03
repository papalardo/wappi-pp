<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Http\Request;

Route::post('/user', function (Request $request) {
    // return $user = \App\Models\Customer::create([
    //     'name' => 'Fábio',
    //     'last_name' =>  'Vaz',
    //     'email' => 'ediano.gama@wavez.com.br',
    //     'phone' => '556196561012'
    // ]);
    return \App\Models\Customer::find(1);

    $user = \App\Models\Customer::with('dialog_config')->find(2);
    // $user->dialog_config()->update(['type' => 'confirmation_of_attendance']);
    // $user->notify(new \App\Notifications\ConfirmationOfAttendance());
    return $user;
});

Route::get('/', function () {
    return (new App\MyClasses\Dialog('Olá'))->getIntent();

    // return \App\MyClasses\Dialog
    // return view('welcome');
});


