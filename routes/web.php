<?php

use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\CalendarExportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeCalendarController;
use App\Http\Controllers\Admin\EmployeeCalendarDayController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EmployeeDocumentController;
use App\Http\Controllers\Admin\EmployeePasswordController;
use App\Http\Controllers\Admin\EmployeeSalaryController;
use App\Http\Controllers\Admin\EmployeeVacationController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\VacationApprovalController;
use App\Http\Controllers\Admin\VacationController;
use App\Http\Controllers\Employee\CalendarEmployeeController;
use App\Http\Controllers\Employee\DocumentEmployeeController;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use App\Http\Controllers\Employee\EmployeeNotificationController;
use App\Http\Controllers\Employee\ProfileController;
use App\Http\Controllers\Employee\SalaryEmployeeController;
use App\Http\Controllers\Employee\VacationEmployeeController;
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

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin|hr'])->prefix('admin')->name('admin.')->group(function () {
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

    Route::get('employees/{employee}/calendar/day', [EmployeeCalendarDayController::class, 'show'])
        ->name('employees.calendar.day.show');

    Route::post('employees/{employee}/calendar/day', [EmployeeCalendarDayController::class, 'storeOrUpdate'])
        ->name('employees.calendar.day.store');

    Route::delete('employees/{employee}/calendar/day', [EmployeeCalendarDayController::class, 'destroy'])
        ->name('employees.calendar.day.destroy');

    Route::get('calendar/export-all', [CalendarExportController::class, 'exportAll'])
    ->name('calendar.export-all');


    Route::get('employees/{employee}/calendar/export', [EmployeeCalendarController::class, 'export'])
        ->name('employees.calendar.export');


    Route::get('vacations', [VacationController::class, 'index'])
        ->name('vacations.index');

    // Approve / Reject (protected)
    Route::patch('vacations/{vacation}/approve', [VacationApprovalController::class, 'approve'])
        ->name('vacations.approve');
    // ->middleware('role:admin|hr');

    Route::patch('vacations/{vacation}/reject', [VacationApprovalController::class, 'reject'])
        ->name('vacations.reject');
    // ->middleware('role:admin|hr');

    Route::get('salaries', [SalaryController::class, 'index'])->name('salaries.index');

    Route::post('employees/{employee}/reset-password', [EmployeePasswordController::class, 'reset'])
        ->name('employees.reset-password');


    Route::get('notifications', [AdminNotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('notifications/{id}/read', [AdminNotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::post('notifications/read-all', [AdminNotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');
});




Route::middleware(['auth', 'role:employee'])
    ->prefix('employee')
    ->name('employee.')
    ->group(function () {
        Route::get('dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');

        Route::get('profile', [ProfileController::class, 'show'])->name('profile');

        Route::get('vacations', [VacationEmployeeController::class, 'index'])->name('vacations.index');
        Route::post('vacations', [VacationEmployeeController::class, 'store'])->name('vacations.store');

        Route::get('salaries', [SalaryEmployeeController::class, 'index'])->name('salaries.index');

        Route::get('documents', [DocumentEmployeeController::class, 'index'])->name('documents.index');
        Route::get('documents/{document}/download', [DocumentEmployeeController::class, 'download'])->name('documents.download');

        Route::get('calendar', [CalendarEmployeeController::class, 'show'])->name('calendar.show');

        Route::get('/notifications', [EmployeeNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [EmployeeNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [EmployeeNotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    });


require __DIR__ . '/auth.php';
