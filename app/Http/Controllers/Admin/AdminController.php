<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TrafficLog;
use App\Models\Campaign;
use App\Models\Domain;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Contagem de usuários ativos por plano
        $usersByPlan = User::where('is_active', true)
            ->select('current_plan_id', DB::raw('count(*) as total'))
            ->groupBy('current_plan_id')
            ->get()
            ->map(function ($item) {
                $plan = Plan::find($item->current_plan_id);
                $item->plan_name = $plan ? $plan->name : 'Sem plano';
                return $item;
            });
        
        // Total de usuários ativos
        $activeUsers = User::where('is_active', true)->count();
        
        // Total de tráfego nas últimas 24 horas
        $traffic24h = TrafficLog::where('created_at', '>=', now()->subHours(24))->count();
        
        // Total de campanhas ativas
        $activeCampaigns = Campaign::where('is_active', true)->count();
        
        // Total de domínios verificados
        $verifiedDomains = Domain::where('verified', true)->count();
        
        // Dados para gráfico - requisições por usuário nos últimos 7 dias
        $startDate = now()->subDays(6)->startOfDay();
        $topUsers = TrafficLog::select('campaign_id', DB::raw('count(*) as total'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('campaign_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $campaign = Campaign::find($item->campaign_id);
                if ($campaign) {
                    $user = User::find($campaign->user_id);
                    $item->user_name = $user ? $user->name . ' ' . $user->surname : 'Desconhecido';
                    $item->campaign_name = $campaign->name;
                } else {
                    $item->user_name = 'Desconhecido';
                    $item->campaign_name = 'Desconhecido';
                }
                return $item;
            });
        
        // Dados para gráfico - requisições por dia nos últimos 7 dias
        $trafficByDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = TrafficLog::whereDate('created_at', $date)->count();
            $trafficByDay[] = [
                'date' => now()->subDays($i)->format('d/m'),
                'count' => $count
            ];
        }
        
        return view('admin.dashboard', compact(
            'usersByPlan',
            'activeUsers',
            'traffic24h',
            'activeCampaigns',
            'verifiedDomains',
            'topUsers',
            'trafficByDay'
        ));
    }
    
    public function users()
    {
        $users = User::with('plan')->get();
        $plans = Plan::all();
        
        return view('admin.users', compact('users', 'plans'));
    }
    
    public function editUser(User $user)
    {
        $plans = Plan::all();
        return view('admin.edit-user', compact('user', 'plans'));
    }
    
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'is_active' => 'boolean',
            'is_admin' => 'boolean',
            'current_plan_id' => 'nullable|exists:plans,id',
            'plan_expires_at' => 'nullable|date',
        ]);
        
        $user->update($validated);
        
        return redirect()->route('admin.users')
            ->with('success', 'Usuário atualizado com sucesso!');
    }
    
    public function trafficLogs(Request $request)
    {
        $query = TrafficLog::with(['campaign.user']);
        
        // Aplicar filtros
        if ($request->has('user_id') && $request->user_id) {
            $query->whereHas('campaign', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%")
                  ->orWhere('user_agent', 'like', "%{$search}%")
                  ->orWhere('request_url', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%");
            });
        }
        
        // Ordernar e paginar
        $logs = $query->orderBy('created_at', 'desc')
                     ->paginate(20);
        
        $users = User::all();
        
        return view('admin.traffic-logs', compact('logs', 'users'));
    }
    
    public function campaigns()
    {
        $campaigns = Campaign::with(['user', 'domain'])
            ->withCount(['trafficLogs', 
                'trafficLogs as offer_logs_count' => function ($query) {
                    $query->where('destination', 'offer');
                }
            ])
            ->get()
            ->map(function ($campaign) {
                $campaign->connect_rate = $campaign->traffic_logs_count > 0
                    ? $campaign->offer_logs_count / $campaign->traffic_logs_count
                    : 0;
                return $campaign;
            });
        
        return view('admin.campaigns', compact('campaigns'));
    }
    
    public function domains()
    {
        $domains = Domain::with('user')->get();
        return view('admin.domains', compact('domains'));
    }
    
    public function plans()
    {
        $plans = Plan::withCount(['users' => function ($query) {
            $query->where('is_active', true);
        }])->get();
        
        return view('admin.plans', compact('plans'));
    }
}