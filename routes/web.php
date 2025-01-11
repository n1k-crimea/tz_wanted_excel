<?php

use App\Http\Controllers\RowController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth.basic')
    ->post('/upload', [FileUploadController::class, 'upload'])
    ->name('file.upload');

Route::middleware('auth.basic')
    ->get('/rows', [RowController::class, 'index'])
    ->name('rows.index');
