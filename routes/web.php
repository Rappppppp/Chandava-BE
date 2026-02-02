<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


// SPA catch-all — exclude API and dist folder
Route::get('/{any}', function () {
    return response()->file(public_path('dist/index.html'));
})->where('any', '^(?!api|dist).*');
