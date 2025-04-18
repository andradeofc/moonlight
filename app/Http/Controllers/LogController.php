<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrafficLog;
use App\Models\Campaign;

class LogController extends Controller
{
    public function index(Request $request)
    {
        // Aplicar filtros
        $query = TrafficLog::with('campaign');
        
        if ($request->has('campaign_id') && $request->campaign_id) {
            $query->where('campaign_id', $request->campaign_id);
        }
        
        if ($request->has('destination') && $request->destination) {
            $query->where('destination', $request->destination);
        }
        
        if ($request->has('country') && $request->country) {
            $query->where('country', $request->country);
        }
        
        if ($request->has('device_type') && $request->device_type) {
            $query->where('device_type', $request->device_type);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->has('ip_address') && $request->ip_address) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }
        
        // Obter os logs com paginação
        $logs = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Obter campanhas para o filtro de seleção
        $campaigns = Campaign::pluck('name', 'id')->toArray();
        
        // Países para filtro
        $countries = ['US', 'CA', 'BR', 'UK', 'AU', 'DE', 'FR', 'ES', 'IT', 'JP'];
        
        return view('logs.index', compact('logs', 'campaigns', 'countries'));
    }
}