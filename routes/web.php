<?php

use App\Http\Controllers\MetricController;
use Illuminate\Support\Facades\Route;

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
    return redirect('/metrics');
});
Route::get('/metrics', [MetricController::class, 'index'])->name('metrics.index');
Route::any('/get-metrics', [MetricController::class, 'getMetrics'])->name('metrics.get');
Route::post('/save-metrics', [MetricController::class, 'saveMetrics'])->name('metrics.save');
Route::get('/metrics/history', [MetricController::class, 'history'])->name('metrics.history');
