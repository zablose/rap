<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));
Route::get('/user', fn() => view('user'))->middleware('role:user');
Route::post('/password/update', fn() => view('password-update'))->middleware('permission:password.update');
