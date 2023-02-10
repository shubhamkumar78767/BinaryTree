<?php

use App\Http\Controllers\BinaryController;
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

Route::get('/', [BinaryController::class, 'index']);
Route::get('/home', [BinaryController::class, 'index']);
Route::post('/create', [BinaryController::class, 'create']);
Route::get('/bnbSend', [BinaryController::class, 'bnbSend']);
Route::get('/TokenSend', [BinaryController::class, 'TokenSend']); 
Route::get('/fetchUserBalance', [BinaryController::class, 'fetchUserBalance']);
Route::get('/fetchContractAbi', [BinaryController::class, 'fetchContractAbi']);
