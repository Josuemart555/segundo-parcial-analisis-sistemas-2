<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/calendario');
});

Route::get('/calendario', function () {
    return view('calendario.index');
})->name('calendario');
