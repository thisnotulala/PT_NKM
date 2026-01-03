<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SdmController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectPhaseScheduleController;
use App\Http\Controllers\ProjectScheduleGenerateController;
use App\Http\Controllers\EquipmentLoanController;
use App\Http\Controllers\ProjectProgressController;
use App\Http\Controllers\ProjectSdmController;
use App\Http\Controllers\ProjectExpenseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('login');
});

/* AUTH */
Route::get('/login', [AuthController::class, 'formLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/* ADMIN AREA */
Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    /* USER - hanya site_manager */
    Route::middleware('role:site manager')->group(function () {
        Route::get('/user', [UserController::class, 'index'])->name('user.index');
        Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/user', [UserController::class, 'store'])->name('user.store');
        Route::get('/user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    });


    /* CLIENT - site manager & administrasi boleh akses (tanpa delete) */
    Route::middleware('role:site manager,administrasi')->group(function () {
        Route::get('/client', [ClientController::class, 'index'])->name('client.index');
        Route::get('/client/create', [ClientController::class, 'create'])->name('client.create');
        Route::post('/client', [ClientController::class, 'store'])->name('client.store');
        Route::get('/client/{client}/edit', [ClientController::class, 'edit'])->name('client.edit');
        Route::put('/client/{client}', [ClientController::class, 'update'])->name('client.update');
    });

    /* CLIENT - delete hanya site manager */
    Route::middleware('role:site manager')->group(function () {
        Route::delete('/client/{client}', [ClientController::class, 'destroy'])->name('client.destroy');
    }); 

    /* SDM (MASTER) */
    Route::get('/sdm', [SdmController::class, 'index'])->name('sdm.index');
    Route::get('/sdm/create', [SdmController::class, 'create'])->name('sdm.create');
    Route::post('/sdm', [SdmController::class, 'store'])->name('sdm.store');
    Route::get('/sdm/{sdm}/edit', [SdmController::class, 'edit'])->name('sdm.edit');
    Route::put('/sdm/{sdm}', [SdmController::class, 'update'])->name('sdm.update');
    Route::delete('/sdm/{sdm}', [SdmController::class, 'destroy'])->name('sdm.destroy');

    //PENUGASAN SDM
    Route::post('/project/{project}/sdm', [ProjectSdmController::class, 'store'])
        ->name('project.sdm.store');

    Route::delete('/project/{project}/sdm/{assignment}', [ProjectSdmController::class, 'destroy'])
        ->name('project.sdm.destroy');

    /* SATUAN - HANYA ADMINISTRASI */
    Route::middleware(['auth', 'role:administrasi'])->group(function () {
        Route::get('/satuan', [SatuanController::class, 'index'])->name('satuan.index');
        Route::get('/satuan/create', [SatuanController::class, 'create'])->name('satuan.create');
        Route::post('/satuan', [SatuanController::class, 'store'])->name('satuan.store');
        Route::get('/satuan/{satuan}/edit', [SatuanController::class, 'edit'])->name('satuan.edit');
        Route::put('/satuan/{satuan}', [SatuanController::class, 'update'])->name('satuan.update');
        Route::delete('/satuan/{satuan}', [SatuanController::class, 'destroy'])->name('satuan.destroy');
    });

    // EQUIPMENT
    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    Route::get('/equipment/create', [EquipmentController::class, 'create'])->name('equipment.create');
    Route::post('/equipment', [EquipmentController::class, 'store'])->name('equipment.store');
    Route::get('/equipment/{equipment}/edit', [EquipmentController::class, 'edit'])->name('equipment.edit');
    Route::put('/equipment/{equipment}', [EquipmentController::class, 'update'])->name('equipment.update');
    Route::delete('/equipment/{equipment}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');

    Route::get('/project', [ProjectController::class, 'index'])->name('project.index');
    Route::get('/project/create', [ProjectController::class, 'create'])->name('project.create');
    Route::post('/project', [ProjectController::class, 'store'])->name('project.store');
    Route::get('/project/{project}', [ProjectController::class, 'show'])->name('project.show');
    Route::get('/project/{project}/edit', [ProjectController::class, 'edit'])->name('project.edit');
    Route::put('/project/{project}', [ProjectController::class, 'update'])->name('project.update');
    Route::delete('/project/{project}', [ProjectController::class, 'destroy'])->name('project.destroy');

    //EQUIPMENT LOAN
    Route::get('/equipment-loans', [EquipmentLoanController::class, 'index'])->name('equipment_loans.index');
    Route::get('/equipment-loans/create', [EquipmentLoanController::class, 'create'])->name('equipment_loans.create');
    Route::post('/equipment-loans', [EquipmentLoanController::class, 'store'])->name('equipment_loans.store');

    Route::get('/equipment-loans/{loan}', [EquipmentLoanController::class, 'show'])->name('equipment_loans.show');

    Route::post('/equipment-loans/{loan}/approve', [EquipmentLoanController::class, 'approve'])->name('equipment_loans.approve');
    Route::post('/equipment-loans/{loan}/reject', [EquipmentLoanController::class, 'reject'])->name('equipment_loans.reject');

    Route::get('/equipment-loans/{loan}/return', [EquipmentLoanController::class, 'returnForm'])->name('equipment_loans.return.form');
    Route::post('/equipment-loans/{loan}/return', [EquipmentLoanController::class, 'returnStore'])->name('equipment_loans.return.store');


    //SCHEDULE
    Route::get('/jadwal', [ProjectPhaseScheduleController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal/create', [ProjectPhaseScheduleController::class, 'create'])->name('jadwal.create');
    Route::post('/jadwal', [ProjectPhaseScheduleController::class, 'store'])->name('jadwal.store');

    Route::get('/jadwal/{schedule}/edit', [ProjectPhaseScheduleController::class, 'edit'])->name('jadwal.edit');
    Route::put('/jadwal/{schedule}', [ProjectPhaseScheduleController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{schedule}', [ProjectPhaseScheduleController::class, 'destroy'])->name('jadwal.destroy');

    // ajax ambil tahapan berdasarkan proyek
    Route::get('/jadwal/phases/{project}', [ProjectPhaseScheduleController::class, 'phasesByProject'])
        ->name('jadwal.phases');

    Route::get('/project/{project}/jadwal/generate', [ProjectScheduleGenerateController::class, 'form'])
        ->name('project.jadwal.generate.form');

    Route::post('/project/{project}/jadwal/generate', [ProjectScheduleGenerateController::class, 'generate'])
        ->name('project.jadwal.generate.run');

    //LAPORAN
    Route::get('/laporan', [ProjectReportController::class, 'pickProject'])
        ->name('report.pick');

    Route::get('/laporan/project/{project}/pdf', [ProjectReportController::class, 'projectPdf'])
        ->name('report.project.pdf');

    // PROGRESS
    Route::get('/progress-proyek', [ProjectProgressController::class, 'pickProject'])
        ->name('project.progress.pick');
    Route::get('/project/{project}/progress', [ProjectProgressController::class, 'index'])
        ->name('project.progress.index');

    Route::get('/project/{project}/phase/{phase}/progress/create', [ProjectProgressController::class, 'create'])
        ->name('project.progress.create');

    Route::post('/project/{project}/phase/{phase}/progress', [ProjectProgressController::class, 'store'])
        ->name('project.progress.store');


    //PROJECT EXPENSES
    Route::get('/project/{project}/expenses', [ProjectExpenseController::class, 'index'])->name('project.expenses.index');
    Route::get('/project/{project}/expenses/create', [ProjectExpenseController::class, 'create'])->name('project.expenses.create');
    Route::post('/project/{project}/expenses', [ProjectExpenseController::class, 'store'])->name('project.expenses.store');
    Route::get('/project/{project}/expenses/{expense}/edit', [ProjectExpenseController::class, 'edit'])->name('project.expenses.edit');
    Route::put('/project/{project}/expenses/{expense}', [ProjectExpenseController::class, 'update'])->name('project.expenses.update');
    Route::delete('/project/{project}/expenses/{expense}', [ProjectExpenseController::class, 'destroy'])->name('project.expenses.destroy');
    Route::get('/pengeluaran-proyek', [\App\Http\Controllers\ProjectExpenseController::class, 'pickProject'])
        ->name('project.expenses.pick');

    //PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    

});
