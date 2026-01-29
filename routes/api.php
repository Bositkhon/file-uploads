<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BatchUploadImageAction;
use App\Http\Controllers\DeleteImageAction;
use App\Http\Controllers\DownloadImageAction;
use App\Http\Controllers\ShowImageAction;
use App\Http\Controllers\ListImagesAction;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/images', ListImagesAction::class);
    Route::post('/images', BatchUploadImageAction::class);
    Route::get('/images/{id}', ShowImageAction::class);
    Route::get('/images/{id}/download', DownloadImageAction::class);
    Route::delete('/images/{id}', DeleteImageAction::class);
});
