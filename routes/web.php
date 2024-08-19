<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehiclePDFController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/export', [ExportController::class, 'export'])->name('export');
Route::get('/system/requests/{id}/print', [PDFController::class, 'print'])->name('requests.print');
Route::get('vehicle/pdf/export/{id}', [VehiclePDFController::class, 'export'])->name('vehicle.pdf.export');
Route::get('/export/history-pdf', [HistoryController::class, 'exportPdf'])->name('history.export.pdf');

require __DIR__ . '/auth.php';
