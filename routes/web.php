<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\KategoriPengaduanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AiController;
use Illuminate\Support\Facades\Route;

Route::post('/ai/chat', [AiController::class, 'chat'])->name('ai.chat');
use Illuminate\Support\Facades\File;

// ========================
// Auth Routes
// ========================
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

Route::middleware('guest')->group(function () {
    Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/switch-account/{id}', [LoginController::class, 'switchAccount'])->name('switch.account');
});

// ========================
// Public routes
// ========================
Route::get('/', function () {
    $total_laporan = \App\Models\Pengaduan::count();
    $resolved_laporan = \App\Models\Pengaduan::where('status', 'resolved')->count();
    $resolution_rate = $total_laporan > 0 ? round(($resolved_laporan / $total_laporan) * 100) : 0;
    
    // Additional metrics for banners
    $processed_laporan = \App\Models\Pengaduan::whereIn('status', ['in_progress', 'resolved'])->count();
    $kategori_count = \App\Models\KategoriPengaduan::count();
    // Dummy response rate / rating for now, or you can calculate if you have them
    $response_rate = 98; // Maybe calculate avg response time if applicable
    
    $settingsPath = storage_path('app/settings_home.json');
    $settings = File::exists($settingsPath) ? json_decode(File::get($settingsPath), true) : [
        'hero_pill' => 'Platform Pengaduan Digital SMKN 1 Bondowoso',
        'hero_title_1' => 'Suarakan',
        'hero_title_gradient' => 'Aspirasimu,',
        'hero_title_2' => 'Wujudkan Perubahan!',
        'hero_description' => 'Platform digital transparan untuk menyampaikan aspirasi dan melaporkan masalah di sekolah. Setiap suara didengar, setiap laporan ditindaklanjuti.',
        'feature_title' => 'Mengapa Harus Melaporkan?',
        'feature_description' => 'Dirancang untuk memberikan pengalaman pelaporan yang mudah, aman, dan efektif'
    ];
    
    return view('home', compact(
        'total_laporan', 
        'resolved_laporan', 
        'resolution_rate',
        'processed_laporan',
        'kategori_count',
        'response_rate',
        'settings'
    ));
})->name('home');

// Tracking pengaduan tanpa login
Route::get('/track', [PengaduanController::class, 'track'])->name('track.index');
Route::post('/track', [PengaduanController::class, 'track'])->name('pengaduan.track.search');

// Panduan (Bisa diakses publik tanpa login)
Route::view('/panduan', 'panduan')->name('panduan');

// PWA Offline fallback
Route::get('/offline', function () {
    return response()->file(public_path('offline.html'));
})->name('offline');

// Authenticated routes
Route::middleware(['auth'])->group(function () {

    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');
    Route::get('/dashboard/monthly-report', [DashboardController::class, 'monthlyReport'])->name('dashboard.monthly-report');

    // Pengaduan routes
    Route::resource('pengaduan', PengaduanController::  class);
    
    // Additional pengaduan routes
    Route::post('/pengaduan/{pengaduan}/status', [PengaduanController::class, 'updateStatus'])->name('pengaduan.update-status');
    Route::post('/pengaduan/{pengaduan}/assign', [PengaduanController::class, 'assign'])->name('pengaduan.assign');
    Route::get('/pengaduan/export/data', [PengaduanController::class, 'export'])->name('pengaduan.export');

    // Kategori & Settings routes (Admin only)
    Route::middleware(['can:manage-kategori'])->group(function () {
        Route::resource('kategori', KategoriPengaduanController::class);
        Route::post('/kategori/{kategori}/toggle-active', [KategoriPengaduanController::class, 'toggleActive'])->name('kategori.toggle-active');
        
        // Settings
        Route::get('/settings/home', [SettingController::class, 'editHome'])->name('settings.home');
        Route::post('/settings/home', [SettingController::class, 'updateHome'])->name('settings.home.update');
    });

    // User management routes (Admin only)
    Route::middleware(['can:manage-users'])->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        Route::post('/bulk-action', [UserController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/{user}/performance', [UserController::class, 'performance'])->name('performance');
        
    });
    
    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('admin.logs.index');

    // Management Departemen
    Route::resource('departments', \App\Http\Controllers\DepartmentController::class);

    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('update-avatar');
        Route::delete('/avatar', [ProfileController::class, 'removeAvatar'])->name('remove-avatar');
        Route::get('/activity', [ProfileController::class, 'activity'])->name('activity');
        Route::patch('/notifications', [ProfileController::class, 'updateNotificationPreferences'])->name('update-notifications');
        Route::get('/export-data', [ProfileController::class, 'exportData'])->name('export-data');
    });
});

// API routes for AJAX calls
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    Route::get('/pengaduan/stats', [PengaduanController::class, 'getStatistics'])->name('pengaduan.stats');
    Route::get('/kategori/list', [KategoriPengaduanController::class, 'list'])->name('kategori.list');
});
