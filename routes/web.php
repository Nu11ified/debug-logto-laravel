<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Logto\Sdk\LogtoClient;
use Logto\Sdk\LogtoConfig;

// Initialize Logto client
$logtoEndpoint = env('LOGTO_ENDPOINT', 'default-endpoint');
$logtoAppId = env('LOGTO_APP_ID', 'default-app-id');
$logtoAppSecret = env('LOGTO_APP_SECRET', 'default-app-secret');

if ($logtoEndpoint === 'default-endpoint' || $logtoAppId === 'default-app-id' || $logtoAppSecret === 'default-app-secret') {
    throw new \Exception('Logto configuration not set properly in .env file');
}

$client = new LogtoClient(
    new LogtoConfig(
        endpoint: $logtoEndpoint,
        appId: $logtoAppId,
        appSecret: $logtoAppSecret
    )
);

// Root route
Route::get('/', function () use ($client) {
    return Inertia::render('Welcome', [
        'canLogin' => !$client->isAuthenticated(),
        'canRegister' => true,
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

// Dashboard route
Route::get('/dashboard', function () use ($client) {
    if (!$client->isAuthenticated()) {
        return redirect()->route('sign-in');
    }
    return Inertia::render('Dashboard');
})->name('dashboard');

// Sign-in route
Route::get('/sign-in', function () use ($client) {
    return redirect($client->signIn('https://panel.justicerp.com/callback'));
})->name('sign-in');

// Sign-out route
Route::get('/sign-out', function () use ($client) {
    return redirect($client->signOut(route('home')));
})->name('sign-out');

// Callback route
Route::get('/callback', function () use ($client) {
    try {
        $client->handleSignInCallback();
        session(['authenticated' => true]);
    } catch (\Throwable $exception) {
        // Display the error message directly to the user
        return response()->json(['error' => 'Sign-in failed: ' . $exception->getMessage()], 500);
    }
    return redirect()->route('dashboard');
})->name('callback');

// User info route
Route::get('/userinfo', function () use ($client) {
    if (!$client->isAuthenticated()) {
        return "Not authenticated <a href='/sign-in'>Sign in</a>";
    }

    $idTokenClaims = $client->getIdTokenClaims();
    $userInfo = $client->fetchUserInfo();

    $html = "<h1>User Information</h1>";
    $html .= "<h2>ID Token Claims</h2>";
    $html .= "<pre>" . htmlspecialchars(json_encode($idTokenClaims, JSON_PRETTY_PRINT)) . "</pre>";
    $html .= "<h2>User Info</h2>";
    $html .= "<pre>" . htmlspecialchars(json_encode($userInfo, JSON_PRETTY_PRINT)) . "</pre>";
    $html .= "<a href='/sign-out'>Sign out</a>";

    return $html;
})->name('userinfo');
