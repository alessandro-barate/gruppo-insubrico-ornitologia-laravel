<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageContentController;
use App\Http\Controllers\Admin\PageContentController as AdminPageContent;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Pannello admin: usa lo stesso middleware di autenticazione delle altre rotte admin
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('page-contents', [AdminPageContent::class, 'index']);
    Route::post('page-contents/{pageContent}', [AdminPageContent::class, 'update']);
    Route::delete('page-contents/{pageContent}/value', [AdminPageContent::class, 'reset']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Pubblica: letta dalle pagine del sito
Route::get('/pages/{page}', [PageContentController::class, 'show']);

// Rate limiting a massimo 5 richieste al minuto per ogni IP
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1');