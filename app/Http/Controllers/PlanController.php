<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PlanController extends Controller
{
    /**
     * Display a listing of the available plans.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Temporariamente, vamos apenas retornar para o dashboard
        // até que a tabela de planos seja criada
        Log::info("Exibindo página de planos (temporária)");
        
        // Para teste, redirecionamos diretamente para o dashboard
        return view('plans.fallback');
    }

    /**
     * Display the specified plan.
     *
     * @param  int  $plan
     * @return \Illuminate\Http\Response
     */
    public function show($plan)
    {
        // Implementação temporária
        return view('plans.fallback');
    }
}