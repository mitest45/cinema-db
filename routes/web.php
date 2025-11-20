<?php

use Illuminate\Support\Facades\Route;

//Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MovieController;


Route::get('/', function () {
    return view('welcome');
});


// Admin Routes
Route::prefix('admin')->name('admin.')->group(function(){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Movie
    Route::prefix('movie')->name('movie.')->group(function(){
        Route::get('/', [MovieController::class, 'index'])->name('index');
        Route::get('/add', [MovieController::class, 'add'])->name('add');
        Route::post('/search-movie', [MovieController::class, 'search_movies'])->name('search-movie');
    });
});
