<?php
use Illuminate\Support\Facades\Auth;

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

    Route::get('/my/inbox', function () {
        abort(501);
    })->name('my-inbox');
    Route::get('/my/calendar', function () {
        abort(501);
    })->name('my-calendar');
    Route::get('/my/preferences', function () {
        abort(501);
    })->name('my-preferences');

    Route::post('/my/preferences/google2fa', '\App\Http\Controllers\CurrentUserController@saveGoogle2FA')->name('save-google2fa');
    Route::post('/google/authenticator', '\App\Http\Controllers\CurrentUserController@validateGoogle2FA')->name('validate-google2fa');

    Route::get('/settings', '\App\Http\Controllers\SettingsController@lobby')->name('settings');
    Route::post('/settings', '\App\Http\Controllers\SettingsController@saveSettings')->name('save-settings');
    Route::get('/settings/users', function () {
        abort(501);
    })->name('settings-users');
    Route::get('/settings/groups', function () {
        abort(501);
    })->name('settings-groups');
    Route::get('/settings/roles', function () {
        abort(501);
    })->name('settings-roles');
    Route::get('/settings/email', '\App\Http\Controllers\SettingsController@email')->name('settings-email');
    Route::get('/settings/google', '\App\Http\Controllers\SettingsController@google')->name('settings-google');
    Route::get('/settings/minfraud', '\App\Http\Controllers\SettingsController@minfraud')->name('settings-minfraud');
    Route::get('/settings/weather', '\App\Http\Controllers\SettingsController@weather')->name('settings-weather');
    Route::get('/settings/bin-check', '\App\Http\Controllers\SettingsController@bincheck')->name('settings-bin-check');

    if ('polling' == config('app.rtu.method')) {
        Route::get('/rtu', '\App\Http\Controllers\AuthenticatedSessionController@onPolling')->name('rtu');
    }
});

Route::get('/login', '\App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login')->middleware('guest');
Route::post('/login', '\App\Http\Controllers\Auth\LoginController@login')->name('submit-login')->middleware('guest');

//Route::fallback(function () {
//    return redirect('/');
//});
