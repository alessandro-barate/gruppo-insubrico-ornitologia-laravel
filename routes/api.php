<?php

use App\Http\Controllers\Api\LinkController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\OutreachController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageContentController;
use Illuminate\Support\Facades\Route;

/*
| API pubbliche lette dal sito Nuxt (prefisso /api).
| Tutte in sola lettura, tranne contatti e newsletter.
| La gestione dei contenuti è nel pannello Filament (/admin).
*/

// Testi e immagini modificabili delle pagine
Route::get('/pages/{page}', [PageContentController::class, 'show']);

// News
Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{slug}', [NewsController::class, 'show']);

// Pubblicazioni
Route::get('/pubblicazioni', [PublicationController::class, 'home']);
Route::get('/pubblicazioni/{path}', [PublicationController::class, 'show'])->where('path', '.*');

// Progetti
Route::get('/progetti', [ProjectController::class, 'page']);
Route::get('/progetti/sections', [ProjectController::class, 'sections']);
Route::get('/progetti/{section}/{project?}', [ProjectController::class, 'show']);

// Divulgazione
Route::get('/divulgazione', [OutreachController::class, 'page']);
Route::get('/divulgazione/subsections', [OutreachController::class, 'subsections']);
Route::get('/divulgazione/{slug}', [OutreachController::class, 'show']);

// Social e link utili
Route::get('/links', [LinkController::class, 'index']);

// Ricerca nel sito
Route::get('/search', SearchController::class)->middleware('throttle:60,1');

// Form: massimo 5 invii al minuto per IP
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->middleware('throttle:5,1');
