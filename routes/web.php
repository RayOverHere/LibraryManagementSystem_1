<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\User\CatalogController;
use App\Http\Controllers\User\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// User Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/catalog', [CatalogController::class, 'index'])->name('user.catalog');
    Route::post('/catalog/borrow/{book}', [CatalogController::class, 'borrow'])->name('user.borrow');
    Route::get('/devices', [\App\Http\Controllers\User\DeviceController::class, 'index'])->name('user.devices.index');
    Route::delete('/devices/{device}', [\App\Http\Controllers\User\DeviceController::class, 'destroy'])->name('user.devices.destroy');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('books', AdminBookController::class);
    Route::get('members', [AdminMemberController::class, 'index'])->name('members.index');
    Route::get('members/{member}/edit', [AdminMemberController::class, 'edit'])->name('members.edit');
    Route::put('members/{member}', [AdminMemberController::class, 'update'])->name('members.update');
    Route::delete('members/{member}', [AdminMemberController::class, 'destroy'])->name('members.destroy');
    Route::get('transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::put('transactions/{transaction}', [AdminTransactionController::class, 'update'])->name('transactions.update');
    Route::get('books/lookup/{isbn}', [AdminBookController::class, 'lookup'])->name('books.lookup');
});

require __DIR__.'/auth.php';
