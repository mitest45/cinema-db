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
        Route::get('/add', [MovieController::class, 'movie_form'])->name('add');
        Route::get('/edit/{id}', [MovieController::class, 'movie_form'])->name('edit');
        Route::post('/search-movie', [MovieController::class, 'search_movies'])->name('search-movie');
        Route::post('/fetch-movie-details', [MovieController::class, 'fetch_movie_details'])->name('fetch_movie_details');
        Route::post('/save/{id?}', [MovieController::class, 'save'])->name('save');// Id is optional
        Route::delete('/delete/{id}', [MovieController::class, 'delete'])->name('delete');
    });


});
