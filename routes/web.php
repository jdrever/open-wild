<?php

use App\Http\Controllers\RecordsController;
use App\Http\Controllers\SitesController;
use App\Http\Controllers\SpeciesController;
use App\Http\Controllers\SquaresController;
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
Route::get('/species/{speciesName}', [RecordsController::class, 'singleSpeciesForDataset']);
Route::get('/species-autocomplete/{speciesName}', [SpeciesController::class, 'getSpeciesNameAutoComplete']);
Route::get('/record/{occurrenceId}', [RecordsController::class, 'singleRecord']);

Route::get('/sites/', [SitesController::class, 'index']);
Route::post('/sites/', [SitesController::class, 'index']);
Route::get('/sites/{siteName}', [SitesController::class, 'listForDataset']);
Route::get('/sites-autocomplete/{siteName}', [SitesController::class, 'getSiteNameAutoComplete']);
Route::get('/site/{siteName}/type/{speciesNameType}/group/{speciesGroup}/axiophytes/{axiophyteFilter}/{refresh?}', [SpeciesController::class, 'listForSite']);
Route::get('/site/{siteName}/species/{speciesName}', [RecordsController::class, 'singleSpeciesForSite']);

Route::get('/squares/', [SquaresController::class, 'index']);
Route::get('/square/{gridSquare}/type/{speciesNameType}/group/{speciesGroup}/axiophytes/{axiophyteFilter}/{refresh?}', [SpeciesController::class, 'listForSquare']);
Route::get('/square/{gridSquare}/species/{speciesName}', [RecordsController::class, 'singleSpeciesForSquare']);

Route::get('/radius/', [RadiusController::class, 'index']);

Route::view('/about', 'about');
