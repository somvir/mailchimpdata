<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploader;

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

Route::get('/upload', [FileUploader::class, 'showUploadForm']);
Route::post('/upload', [FileUploader::class, 'handleFileUpload']);
Route::get('/sync', [FileUploader::class, 'startSyncView']);
Route::post('/sync', [FileUploader::class, 'startSync']);