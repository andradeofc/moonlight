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

// Rota para webhook da PerfectPay (não protegida)
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
        // Se o usuário não tem plano ativo, redireciona para a página de planos
        if (!auth()->user()->hasActivePlan()) {
            return redirect()->route('plans.index');
        }
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Rotas Protegidas por Autenticação (Sem exigir plano ativo)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Visualização dos planos - Acessível para todos usuários autenticados
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/{plan}', [PlanController::class, 'show'])->name('plans.show');
});

/*
|--------------------------------------------------------------------------
| Rotas Protegidas por Autenticação + Plano Ativo
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard e home
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home.dashboard');

    // Logs de tráfego
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    
    // Configurações
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');
    
    // Rotas de Campanhas - com verificação via policy
    Route::get('campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::get('campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create');
    Route::post('campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::get('campaigns/{campaign}', [CampaignController::class, 'show'])
        ->name('campaigns.show')
        ->middleware('can:view,campaign');
    Route::get('campaigns/{campaign}/edit', [CampaignController::class, 'edit'])
        ->name('campaigns.edit')
        ->middleware('can:update,campaign');
    Route::put('campaigns/{campaign}', [CampaignController::class, 'update'])
        ->name('campaigns.update')
        ->middleware('can:update,campaign');
    Route::delete('campaigns/{campaign}', [CampaignController::class, 'destroy'])
        ->name('campaigns.destroy')
        ->middleware('can:delete,campaign');
    Route::post('campaigns/proceed', [CampaignController::class, 'prePageProceed'])->name('campaigns.proceed');
    
    // Rotas de Domínios - com verificação via policy
    Route::get('domains', [DomainController::class, 'index'])->name('domains.index');
    Route::get('domains/create', [DomainController::class, 'create'])->name('domains.create');
    Route::post('domains', [DomainController::class, 'store'])->name('domains.store');
    Route::get('domains/{domain}', [DomainController::class, 'show'])
        ->name('domains.show')
        ->middleware('can:view,domain');
    Route::get('domains/{domain}/edit', [DomainController::class, 'edit'])
        ->name('domains.edit')
        ->middleware('can:update,domain');
    Route::put('domains/{domain}', [DomainController::class, 'update'])
        ->name('domains.update')
        ->middleware('can:update,domain');
    Route::delete('domains/{domain}', [DomainController::class, 'destroy'])
        ->name('domains.destroy')
        ->middleware('can:delete,domain');
    Route::post('domains/{domain}/verify', [DomainController::class, 'verify'])
        ->name('domains.verify')
        ->middleware('can:verify,domain');
});


// Rotas do painel administrativo - protegidas pelo middleware 'admin'
Route::middleware(['auth', \App\Http\Middleware\AdminAccess::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\Admin\AdminController::class, 'updateUser'])->name('users.update');
    Route::get('/traffic-logs', [App\Http\Controllers\Admin\AdminController::class, 'trafficLogs'])->name('traffic-logs');
    Route::get('/campaigns', [App\Http\Controllers\Admin\AdminController::class, 'campaigns'])->name('campaigns');
    Route::get('/domains', [App\Http\Controllers\Admin\AdminController::class, 'domains'])->name('domains');
    Route::get('/plans', [App\Http\Controllers\Admin\AdminController::class, 'plans'])->name('plans');
});