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

    Route::get('/backgrounds/{model}/{id}.png', '\App\Helpers\ModelImageHelper@getBackgroundImage')->name('get-model-background')->where('id', '[0-9]+');
    Route::get('/avatars/{model}/{id}.png', '\App\Helpers\ModelImageHelper@getAvatarImage')->name('get-model-avatar')->where('id', '[0-9]+');

    Route::post('/my/preferences/google2fa', '\App\Http\Controllers\CurrentUserController@saveGoogle2FA')->name('save-google2fa');
    Route::post('/google/authenticator', '\App\Http\Controllers\CurrentUserController@validateGoogle2FA')->name('validate-google2fa');

    Route::get('/settings', '\App\Http\Controllers\SettingsController@lobby')->name('settings');
    Route::post('/settings', '\App\Http\Controllers\SettingsController@saveSettings')->name('save-settings');
    Route::get('/settings/users', '\App\Http\Controllers\UserController@list')->name('settings-users');
    Route::get('/settings/users/new', '\App\Http\Controllers\UserController@add')->name('create-user');
    Route::post('/settings/users/new', '\App\Http\Controllers\UserController@create')->name('create-user');
    Route::get('/settings/users/{id}', '\App\Http\Controllers\UserController@view')->name('view-user')->where('id', '[0-9]+');
    Route::get('/settings/users/{id}/edit', '\App\Http\Controllers\UserController@edit')->name('edit-user')->where('id', '[0-9]+');
    Route::put('/settings/users/{id}/edit', '\App\Http\Controllers\UserController@update')->name('update-user')->where('id', '[0-9]+');
    Route::get('/settings/users/{id}/audit', '\App\Http\Controllers\UserController@audit')->name('audit-user')->where('id', '[0-9]+');
    
    Route::get('/settings/groups', '\App\Http\Controllers\GroupController@list')->name('settings-groups');
    Route::get('/settings/groups/new', '\App\Http\Controllers\GroupController@add')->name('create-group');
    Route::post('/settings/groups/new', '\App\Http\Controllers\GroupController@create')->name('create-group');
    Route::get('/settings/groups/{id}', '\App\Http\Controllers\GroupController@view')->name('view-group')->where('id', '[0-9]+');
    Route::put('/settings/groups/{id}', '\App\Http\Controllers\GroupController@edit')->name('edit-group')->where('id', '[0-9]+');
    Route::get('/settings/groups/{id}/audit', '\App\Http\Controllers\GroupController@audit')->name('audit-group')->where('id', '[0-9]+');


    Route::get('/settings/roles', '\App\Http\Controllers\RoleController@list')->name('settings-roles');
    Route::get('/settings/roles/new', '\App\Http\Controllers\RoleController@add')->name('create-role');
    Route::post('/settings/roles/new', '\App\Http\Controllers\RoleController@create')->name('create-role');
    Route::get('/settings/roles/{id}', '\App\Http\Controllers\RoleController@view')->name('view-role')->where('id', '[0-9]+');
    Route::put('/settings/roles/{id}', '\App\Http\Controllers\RoleController@edit')->name('edit-role')->where('id', '[0-9]+');
    Route::get('/settings/roles/{id}/audit', '\App\Http\Controllers\RoleController@audit')->name('audit-role')->where('id', '[0-9]+');

    Route::get('/settings/system', '\App\Http\Controllers\SettingsController@system')->name('settings-system');
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
