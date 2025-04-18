<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use App\Http\Middleware\CheckActivePlan;
use Illuminate\Support\Facades\Log;

class Kernel extends HttpKernel
{
    /**
     * Middleware global para todas as requisições.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Grupo de middlewares para rotas web.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        
        // Adicionando um novo grupo para verificação de plano
        'plan' => [
            'auth',
            \App\Http\Middleware\CheckActivePlan::class,
        ],
    ];

    /**
     * Middlewares individuais que podem ser usados por nome em rotas.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        
        // Tenta registrar o middleware de maneira diferente
        //'check.plan' => \App\Http\Middleware\CheckActivePlan::class,
    ];
    
    // Adicionar uma função construct para registrar um log quando o Kernel for inicializado
    public function __construct(\Illuminate\Contracts\Foundation\Application $app, \Illuminate\Routing\Router $router)
    {
        parent::__construct($app, $router);
        
        // Log para debug
        Log::info('Kernel foi inicializado', [
            'middleware_groups' => array_keys($this->middlewareGroups),
            'route_middleware' => array_keys($this->routeMiddleware),
            'check_active_plan_exists' => class_exists(\App\Http\Middleware\CheckActivePlan::class)
        ]);
    }
}