<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LineLoginController;
use App\Http\Controllers\PartnershipController;
use App\Http\Controllers\LineWebhookController; 
use App\Http\Controllers\PartnershipInvitationController;
use App\Http\Controllers\ConditionController;
use App\Http\Controllers\MenstruationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('guest')->group(function () {
    Route::get('/line/login', [LineLoginController::class, 'lineLogin'])
        ->name('line.login');
    Route::get('/line/callback', [LineLoginController::class, 'callback'])
        ->name('line.callback');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('partnerships')->group(function () {
        // パートナーシップ情報の表示
        Route::get('/', [PartnershipController::class, 'show'])
            ->name('partnerships.show');
            
        // マッチング処理
        Route::post('/join/{token}', [PartnershipController::class, 'processMatch'])
            ->name('partnerships.match');

        // 招待関連（
        Route::get('/invite', [PartnershipInvitationController::class, 'create'])
            ->name('partnerships.invite');
        Route::post('/invite', [PartnershipInvitationController::class, 'store'])
            ->name('partnerships.create');
        
        // 招待リンク表示
        Route::get('/invitation', [PartnershipInvitationController::class, 'show'])
            ->name('partnerships.invitation');

        // 招待承認関連
        Route::get('/join/{token}', [PartnershipInvitationController::class, 'showJoin'])
            ->name('partnerships.join');
    });

        // 体調記録関連のルートをグループ化
        Route::prefix('conditions')->name('conditions.')->group(function () {
            // 基本機能
            Route::get('/', [ConditionController::class, 'index'])->name('index');
            Route::post('/store', [ConditionController::class, 'store'])->name('store');
            
            // 表示機能
            Route::get('/history', [ConditionController::class, 'history'])->name('history');
            Route::get('/graph', [ConditionController::class, 'graph'])->name('graph');
            Route::get('/cycle', [ConditionController::class, 'cycle'])->name('cycle');
            
            // リソース操作
            Route::get('/{condition}/edit', [ConditionController::class, 'edit'])->name('edit');
            Route::put('/{condition}', [ConditionController::class, 'update'])->name('update');
            Route::delete('/{condition}', [ConditionController::class, 'destroy'])->name('destroy');
        });

            // // 月経記録関連のルート
            // Route::get('/menstruation', [MenstruationController::class, 'index'])->name('menstruation.index');
            // Route::get('/menstruation/create', [MenstruationController::class, 'create'])->name('menstruation.create');
            // Route::post('/menstruation', [MenstruationController::class, 'store'])->name('menstruation.store');
            // Route::post('/menstruation/end', [MenstruationController::class, 'storeEnd'])->name('menstruation.storeEnd');
});

// Webhookルートの追加（authミドルウェア不要）
Route::post('webhook/linebot', [LineWebhookController::class, 'reply']);

require __DIR__.'/auth.php';
