<?php

use Illuminate\Support\Facades\Route;
use HoneybadgerIo\NovaHoneybadger\Http\Controllers\ToolController;


Route::get('/{resource}/{resourceId}', ToolController::class . '@fetch');
Route::get('/url', ToolController::class . '@url');