<?php

use Illuminate\Support\Facades\Route;

//Controllers
use App\Http\Controllers\Admin\DashboardController;


Route::get('/', function () {
    return view('welcome');
});


// Admin Routes
Route::prefix('admin')->name('admin.')->group(function(){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
