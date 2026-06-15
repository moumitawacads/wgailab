<?php

use App\Http\Controllers\Admin\ChecklistController as AdminChecklistController;
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

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DomeworkController;
use App\Http\Controllers\BusinessPlanController;
use App\Http\Controllers\ResourceLibraryController;
use App\Http\Controllers\ChecklistController;

Route::get('/', function () {
    return view('frontend.pages.home');
});

Route::middleware(['auth', 'role:admin,superadmin,workforce_development'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'loadadminDashboard'])->name('admin.dashboard');
    Route::get('/settings', [SettingsController::class, 'getsettings'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'savesettings'])->name('save_settings');
    Route::get('/notifications', [NotificationController::class, 'fetchAll'])->name('admin.notifications');

    // Users Route //
    Route::get('/users', [UserController::class, 'list'])->name('admin.users');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::post('/users/update/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::get('/users/add', [UserController::class, 'add'])->name('admin.users.add');
    Route::post('/users/create', [UserController::class, 'create'])->name('admin.users.create');

    /** Class Route */
    Route::get('/classes', [ClassController::class, 'list'])->name('admin.classes');
    Route::get('/classes/add', [ClassController::class, 'add'])->name('admin.classes.add');
    Route::post('/classes/create', [ClassController::class, 'create'])->name('admin.classes.create');
    Route::delete('/classes/{id}', [ClassController::class, 'destroy'])->name('admin.classes.destroy');
    Route::get('/classes/edit/{id}', [ClassController::class, 'edit'])->name('admin.classes.edit');
    Route::post('/classes/update/{id}', [ClassController::class, 'update'])->name('admin.classes.update');

    Route::get('/manage_schedule', [ClassController::class, 'manage_schedule'])->name('admin.manageschedule');
    //Route::post('/schedule', [ClassController::class, 'storeSchedule'])->name('admin.storeschedule');
    Route::get('/schedule_log', [ClassController::class, 'schedule_log'])->name('admin.schedule_log');

    /** SEssion Manage */
    Route::get('/session/view/{id}', [ClassController::class, 'view_session'])->name('admin.session.view');
    Route::post('/session/create', [ClassController::class, 'create_session'])->name('admin.session.create');
    Route::post('/session/update{id}', [ClassController::class, 'update_session'])->name('admin.session.update');
    Route::delete('/session/cancel/{id}', [ClassController::class, 'cancel_session'])
        ->name('admin.session.cancelled');

    Route::delete('/session/delete/{id}', [ClassController::class, 'delete_session'])
        ->name('admin.session.delete');
    Route::get('/session/managedomework/{id}', [ClassController::class, 'manage_domework'])->name('admin.session.managedomework');
    Route::post('/session/update_domework_assignment/{id}', [ClassController::class, 'update_domework_assignment'])->name('admin.session.update_domework_assignment');

    Route::get('/domeworks/{session_id}', [ClassController::class, 'viewDomework'])->name('admin.view.domework');

    Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {
        Route::get('/session/edit/{id}', [ClassController::class, 'edit_session'])->name('admin.session.edit');
    });

    /** Attendance Report */
    Route::get('/attendance', [ClassController::class, 'attendance_record'])->name('admin.attendance_record');
    Route::get('/schedule-details', [ClassController::class, 'getScheduleDetails'])->name('admin.schedule_details');
    Route::get('/export-weekly-report', [ClassController::class, 'exportWeeklyReport'])->name('admin.export_weekly');
    Route::post('/compensation/{week_id}/payment/update-status', [ClassController::class, 'updatePaymentStatus'])->name('admin.compensation.payment.status-update');

    /** COmpensation */
    Route::get('/compensation_report', [ClassController::class, 'compensation_report'])->name('admin.compensation_report');
    Route::post('/compensation/update-status', [ClassController::class, 'updateStatus']);

    /** Domework */
    Route::get('/domework', [DomeworkController::class, 'index'])->name('admin.domework');
    Route::get('/domework/create', [DomeworkController::class, 'create'])->name('admin.domework.create');
    Route::post('/domework/store', [DomeworkController::class, 'store'])->name('admin.domework.store');
    Route::delete('/domework/{domework}', [DomeworkController::class, 'destroy'])->name('admin.domework.destroy');
    Route::get('/domework/edit/{id}', [DomeworkController::class, 'edit'])->name('admin.domework.edit');
    Route::post('/domework/update/{domework}', [DomeworkController::class, 'update'])->name('admin.domework.update');
    Route::get('/domework-answer-sheet', [DomeworkController::class, 'domeWorkAnswerSheet'])
        ->name('admin.dome_answer_sheet');
    Route::get('/worksheet-pdf/{session_id}/{user_id}', [DomeworkController::class, 'downloadStudentWorksheetPdf'])->name('admin.worksheet.pdf');

    /** BusinessPlan */
    Route::get('/businessplan', [BusinessPlanController::class, 'index'])->name('admin.businessplan');
    Route::get('/businessplan/create', [BusinessPlanController::class, 'create'])->name('admin.businessplan.create');
    Route::post('/businessplan/store', [BusinessPlanController::class, 'store'])->name('admin.businessplan.store');
    Route::delete('/businessplan/{businessPlan}', [BusinessPlanController::class, 'destroy'])->name('admin.businessplan.destroy');
    Route::get('/businessplan/edit/{businessPlan}', [BusinessPlanController::class, 'edit'])->name('admin.businessplan.edit');
    Route::post('/businessplan/update/{businessPlan}', [BusinessPlanController::class, 'update'])->name('admin.businessplan.update');

    /** Resource Library */
    Route::get('/resource-library', [ResourceLibraryController::class, 'index'])->name('admin.resource_library');
    Route::get('/resource-library/create', [ResourceLibraryController::class, 'create'])->name('admin.resource_library.create');
    Route::post('/resource-library/store', [ResourceLibraryController::class, 'store'])->name('admin.resource_library.store');
    Route::delete('/resource-library/{resourceLibrary}', [ResourceLibraryController::class, 'destroy'])->name('admin.resource_library.destroy');
    Route::get('/resource-library/edit/{resourceLibrary}', [ResourceLibraryController::class, 'edit'])->name('admin.resource_library.edit');
    Route::post('/resource-library/update/{resourceLibrary}', [ResourceLibraryController::class, 'update'])->name('admin.resource_library.update');

    /** Checklists */
    Route::get('/checklists', [AdminChecklistController::class, 'index'])->name('admin.checklists.index');
    Route::get('/checklists/create', [AdminChecklistController::class, 'create'])->name('admin.checklists.create');
    Route::post('/checklists', [AdminChecklistController::class, 'store'])->name('admin.checklists.store');
    Route::get('/checklists/{checklist}/edit', [AdminChecklistController::class, 'edit'])->name('admin.checklists.edit');
    Route::put('/checklists/{checklist}', [AdminChecklistController::class, 'update'])->name('admin.checklists.update');
    Route::delete('/checklists/{checklist}', [AdminChecklistController::class, 'destroy'])->name('admin.checklists.destroy');
});

