<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LibraryController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('library');
})->name('library.home');

Route::prefix('/library')->group(function () {
    Route::get('/index', [LibraryController::class, 'index'])->name('library.index');
    Route::post('/add', [LibraryController::class, 'store'])->name('library.store');
});
