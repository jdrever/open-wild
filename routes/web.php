<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpeciesController;
use App\Http\Controllers\RecordsController;

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
Route::get('/species/{speciesName}/type/{speciesNameType}/group/{speciesGroup}/axiophytes/{axiophyteFilter}', [SpeciesController::class, 'listForDataset']);
Route::get('/species/{speciesName}', [RecordsController::class, 'singleSpeciesForDataset']);
