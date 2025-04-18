<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckActivePlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Permite o acesso por enquanto, enquanto estamos configurando
        return $next($request);
        
        /* Código a ser habilitado posteriormente
        // Verifica se o usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        Log::info('CheckActivePlan middleware:', [
            'user_id' => $user->id,
            'is_active' => $user->is_active,
            'current_plan_id' => $user->current_plan_id,
            'plan_expires_at' => $user->plan_expires_at
        ]);

        // Verifica se o usuário tem um plano ativo
        if (!$user->hasActivePlan()) {
            // Redireciona para a página de planos com mensagem de aviso
            Log::info('User does not have active plan, redirecting to plans.index');
            
            // Redirecionar para a página de planos
            return redirect()->route('plans.index')
                ->with('warning', 'Você precisa escolher um plano para acessar o sistema.');
        }
        */
    }
}