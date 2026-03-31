<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminAuthController;
use Illuminate\Support\Facades\Auth;

// ログイン後のトップページ振り分け
Route::get('/', function () {
    $user = Auth::user();
    if ($user->role === 1) {
        // 管理者
        return redirect('/admin/attendance/list');
    } 
    // スタッフ
    return redirect('/attendance');
})->middleware(['auth', 'verified']);

// ----------------------
// スタッフ用勤怠
// ----------------------
Route::get('/attendance', [AttendanceController::class, 'attendance'])->name('attendance')->middleware(['auth']);
Route::post('/attendance/clock_in', [AttendanceController::class, 'clock_in'])->name('clock.in')->middleware(['auth']);
Route::post('/attendance/clock_out', [AttendanceController::class, 'clock_out'])->name('clock.out')->middleware(['auth']);
Route::post('/attendance/break_start', [AttendanceController::class, 'break_start'])->name('break.start')->middleware(['auth']);
Route::post('/attendance/break_end', [AttendanceController::class, 'break_end'])->name('break.end')->middleware(['auth']);
Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list')->middleware(['auth']);
Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail'])->name('attendance_detail')->middleware(['auth']);
Route::post('/attendance/apply_correction/{id}', [AttendanceController::class, 'applyCorrection'])->name('attendance.apply_correction')->middleware(['auth']);
Route::get('/stamp_correction_request/list', [AttendanceController::class, 'stampRequests'])->name('stamp_requests_list')->middleware(['auth']);


Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// 管理者勤怠
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/attendance/list', [AdminAuthController::class, 'adminList'])->name('admin.attendance.list');
    Route::get('/admin/attendance/detail/{id}', [AdminAuthController::class, 'adminDetail'])->name('admin.attendance.detail');
    Route::post('/admin/attendance/apply_correction/{id}', [AdminAuthController::class, 'applyCorrection'])->name('admin.attendance.apply_correction');

    // スタッフ一覧・月次勤怠
    Route::get('/admin/staff/list', [AdminAuthController::class, 'staffList'])->name('admin.staff.list');
    Route::get('/admin/attendance/staff/{userId}', [AdminAuthController::class, 'staffAttendance'])
    ->name('admin.attendance.staff');

    Route::get('/admin/stamp_correction_request/list', [AdminAuthController::class, 'requestList'])
        ->name('admin.request.list');
    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminAuthController::class, 'requestDetail'])->name('admin.request.detail');
    Route::post('/admin/stamp_correction_request/approve/{id}', [AdminAuthController::class, 'approveRequest'])->name('admin.request.approve');
});