<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\StudentCredentialsMail;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\StudentRegisterController;
use App\Http\Controllers\Auth\AdminRegisterController;
use App\Http\Controllers\EnrollmentController;

use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Professor\DashboardController as ProfessorDashboardController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminEnrollmentController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\ProfessorController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Professor\AnnouncementController as ProfessorAnnouncementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Professor\GradeController;
use App\Http\Controllers\Student\StudentAnnouncementController;
use App\Http\Controllers\Student\StudentSettingsController;
use App\Http\Controllers\Professor\ChangePasswordController as ProfessorChangePasswordController;
use App\Http\Controllers\Student\GradeHistoryController;
use App\Http\Controllers\Admin\ChangePasswordController as AdminChangePasswordController;
use App\Http\Controllers\GradeConsolidationController;
use App\Http\Controllers\GradeExportController;
use App\Http\Controllers\Admin\GradeSummaryController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\Professor\ClassesController;
use App\Http\Controllers\Professor\GradeInputController;
use App\Http\Controllers\GradeConsolidatedController;
use App\Http\Controllers\Admin\ECRController;







/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Enrollment
Route::get('/enroll', [EnrollmentController::class, 'showForm'])->name('enroll.form');
Route::post('/enroll', [EnrollmentController::class, 'submit'])->name('enroll.submit');

Route::get('/login/student', [CustomLoginController::class, 'showStudentLogin'])->name('login.student');
Route::post('/login/student', [CustomLoginController::class, 'login'])->name('login.student.submit');

Route::get('/login/professor', [CustomLoginController::class, 'showProfessorLogin'])->name('login.professor');
Route::post('/login/professor', [CustomLoginController::class, 'loginProfessor'])->name('login.professor.submit');


Route::get('/login/admin', [CustomLoginController::class, 'showAdminLogin'])->name('login.admin');
Route::post('/login/admin', [CustomLoginController::class, 'loginAdmin'])->name('login.admin.submit');
Route::get('/register/admin', [AdminRegisterController::class, 'show'])->name('register.admin');
Route::post('/register/admin', [AdminRegisterController::class, 'register'])->name('register.admin.submit');


// Dashboard routes - protected by auth middleware + role middleware (if you have)

        Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    

    Route::get('/professor/dashboard', [ProfessorDashboardController::class, 'index'])->name('professor.dashboard');
    Route::get('/professor/classes', [ProfessorDashboardController::class, 'classes'])->name('professor.classes');
    Route::get('/professor/grades', [GradeController::class, 'index'])->name('professor.grades');
    Route::get('/professor/grades/create', [GradeController::class, 'create'])->name('professor.grades.create');
    Route::get('/professor/reports', [ProfessorDashboardController::class, 'reports'])->name('professor.reports');
    Route::get('/professor/announcements', [ProfessorAnnouncementController::class, 'index'])->name('professor.announcements');
    Route::get('/professor/announcements/create', [ProfessorAnnouncementController::class, 'create'])->name('professor.announcements.create');
    Route::post('/professor/announcements', [ProfessorAnnouncementController::class, 'store'])->name('professor.announcements.store');
    Route::get('/professor/announcements/{announcement}/edit', [ProfessorAnnouncementController::class, 'edit'])->name('professor.announcements.edit');
    Route::put('/professor/announcements/{announcement}', [ProfessorAnnouncementController::class, 'update'])->name('professor.announcements.update');
    Route::delete('/professor/announcements/{announcement}', [ProfessorAnnouncementController::class, 'destroy'])->name('professor.announcements.destroy');
    Route::get('/change-password', [ChangePasswordController::class, 'showChangePasswordForm'])->name('professor.change-password');
    Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])->name('professor.update-password');
    Route::get('/professor/my-classes', [ClassesController::class, 'index'])->name('professor.classes');




    Route::get('/grades/input', [GradeController::class, 'create'])->name('grades.input');
    Route::get('/grades/input', [GradeController::class, 'create'])->name('grades.create');
    Route::post('/grades/store', [GradeController::class, 'store'])->name('grades.store');
    Route::get('/grades/{grade}/edit', [GradeController::class, 'edit'])->name('grades.edit');
    Route::put('/grades/{grade}', [GradeController::class, 'update'])->name('grades.update');
    Route::delete('/grades/{grade}', [GradeController::class, 'destroy'])->name('grades.destroy');
    











    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (General)
