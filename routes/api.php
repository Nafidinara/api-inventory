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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1')->group(function(){

        Route::prefix('admin')->group(function(){
            Route::post('register', 'UserController@register');
            Route::post('login', 'UserController@login');
            Route::group(['middleware' => ['jwt.verify']], function() {
            Route::get('getAdmin', 'UserController@getAuthenticatedUser');
            });
        });

            Route::resource('pegawai','PegawaiController',[
                'except' => ['create','edit']
            ]);

            Route::resource('laptop','LaptopController',[
                'except' => ['create','edit']
            ]);


            Route::resource('divisi','DivisiController',[
                'except' => ['create','edit']
            ]);

            Route::post('printer/{printer}/tambah','PrinterController@tambahFile');
            Route::resource('printer','PrinterController',[
                'except' => ['create','edit']
            ]);

});
