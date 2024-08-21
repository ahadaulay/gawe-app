<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectToolController;
use App\Http\Controllers\ProjectApplicantController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\WalletTransactionController;

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

    Route::middleware(['can:withdraw wallet'])->group(function () {
        Route::get('/dashboard/wallet',[DashboardController::class,'wallet'])->name('dashboard.wallet');
        Route::get('/dashboard/wallet/withdraw',[DashboardController::class,'withdraw_wallet'])->name('dashboard.wallet.withdraw');
        Route::post('/dashboard/wallet/withdraw/store',[DashboardController::class,'withdraw_wallet_store'])->name('dashboard.wallet.withdraw.store');
    });

    Route::middleware(['can:topup wallet'])->group(function () {
        Route::get('/dashboard/wallet/topup',[DashboardController::class,'topup_wallet'])->name('dashboard.wallet.topup');
        Route::post('/dashboard/wallet/topup/store',[DashboardController::class,'topup_wallet_store'])->name('dashboard.wallet.topup.store');
    });

    Route::middleware(['can:apply job'])->group(function () {
       Route::get('/apply/{project:slug}',[FrontController::class,'apply_job'])->name('front.apply_job');
       Route::post('/apply/{project:slug}',[FrontController::class,'apply_job_store'])->name('front.apply_job.store');
       Route::get('/dashboard/proposals',[DashboardController::class,'proposals'])->name('dashboard.proposals');
       Route::get('/dashboard/proposals_details/{project}/{projectApplicant}',[DashboardController::class,'proposal_details'])->name('dashboard.proposal.details'); 
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::middleware(['can:manage wallets'])->group(function () {
            Route::get('/wallet/topups',[WalletTransactionController::class,'wallet_topups'])->name('topups'); 
            Route::get('/wallet/withdrawal',[WalletTransactionController::class,'wallet_withdrawal'])->name('withdrawal');
            Route::resource('wallet_transactions', WalletTransactionController::class);
        });

        Route::middleware(['can:manage applicants'])->group(function () {
            Route::resource('project_applicants', ProjectApplicantController::class);
        });

        Route::middleware(['can:manage projects'])->group(function () {
            Route::resource('projects', ProjectApplicantController::class);

            Route::post('/projects/{projectApplicant}/completed', [ProjectController::class, 'complete_project_store'])->name('complete_projects.store');
            Route::get('/projects/{project}/tools',[ProjectController::class,'tools'])->name('projects.tools');
            Route::post('/projects/{project}/tools/store',[ProjectController::class,'tools_store'])->name('projects.tools.store');

            Route::resource('project_tools', ProjectToolController::class);
        });

        Route::middleware(['can:manage categories'])->group(function () {
            Route::resource('categories', CategoryController::class);
        });

        Route::middleware(['can:manage tools'])->group(function () {
            Route::resource('tools', ToolController::class);
        });
    });
    

    

});

require __DIR__.'/auth.php';
