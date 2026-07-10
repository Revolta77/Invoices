<?php

use App\Http\Controllers\Auth\GoogleLinkController;
use App\Livewire\AppShell;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::get('/auth/google/link', [GoogleLinkController::class, 'redirect'])
        ->name('auth.google.link');
    Route::get('/auth/google/link/callback', [GoogleLinkController::class, 'linkCallback'])
        ->name('auth.google.link.callback');

    Route::livewire('/app', AppShell::class)->name('app.shell');
});
