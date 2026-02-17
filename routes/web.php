<?php

use App\Http\Controllers\Admin\EmployeeSalaryController;
use App\Http\Controllers\Admin\EmployeeVacationController;
use App\Http\Controllers\Admin\EmployeeCalendarController;
use App\Http\Controllers\ProfileController;
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

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('employees', \App\Http\Controllers\Admin\EmployeeController::class);
    Route::resource('positions', \App\Http\Controllers\Admin\PositionController::class);
    Route::resource('vacations', \App\Http\Controllers\Admin\VacationController::class);
    Route::resource('salaries', \App\Http\Controllers\Admin\SalaryController::class);
    Route::resource('holidays', \App\Http\Controllers\Admin\HolidayController::class);

    Route::post('employees/{employee}/documents', [\App\Http\Controllers\Admin\EmployeeDocumentController::class, 'store'])
        ->name('employees.documents.store');
    Route::get('documents/{document}/download', [\App\Http\Controllers\Admin\EmployeeDocumentController::class, 'download'])
        ->name('documents.download');
    Route::delete('documents/{document}', [\App\Http\Controllers\Admin\EmployeeDocumentController::class, 'destroy'])
        ->name('documents.destroy');



    Route::post('employees/{employee}/vacations', [EmployeeVacationController::class, 'store'])
        ->name('employees.vacations.store');

    Route::post('employees/{employee}/salaries', [EmployeeSalaryController::class, 'store'])
        ->name('employees.salaries.store');

    Route::get('employees/{employee}/calendar', [EmployeeCalendarController::class, 'show'])
        ->name('employees.calendar.show');
});


require __DIR__ . '/auth.php';
