<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\WhatsAppStatusController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/wa-status', [WhatsAppStatusController::class, 'index'])->name('wa.status');

Route::get('/inv/{token}', [InvitationController::class, 'show'])->name('invitation.show');
Route::post('/inv/{token}/verify', [InvitationController::class, 'verify'])->name('invitation.verify');
