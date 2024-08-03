<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Logto\Sdk\LogtoClient;
use Logto\Sdk\LogtoConfig;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new LogtoClient(
            new LogtoConfig(
                endpoint: env('LOGTO_ENDPOINT', 'https://your-logto-endpoint.app'),
                appId: env('LOGTO_APP_ID', 'replace-with-your-app-id'),
                appSecret: env('LOGTO_APP_SECRET', 'replace-with-app-secret'),
            ),
        );
    }

    public function callback(Request $request)
    {
        try {
            $this->client->handleSignInCallback();
            return redirect('/')->with('status', 'Signed in successfully!');
        } catch (\Throwable $exception) {
            Log::error('Sign-in callback error: ' . $exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function signIn()
    {
        $redirectUri = 'https://panel.justicerp.com/callback';
        return redirect($this->client->signIn($redirectUri));
    }

    public function signOut()
    {
        return redirect($this->client->signOut('https://panel.justicerp.com/'));
    }

    public function home()
    {
        if ($this->client->isAuthenticated() === false) {
            return "Not authenticated <a href='/sign-in'>Sign in</a>";
        }
        return "<a href='/sign-out'>Sign out</a>";
    }

    public function userInfo()
    {
        if ($this->client->isAuthenticated() === false) {
            return "Not authenticated <a href='/sign-in'>Sign in</a>";
        }
        $idTokenClaims = json_decode(json_encode($this->client->getIdTokenClaims()), true);
        $userInfo = json_decode(json_encode($this->client->fetchUserInfo()), true);

        return response()->json([
            'idTokenClaims' => $idTokenClaims,
            'userInfo' => $userInfo,
        ]);
    }
}
