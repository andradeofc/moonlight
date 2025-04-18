<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckActivePlan
{
    public function handle(Request $request, Closure $next)
    {
        // Verifica se o usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Verifica se o usuário tem um plano ativo
        if (!$user->hasActivePlan()) {
            return redirect()->route('plans.index')
                ->with('warning', 'Você precisa escolher um plano para acessar o sistema.');
        }
        
        return $next($request);
    }
}