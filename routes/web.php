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
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => true,
        'canRegister' => true,
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

// Dashboard route
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

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
    if ($client->isAuthenticated() === false) {
        return "Not authenticated <a href='/sign-in'>Sign in</a>";
      }

    $idTokenClaims = $client->getIdTokenClaims();
    $userInfo = $client->fetchUserInfo();

    return response()->json([
        'id_token_claims' => $idTokenClaims,
        'user_info' => $userInfo
    ]);
})->name('userinfo');
