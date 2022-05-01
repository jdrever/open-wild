<?php

use App\Http\Controllers\RecordsController;
use App\Http\Controllers\SitesController;
use App\Http\Controllers\SpeciesController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [SpeciesController::class, 'index']);

Route::post('/', [SpeciesController::class, 'index']);
Route::get('/species/{speciesName}/type/{speciesNameType}/group/{speciesGroup}/axiophytes/{axiophyteFilter}/{refresh?}', [SpeciesController::class, 'listForDataset']);
Route::get('/species-update/{speciesName}/type/{speciesNameType}/group/{speciesGroup}/axiophytes/{axiophyteFilter}', [SpeciesController::class, 'updateDataset']);
Route::get('/species/{speciesName}', [RecordsController::class, 'singleSpeciesForDataset']);
Route::get('/record/{occurrenceId}', [RecordsController::class, 'singleRecord']);

Route::get('/sites/', [SitesController::class, 'index']);
Route::post('/sites/', [SitesController::class, 'index']);
Route::get('/sites/{siteName}/{refresh?}', [SitesController::class, 'listForDataset']);
