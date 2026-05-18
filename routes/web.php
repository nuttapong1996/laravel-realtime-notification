<?php

use App\Http\Controllers\ProfileController;
use App\Models\User;
use App\Notifications\RealtimeNotification;
use Illuminate\Support\Facades\Route;

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
});

require __DIR__ . '/auth.php';


// สร้าง Route สำหรับทดสอบยิง Notification หา User ID ที่กำหนด
Route::get('/send-notification/{userId}', function ($userId) {
    $user = User::find($userId);

    if (!$user) {
        return 'ไม่พบผู้ใช้';
    }

    $message = "มีคิวงานใหม่ถูกมอบหมายให้คุณ ณ เวลา " . now()->format('H:i:s');

    // ส่ง Notification
    $user->notify(new RealtimeNotification($message));

    return "ส่งแจ้งเตือนให้คุณ {$user->name} สำเร็จแล้ว!";
});
