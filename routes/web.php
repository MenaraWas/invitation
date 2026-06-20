<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvitationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/inv/{token}', [InvitationController::class, 'show'])->name('invitation.show');
Route::post('/inv/{token}/verify', [InvitationController::class, 'verify'])->name('invitation.verify');
