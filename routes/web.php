<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{pregMatch}', function () {
    return view('welcome');
})->where('pregMatch', '.*');
