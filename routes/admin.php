<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

//
// config
//
Route::namespace('Config')
    ->prefix('config')
    ->as('config.')
    ->middleware('cache.headers:public;max_age=1800;etag')
    ->group(function () {
        Route::get('settings', 'Settings\IndexController@index');
    });

Route::namespace('Dashboard')->prefix('/')->as('dashboard.')->group(function () {
    Route::get('/', 'IndexController@index')->name('index');
});

//
// accounts
//
Route::namespace('Accounts')->prefix('accounts/')->as('accounts.')->group(function () {
    Route::get('logins', 'LoginsController@index')->name('logins');
    Route::get('create', 'UpdateController@update')->name('create');
    Route::post('create', 'UpdateController@store');
    Route::delete('{account}/two-factor', 'TwoFactorController@delete')->name('two-factor');
    Route::get('{account}/update', 'UpdateController@update')->name('update');
    Route::post('{account}/update', 'UpdateController@store');
    Route::post('{account}/invite', 'InviteController@invite')->name('invite');
    Route::get('{account}', 'ViewController@view')->name('view');
    Route::get('/', 'IndexController@index')->name('index');
    Route::delete('{account}', 'DeleteController@delete')->name('delete');
});

//
// tenants
//
Route::namespace('Tenants')->prefix('tenants')->as('tenants.')->group(function () {
    Route::get('/', 'IndexController@index')->name('index');
    Route::get('create', 'UpdateController@update')->name('create');
    Route::post('create', 'UpdateController@store');
    Route::get('{tenant}', 'ViewController@view')->name('view');
    Route::get('{tenant}/update', 'UpdateController@update')->name('update');
    Route::post('{tenant}/update', 'UpdateController@store');
    Route::get('{tenant}/switch', 'SwitchController@switch')->name('switch');
    Route::delete('{tenant}', 'DeleteController@delete')->name('delete');
});

//
// account/me
//
Route::namespace('Accounts')->prefix('account/')->as('account.')->group(function () {
    Route::post('me', 'MeController@store');
    Route::get('me', 'MeController@update')->name('me.update');
});

//
// system
//
Route::namespace('System')->prefix('system/')->as('system.')->group(function () {
    Route::get('changelog', 'ChangelogController@index')->name('changelog');
    Route::get('code', 'CodeController@index')->name('code');
    Route::get('server/load', 'ServerController@load')->name('server.load');
    Route::post('server', 'ServerController@store');
    Route::get('server', 'ServerController@index')->name('server');
    Route::get('database', 'DatabaseController@index')->name('database');
    Route::get('database/download/{name}', 'DatabaseController@download')->name('database.download');
    Route::get('open-source', 'OpenSourceController@index')->name('open-source');
    Route::get('styleguide', 'StyleguideController@index')->name('styleguide');

    Route::namespace('Logs')->prefix('logs/')->as('logs.')->group(function () {
        Route::get('/', 'IndexController@index')->name('index');
        Route::get('{log}', 'ViewController@view')->name('view');
        Route::get('{log}/download', 'ViewController@download')->name('download');
        Route::get('{log}/{entry}', 'ViewController@detail')->name('entry');
    });

    Route::namespace('Jobs')->prefix('jobs/')->as('jobs.')->group(function () {
        Route::get('/', 'IndexController@index')->name('index');
        Route::get('{job}', 'RescheduleController@reschedule')->name('reschedule');
    });

    Route::post('feedback', 'FeedbackController@store')->name('feedback');
});