|--------------------------------------------------------------------------
*/

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    // Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Enrollment
    Route::get('/admin/enrollments/pending', [AdminEnrollmentController::class, 'pending'])->name('admin.enrollments.pending');
    Route::get('/admin/enrollments', [AdminEnrollmentController::class, 'index'])->name('admin.enrollments.index');
    Route::post('/admin/enrollments/{enrollment}/approve', [AdminEnrollmentController::class, 'approve'])->name('admin.enrollments.approve');
    Route::post('/admin/enrollments/{enrollment}/reject', [AdminEnrollmentController::class, 'reject'])->name('admin.enrollments.reject');

    // Professors
    Route::get('/admin/professors', [ProfessorController::class, 'index'])->name('admin.professors.index');
    Route::get('/admin/professors/create', [ProfessorController::class, 'create'])->name('admin.professors.create');
    Route::post('/admin/professors', [ProfessorController::class, 'store'])->name('admin.professors.store');
    Route::get('/admin/professors/{professor}/edit', [ProfessorController::class, 'edit'])->name('admin.professors.edit');
    Route::put('/admin/professors/{professor}', [ProfessorController::class, 'update'])->name('admin.professors.update');
    Route::delete('/admin/professors/{professor}', [ProfessorController::class, 'destroy'])->name('admin.professors.destroy');
    Route::post('/admin/professors/assign', [ProfessorController::class, 'assignSubject'])->name('admin.professors.assign');
    Route::get('/professor/mclasses', [ClassesController::class, 'index'])->name('professor.classes');



    // Students
    Route::get('/admin/students', [StudentController::class, 'index'])->name('admin.students.index');

    // Announcements
    Route::get('/admin/announcements', [AnnouncementController::class, 'index'])->name('admin.announcements.index');
    Route::get('/admin/announcements/create', [AnnouncementController::class, 'create'])->name('admin.announcements.create');
    Route::post('/admin/announcements', [AnnouncementController::class, 'store'])->name('admin.announcements.store');
    Route::get('/admin/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('admin.announcements.edit');
    Route::put('/admin/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('admin.announcements.update');
    Route::delete('/admin/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('admin.announcements.destroy'); 
    Route::get('/admin/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('admin.announcements.show');
    Route::get('/admin/enrollments/{enrollment}/export', [EnrollmentController::class, 'export'])->name('admin.enrollments.export');
    Route::post('/admin/students', [StudentController::class, 'store'])->name('admin.students.store');
    Route::get('/admin/students', [StudentController::class, 'index'])->name('admin.students.index');
    Route::get('/admin/students/create', [StudentController::class, 'create'])->name('admin.students.create');
    Route::get('/admin/students/{student}/edit', [StudentController::class, 'edit'])->name('admin.students.edit');
    Route::put('/admin/students/{student}', [StudentController::class, 'update'])->name('admin.students.update');
    Route::delete('/admin/students/{student}', [StudentController::class, 'destroy'])->name('admin.students.destroy');


    Route::get('/performance', [\App\Http\Controllers\Student\PerformanceController::class, 'index'])->name('student.academic-performance');
    Route::get('/grade-history', [GradeHistoryController::class, 'index'])->name('student.grade-history');
    Route::get('/student/grades', [StudentController::class, 'grades'])->name('student.grades');
    Route::get('/student/performance', [StudentController::class, 'performance'])->name('student.performance');
    Route::get('/student/history', [StudentController::class, 'history'])->name('student.history');
    Route::get('/announcements', [StudentAnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/settings', [StudentSettingsController::class, 'index'])->name('settings');
    Route::get('/settings', [StudentSettingsController::class, 'index'])->name('student.settings');
    Route::post('/settings/profile-picture', [StudentSettingsController::class, 'updateProfilePicture'])->name('settings.profile-picture');
    Route::post('/settings/change-password', [StudentSettingsController::class, 'changePassword'])->name('settings.change-password');
    Route::post('/student/settings/save-theme', [StudentSettingsController::class, 'saveTheme'])->name('settings.save-theme');
    Route::get('/student/academic-standing', [\App\Http\Controllers\Student\StandingController::class, 'index'])->name('student.academic-standing');
    Route::get('/student/grade-analytics', [\App\Http\Controllers\Student\AnalyticsController::class, 'index'])->name('student.grade-analytics');
    Route::get('/student/grades', [\App\Http\Controllers\Student\GradesController::class, 'index'])->name('student.grades');




use Illuminate\Support\Facades\Auth;

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');





    Route::get('/admin/change-password', [AdminChangePasswordController::class, 'showChangeForm'])->name('admin.change-password.form');
    Route::post('/admin/change-password', [AdminChangePasswordController::class, 'updatePassword'])->name('admin.change-password.update');



Route::get('/professor/change-password', [ProfessorChangePasswordController::class, 'showChangeForm'])->name('professor.change-password.form');
Route::post('/professor/change-password', [ProfessorChangePasswordController::class, 'updatePassword'])->name('professor.change-password.update');
Route::get('/grades/consolidation', [GradeConsolidationController::class, 'index'])->name('grades.consolidation');
Route::get('/admin/grade-summary', [GradeSummaryController::class, 'index'])->name('grades.summary');
Route::get('/report-card/{studentId}', [ReportCardController::class, 'generate'])->name('report.card.generate');
Route::get('/export-grades', [App\Http\Controllers\GradeExportController::class, 'export'])->name('grades.export');
Route::get('/admin/enrollments/archive', [EnrollmentController::class, 'archive'])->name('enrollments.archive');



// Recommended location if your other professor routes are prefixed with /admin
Route::get('/admin/subjects/by-grade/{grade}', [App\Http\Controllers\Admin\ProfessorController::class, 'getSubjectsByGrade']);



Route::prefix('professor')->middleware(['auth'])->group(function () {
    Route::get('/input-grades', [GradeInputController::class, 'index'])->name('grades.input');
    Route::post('/submit-grades', [GradeInputController::class, 'submit'])->name('grades.submit');
});


Route::get('/grades/consolidated', [GradeConsolidatedController::class, 'index'])->name('grades.consolidated');

Route::put('/admin/professors/{professor}', [AdminProfessorController::class, 'ajaxUpdate']);
Route::delete('/admin/professors/{professor}', [AdminProfessorController::class, 'destroy']);

// For update
Route::put('/admin/professors/update', [ProfessorController::class, 'update'])->name('admin.professors.update');
Route::get('/admin/professors/{professor}/destroy', [ProfessorController::class, 'destroy'])->name('admin.professors.destroy');

Route::post('/student/report-card-request', [ReportCardController::class, 'request'])->name('reportcard.request');
Route::get('/student/report-forms', function () {return view('student.report_forms');})->name('student.reportforms');
Route::post('/student/request-form', [ReportFormController::class, 'requestForm'])->name('student.form.request');


// Admin routes for report card requests
Route::get('/admin/report-card/requests', [ReportCardController::class, 'index'])->name('admin.reportcard.requests');
Route::get('/admin/form-requests/archive', [ReportCardController::class, 'archive'])->name('admin.reportcard.archive');
Route::post('/admin/report-card/requests/{id}/approve', [ReportCardController::class, 'approve'])->name('admin.reportcard.approve');
Route::post('/admin/report-card/requests/{id}/decline', [ReportCardController::class, 'decline'])->name('admin.reportcard.decline');
Route::get('/admin/report-card/requests', [ReportCardController::class, 'index'])->name('admin.reportcard.index');



Route::get('admin/class-record', [ECRController::class, 'index'])->name('classrecord.index');
// Route::get('/export-single/{id}', [ExcelController::class, 'exportSingle'])->name('excel.exportSingle');
// Route::get('/export-all', [ExcelController::class, 'exportAll'])->name('excel.exportAll');

use App\Http\Controllers\ExcelController;

Route::get('/export-single/{id}', [ExcelController::class, 'exportSingle'])->name('excel.exportSingle');
Route::get('/export-all', [ExcelController::class, 'exportAll'])->name('excel.exportAll');

use App\Http\Controllers\AttendanceController;


Route::get('/admin/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/admin/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');