// SE
Route::middleware(['auth', 'role:se'])->prefix('se')->group(function () {
    /** Save Homework */
    Route::post('/save-worksheet', [DomeworkController::class, 'saveWorksheet'])->name('save.worksheet');
    Route::get('/', [DashboardController::class, 'loadseDashboard'])->name('se.dashboard');
    Route::get('/upcoming_schedules', [SDashboardController::class, 'upcoming_schedules'])->name('se.upcoming_schedules');
    Route::post('/clock-in', [ClassController::class, 'clockIn'])->name('attendance.clockin');
    Route::get('/attandance_report', [SDashboardController::class, 'loadAttendance'])->name('se.attandance_report');
    Route::get('/schedule-details', [ClassController::class, 'getScheduleDetails'])->name('se.schedule_details');
    Route::get('/resource-library', [SDashboardController::class, 'getresourcelibrary'])->name('se.resource_library');
    Route::post('/compensation-request', [SDashboardController::class, 'storeCompensation'])->name('se.compensation.store');
    Route::get('/notifications', [NotificationController::class, 'fetchAll'])->name('se.notifications');
    Route::get('/assigned_domework', [SDashboardController::class, 'assigned_domework'])->name('se.assigned_domework');
    Route::get('/start_session/{session_id}', [SDashboardController::class, 'start_session'])->name('se.session.start');
    Route::get('/worksheet-pdf/{session_id}', [DomeworkController::class, 'downloadWorksheetPdf'])->name('worksheet.pdf');

    /** checklists */
    Route::post('/checklist/{checklist}/complete', [ChecklistController::class, 'complete'])->name('checklist.complete');
    Route::post('/checklist/{checklist}/incomplete', [ChecklistController::class, 'incomplete'])->name('checklist.incomplete');
    Route::get('/my-checklists', [ChecklistController::class, 'index'])->name('se.checklists');
});

