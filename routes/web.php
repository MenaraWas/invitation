<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\WhatsAppStatusController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/wa-status', [WhatsAppStatusController::class, 'index'])->name('wa.status');
Route::post('/wa-status/reset', [WhatsAppStatusController::class, 'reset'])
    ->middleware('auth')
    ->name('wa.status.reset');

Route::get('/inv/{identifier}', [InvitationController::class, 'show'])->name('invitation.show');
Route::post('/inv/{identifier}/verify', [InvitationController::class, 'verify'])->name('invitation.verify');
