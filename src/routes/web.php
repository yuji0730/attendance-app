<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AttendanceController;
use App\Models\Attendance;
use App\Http\Controllers\ModificationRequestController;
use App\Models\ModificationRequest;
use App\Http\Controllers\AdminAttendanceController;

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

Route::get('/', function () {
    return view('welcome');
});


// Route::get('/admin/login', function () {
//     return view('admin.auth.login');})->name('admin.login');
Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])
    ->name('admin.login');

Route::post('/login', [LoginController::class, 'store'])->name('login');

Route::get('/attendance/list', [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('/attendance/{id}', [AttendanceController::class, 'detail'])->name('attendance.detail');

Route::get('/attendance', [AttendanceController::class, 'showClock'])->name('attendance.show');
Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock_in');
Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock_out');

Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])->name('attendance.break_start');
Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])->name('attendance.break_end');


Route::get('/stamp_correction_request/list', [ModificationRequestController::class, 'request'])->name('request.index')->middleware('auth');

Route::post('/attendance-request/date/{date}', [ModificationRequestController::class, 'store'])->name('attendance-request.store');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::get('/admin/staff/list', [AdminAttendanceController::class, 'staff'])->name('admin.staff.index');
});
