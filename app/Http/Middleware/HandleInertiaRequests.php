<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Logto\Sdk\LogtoClient;
use Logto\Sdk\LogtoConfig;


class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * The Logto client instance.
     *
     * @var LogtoClient
     */
    protected $client;

    /**
     * Initialize the middleware with the Logto client.
     */
    public function __construct()
    {
        $this->client = new LogtoClient(
            new LogtoConfig(
                endpoint: env('LOGTO_ENDPOINT'),
                appId: env('LOGTO_APP_ID'),
                appSecret: env('LOGTO_APP_SECRET')
            )
        );
    }

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
        ];
    }
}
