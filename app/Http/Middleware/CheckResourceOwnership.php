<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckResourceOwnership
{
    public function handle(Request $request, Closure $next, $model = null, $paramName = null)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if (!$model) {
            return $next($request);
        }
        
        $paramName = $paramName ?? strtolower(class_basename($model));
        $resource = $request->route($paramName);

        if (!$resource) {
            return $next($request);
        }

        if ($resource->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Você não tem permissão para acessar este recurso.');
        }

        return $next($request);
    }
}