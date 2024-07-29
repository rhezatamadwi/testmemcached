<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestMemcachedController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/loop-test', [TestMemcachedController::class, 'loopTest'])->name('testMemcached.loopTest');
Route::get('/load-test/{method}', [TestMemcachedController::class, 'loadTest'])->name('testMemcached.loadTest');
