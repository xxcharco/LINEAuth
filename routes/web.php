<?php

use App\Http\Controllers\IntimacyLogController;
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
})->name('welcome');

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
        
        // 日付指定の保存ルートを追加（順序を変更）
        Route::post('/store/{date?}', [ConditionController::class, 'store'])->name('store');
        
        // 表示機能
        Route::get('/history', [ConditionController::class, 'history'])->name('history');
        Route::get('/graph', [ConditionController::class, 'graph'])->name('graph');
        Route::get('/cycle', [ConditionController::class, 'cycle'])->name('cycle');
        
        // リソース操作
        Route::get('/{condition}/edit', [ConditionController::class, 'edit'])->name('edit');
        Route::put('/{condition}', [ConditionController::class, 'update'])->name('update');
        Route::delete('/{condition}', [ConditionController::class, 'destroy'])->name('destroy');

        // 日付指定のルート
        Route::get('/date/{date}', [ConditionController::class, 'index'])->name('date');
        });

        // 月経記録関連のルートをグループ化
        Route::prefix('menstruation')->name('menstruation.')->group(function () {
            // 基本機能
            Route::get('/', [MenstruationController::class, 'index'])->name('index');
            Route::get('/create', [MenstruationController::class, 'create'])->name('create');
            Route::post('/', [MenstruationController::class, 'store'])->name('store');
            Route::post('/end', [MenstruationController::class, 'storeEnd'])->name('storeEnd');
        // 編集関連のルート
            Route::get('/{menstruation}/edit', [MenstruationController::class, 'edit'])->name('edit');
            Route::put('/{menstruation}', [MenstruationController::class, 'update'])->name('update');
            Route::delete('/{menstruation}', [MenstruationController::class, 'destroy'])->name('destroy');
    });

        // なかよしログ関連のルートをグループ化
        Route::prefix('intimacy')->name('intimacy.')->group(function () {
            Route::get('/', [IntimacyLogController::class, 'index'])->name('index');
            Route::post('/record', [IntimacyLogController::class, 'store'])->name('store');
            Route::post('/record-batch', [IntimacyLogController::class, 'storeBatch'])->name('storeBatch');
            Route::get('/list', [IntimacyLogController::class, 'list'])->name('list');
            Route::get('/complete', [IntimacyLogController::class, 'complete'])->name('complete');
        });
});

// Webhookルートの追加（authミドルウェア不要）
Route::post('webhook/linebot', [LineWebhookController::class, 'reply']);

require __DIR__.'/auth.php';
