<?php

use App\Http\Controllers\CloakerController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;



// Rota para processar o formulário da prepage 
Route::post('/r/{campaign_id}', [CloakerController::class, 'redirect'])->name('campaign.redirect.post');

Route::get('/', [CloakerController::class, 'handleDomain']);
Route::get('/r/{campaign_id}', [CloakerController::class, 'redirect'])->name('campaign.redirect');


// Rota de teste
Route::get('/test', function() {
    return 'Route test is working!';
});

Route::get('/settings', function () {
    return view('settings'); // certifique-se de criar a view resources/views/settings.blade.php 
})->name('settings');


// Rota principal para logs
Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

// Rota principal para redirecionamento
Route::get('/r/{campaign_id}', [CloakerController::class, 'redirect'])->name('redirect');

// Rota de dashboard/home
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Rotas para administração
Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return redirect()->route('campaigns.index');
    });
    
    // Rotas para domínios
    Route::resource('domains', DomainController::class);
    Route::post('/domains/{domain}/verify', [DomainController::class, 'verify'])->name('domains.verify');
    
    // Rotas para campanhas
    Route::resource('campaigns', CampaignController::class);
    
    // Adicione aqui outras rotas administrativas
});


// Rotas de autenticação
Auth::routes();