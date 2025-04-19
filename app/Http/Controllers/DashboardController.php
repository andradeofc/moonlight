<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\TrafficLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
{
    // Obter IDs de campanhas do usuário atual
    $userCampaignIds = auth()->user()->campaigns()->pluck('id')->toArray();
    
    // Stats for cards - filtrar por campanhas do usuário atual
    $totalRequests = TrafficLog::whereIn('campaign_id', $userCampaignIds)->count();
    $safePageRequests = TrafficLog::whereIn('campaign_id', $userCampaignIds)->where('destination', 'safe')->count();
    $offerPageRequests = TrafficLog::whereIn('campaign_id', $userCampaignIds)->where('destination', 'offer')->count();
    
    // Traffic data for chart (last 7 days)
    $dates = [];
    $totalData = [];
    $safeData = [];
    $offerData = [];
    
    // Get data for last 7 days
    for ($i = 6; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i)->format('Y-m-d');
        $dates[] = Carbon::now()->subDays($i)->format('M d');
        
        $dayTotal = TrafficLog::whereIn('campaign_id', $userCampaignIds)
            ->whereDate('created_at', $date)->count();
        $daySafe = TrafficLog::whereIn('campaign_id', $userCampaignIds)
            ->whereDate('created_at', $date)->where('destination', 'safe')->count();
        $dayOffer = TrafficLog::whereIn('campaign_id', $userCampaignIds)
            ->whereDate('created_at', $date)->where('destination', 'offer')->count();
        
        $totalData[] = $dayTotal;
        $safeData[] = $daySafe;
        $offerData[] = $dayOffer;
    }
    
    $trafficData = [
        'dates' => $dates,
        'total' => $totalData,
        'safe' => $safeData,
        'offer' => $offerData
    ];
    
    // Country data for pie chart - filtrar por campanhas do usuário atual
    $countryStats = TrafficLog::select('country', DB::raw('count(*) as total'))
        ->whereIn('campaign_id', $userCampaignIds)
        ->whereNotNull('country')
        ->groupBy('country')
        ->orderBy('total', 'desc')
        ->limit(10)
        ->get();
    
    $countryData = [
        'labels' => $countryStats->pluck('country')->toArray(),
        'data' => $countryStats->pluck('total')->toArray()
    ];
    
    // Recent campaigns - apenas do usuário atual
    $recentCampaigns = auth()->user()->campaigns()
        ->with('domain')
        ->withCount('trafficLogs as requests_count')
        ->withCount(['trafficLogs as offer_count' => function ($query) {
            $query->where('destination', 'offer');
        }])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    // Calculate connect rate
    foreach ($recentCampaigns as $campaign) {
        $campaign->connect_rate = $campaign->requests_count > 0 
            ? $campaign->offer_count / $campaign->requests_count 
            : 0;
    }
    
    return view('dashboard', compact(
        'totalRequests', 
        'safePageRequests', 
        'offerPageRequests',
        'trafficData',
        'countryData',
        'recentCampaigns'
    ));
}
}