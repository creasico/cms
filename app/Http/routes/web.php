<?php

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the 'public' routes.
|
*/

Route::get('/', 'HomeController@index');

Route::auth();