// Instructor
Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->group(function () {
    Route::get('/', [DashboardController::class, 'loadinstructorDashboard'])->name('instructor.dashboard');
    Route::get('/notifications', [NotificationController::class, 'fetchAll'])->name('instructor.notifications');

    /** Session Manage */
    Route::get('/session/view/{id}', [ClassController::class, 'view_session'])->name('instructor.session.view');
    Route::get('/session/edit/{id}', [ClassController::class, 'edit_session'])->name('instructor.session.edit');
    Route::post('/session/create', [ClassController::class, 'create_session'])->name('instructor.session.create');
    Route::post('/session/update{id}', [ClassController::class, 'update_session'])->name('instructor.session.update');
    Route::delete('/session/cancel/{id}', [ClassController::class, 'cancel_session'])
        ->name('instructor.session.cancelled');
    // Route::delete('/session/delete/{id}', [ClassController::class, 'delete_session'])
    // ->name('instructor.session.delete');
    Route::get('/session/managedomework/{id}', [ClassController::class, 'manage_domework'])->name('instructor.session.managedomework');
    Route::get('/schedule_log', [ClassController::class, 'schedule_log'])->name('instructor.schedule_log');
    Route::get('/manage_schedule', [ClassController::class, 'manage_schedule'])->name('instructor.manageschedule');
    Route::get('/session/managedomework/{id}', [ClassController::class, 'manage_domework'])->name('instructor.session.managedomework');
    Route::post('/session/update_domework_assignment/{id}', [ClassController::class, 'update_domework_assignment'])->name('instructor.session.update_domework_assignment');


    /** Domeworks */
    Route::get('/domeworks', [ClassController::class, 'getInstructorDomeworks'])->name('instructor.domeworks');
    Route::get('/domeworks/{session_id}', [ClassController::class, 'viewDomework'])->name('instructor.view.domework');
});

Route::get('/states/{country}', [UserController::class, 'getStates']);
Route::get('/cities/{state}', [UserController::class, 'getCities']);

// Route::get('/', function () {
//     if (auth()->check()) {
//         $user = auth()->user();
//         if (in_array($user->role, ['admin', 'superadmin', 'workforce_development'])) {
//             return redirect()->route('admin.dashboard');
//         } elseif ($user->role == 'se') {
//             return redirect()->route('se.dashboard');
//         } elseif ($user->role == 'instructor') {
//             return redirect()->route('instructor.dashboard');
//         }
//     }
//     return app(LoginController::class)->showLoginForm();
// })->name('login');
Route::get('/admin/login', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if (in_array($user->role, ['admin', 'superadmin', 'workforce_development'])) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role == 'se') {
            return redirect()->route('se.dashboard');
        } elseif ($user->role == 'instructor') {
            return redirect()->route('instructor.dashboard');
        }
    }
    return app(LoginController::class)->showLoginForm();
})->name('login');
Route::post('/admin/login', [LoginController::class, 'login']);
Route::get('/admin/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/admin/forgot-password', [LoginController::class, 'showForgotForm'])->name('password.request');
Route::post('/admin/forgot-password', [LoginController::class, 'sendResetLink'])->name('password.email');
Route::get('/admin/reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/admin/reset-password', [LoginController::class, 'resetPassword'])->name('password.update');
