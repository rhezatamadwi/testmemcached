<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestMemcachedController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-memcached', [TestMemcachedController::class, 'index'])->name('testMemcached.index');
