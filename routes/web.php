<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\CloakerController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| Rotas Públicas (Sem Autenticação)
|--------------------------------------------------------------------------
*/

// Redirecionamento da campanha (GET e POST)
Route::get('/r/{campaign_id}', [CloakerController::class, 'redirect'])->name('campaign.redirect');
Route::post('/r/{campaign_id}', [CloakerController::class, 'redirect'])->name('campaign.redirect.post');

// Rota de domínio direto (landing sem parâmetros)
Route::get('/domain', [CloakerController::class, 'handleDomain']);

// Rotas para páginas intermediárias (se houver)
Route::get('/prepage/{encoded_url}', [CloakerController::class, 'prePage'])->name('prepage');
Route::post('/prepage/proceed', [CloakerController::class, 'prePageProceed'])->name('prepage.proceed');

// Testes e Debug
Route::get('/test', function() {
    return 'Route test is working!';
});

// Webhooks
Route::post('/webhook/perfectpay', [WebhookController::class, 'handlePerfectPayWebhook'])->name('webhook.perfectpay');

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação
|--------------------------------------------------------------------------
*/

Auth::routes();

// Página inicial
Route::get('/', function() {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Rotas Protegidas por Autenticação + Plano Ativo
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.plan'])->group(function () {
    // Dashboard e home
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Logs de tráfego
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    
    // Configurações
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');
    
    // Administração
    Route::prefix('admin')->group(function () {
        Route::get('/', function () {
            return redirect()->route('campaigns.index');
        });
        
        // Domínios
        Route::resource('domains', DomainController::class);
        Route::post('/domains/{domain}/verify', [DomainController::class, 'verify'])->name('domains.verify');
        
        // Campanhas
        Route::resource('campaigns', CampaignController::class);
    });
}); 
/*
|--------------------------------------------------------------------------
| Rotas Protegidas por Autenticação + Dashboard
|--------------------------------------------------------------------------
*/

// REMOVA O MIDDLEWARE active.plan TEMPORARIAMENTE ATÉ ESTAR CORRETAMENTE CONFIGURADO
Route::middleware(['auth'])->group(function () {

     // Visualização dos planos
     // Rotas de planos
        Route::get('/plans', [App\Http\Controllers\PlanController::class, 'index'])->name('plans.index');
        Route::get('/plans/{id}', [App\Http\Controllers\PlanController::class, 'show'])->name('plans.show');
    // Dashboard e home
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Logs de tráfego
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    
    // Configurações
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');
    
    // Administração
    Route::prefix('admin')->group(function () {
        Route::get('/', function () {
            return redirect()->route('campaigns.index');
        });
        
        // Domínios
        Route::resource('domains', DomainController::class);
        Route::post('/domains/{domain}/verify', [DomainController::class, 'verify'])->name('domains.verify');
        
        // Campanhas
        Route::resource('campaigns', CampaignController::class);
    });
});