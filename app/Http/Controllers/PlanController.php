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
        // Obter apenas os planos Básico, PRO e Freedom
        $plans = Plan::where('is_active', true)
                ->whereIn('name', ['Plano Básico', 'Plano PRO', 'Plano Freedom'])
                ->orderBy('price') // Ordena por preço
                ->get();
        
        return view('plans.index', compact('plans'));
    }

     /**
     * Display the specified plan.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        // Verificar se o plano acessado é um dos permitidos
        $allowedPlans = ['Plano Básico', 'Plano PRO', 'Plano Freedom'];
        
        if (!in_array($plan->name, $allowedPlans)) {
            return redirect()->route('plans.index')
                ->with('warning', 'Plano não disponível');
        }
        
        $paymentLink = $plan->payment_url; // Use o link de pagamento salvo no banco
        return view('plans.show', compact('plan', 'paymentLink'));
    }
}