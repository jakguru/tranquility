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

Route::group(['middleware' => ['guest']], function () {
    Route::get('/', function () {
        return redirect('login');
    });
    Route::get('/dashboard', function () {
        return redirect('login');
    });
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', function () {
        return redirect('dashboard');
    });
    Route::get('/login', function () {
        return redirect('dashboard');
    });
    Route::get('/dashboard', '\App\Http\Controllers\DashboardController@index')->name('dashboard');
    Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout')->name('logout');
    Route::get('/search', '\App\Http\Controllers\DashboardController@search')->name('search');

    if ('polling' == config('app.rtu.method')) {
        Route::get('/rtu', '\App\Http\Controllers\AuthenticatedSessionController@onPolling')->name('rtu');
    }
});

Route::get('/login', '\App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login')->middleware('guest');
Route::post('/login', '\App\Http\Controllers\Auth\LoginController@login')->name('submit-login')->middleware('guest');

//Route::fallback(function () {
//    return redirect('/');
//});
