<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Livewire\NewIdea::class)->name('dashboard');
    Route::get('ideas/{idea}', Livewire\SeeIdea::class)->name('see-idea');
    Route::get('ideas/{idea}/live_meeting', Livewire\LiveMeeting::class)->name('meeting');
    Route::get('meetings/{meeting}', Livewire\SeeMeeting::class)->name('see-meeting');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
