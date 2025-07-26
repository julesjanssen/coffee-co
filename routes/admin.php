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
        Route::get('settings', 'Settings\\IndexController@index');
    });

Route::namespace('Dashboard')->prefix('/')->as('dashboard.')->group(function () {
    Route::get('/', 'IndexController@index')->name('index');
});

//
// game-sessions
//
Route::namespace('GameSessions')->prefix('game-sessions/')->as('game-sessions.')->group(function () {
    Route::get('create', 'UpdateController@update')->name('create');
    Route::post('create', 'UpdateController@store');
    Route::get('{session}/update', 'UpdateController@update')->name('update');
    Route::post('{session}/update', 'UpdateController@store');
    Route::get('{session}', 'ViewController@view')->name('view');
    Route::get('/', 'IndexController@index')->name('index');
    Route::delete('{session}', 'DeleteController@delete');
});

//
// accounts
//
Route::namespace('Accounts')->prefix('accounts/')->as('accounts.')->group(function () {
    Route::get('create', 'UpdateController@update')->name('create');
    Route::post('create', 'UpdateController@store');
    Route::get('{account}/update', 'UpdateController@update')->name('update');
    Route::post('{account}/invite', 'InviteController@store')->name('invite');
    Route::post('{account}/update', 'UpdateController@store');
    Route::get('{account}', 'ViewController@view')->name('view');
    Route::get('/', 'IndexController@index')->name('index');
    Route::delete('{account}', 'DeleteController@delete');
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
});

//
// account/me
//
Route::namespace('Accounts')->prefix('account/')->as('account.')->group(function () {
    Route::post('me', 'MeController@store');
    Route::get('me', 'MeController@update')->name('me.update');
    Route::get('security', 'SecurityController@view')->name('security');
    Route::delete('security/sessions/{session}', 'SecurityController@sessionRevoke')->name('security.sessions.delete');

    Route::namespace('Passkeys')->prefix('passkeys/')->as('passkeys.')->group(function () {
        Route::get('options/create', 'OptionsController@create');
        Route::post('create', 'CreateController@store')->name('create');
        Route::delete('{passkey}', 'DeleteController@delete')->name('delete');
    });
});

//
// system
//
Route::namespace('System')->prefix('system/')->as('system.')->group(function () {
    Route::post('server', 'ServerController@store');
    Route::get('changelog', 'ChangelogController@index')->name('changelog.index');
    Route::get('changelog/latest', 'ChangelogController@latest')->name('changelog.latest');
    Route::get('server', 'ServerController@index')->name('server');
    Route::get('database', 'DatabaseController@index')->name('database');
    Route::get('database/download/{name}', 'DatabaseController@download')->name('database.download');
    Route::get('open-source', 'OpenSourceController@index')->name('open-source');
    Route::post('styleguide', 'StyleguideController@store');
    Route::get('styleguide', 'StyleguideController@index')->name('styleguide');

    Route::namespace('Logs')->prefix('logs/')->as('logs.')->group(function () {
        $filenameRegex = '[^/]+\\.log.*\\.gz|[^/]+\\.log';

        Route::get('/', 'IndexController@index')->name('index');
        Route::get('{filename}/download', 'ViewController@download')->name('download')->where('filename', $filenameRegex);
        Route::get('{filename}', 'ViewController@view')->name('view')->where('filename', $filenameRegex);
        Route::get('{filename}/entry/{id}', 'EntryController@view')->name('entry')
            ->where('filename', $filenameRegex)
            ->where('uniqueId', '[a-fA-F0-9\-]+');
    });

    Route::namespace('Tasks')->prefix('tasks/')->as('tasks.')->group(function () {
        Route::get('{task}', 'ViewController@view')->name('view');
        Route::get('{task}/download', 'DownloadController@download')->name('download');
    });
});
