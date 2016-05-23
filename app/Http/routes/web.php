<?php

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the 'public' routes.
|
*/

Route::get('/', function () {
    info('Accesing web url');

    return view('home');
});

Route::auth();
