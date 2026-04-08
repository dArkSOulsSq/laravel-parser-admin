<?php use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParserController;
use App\Http\Controllers\AdminController;

Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
Route::post('/parser/run', [ParserController::class, 'run'])->name('parser.run');
Route::get('/', fn() => redirect('/admin'));