<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Test login as kurir
Route::get('/test/login-kurir', function () {
    $user = \App\Models\User::where('id_user', 39)->first();

    if ($user) {
        Auth::loginUsingId(39);
        return redirect('/kurir/dashboard')->with('success', 'Logged in as kurir user 39');
    }

    return 'User 39 not found';
});

// Test dashboard rendering
Route::get('/test/dashboard-render', function () {
    $user = \App\Models\User::where('id_user', 39)->first();

    if (!$user) {
        return 'User 39 not found';
    }

    Auth::loginUsingId(39);

    // Now test the dashboard
    return redirect('/kurir/dashboard');
});
