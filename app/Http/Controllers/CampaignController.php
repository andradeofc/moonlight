<?php
// app/Http/Controllers/CampaignController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Domain;
use App\Models\TrafficLog;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function index()
{
    $campaigns = Campaign::with('domain')
        ->withCount([
            'trafficLogs',
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

    return view('campaigns.index', compact('campaigns'));
}
    
    public function create()
    {
        return view('campaigns.create', [
            'domains' => Domain::where('verified', true)->get()
        ]);
    }
    
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'domain_id' => 'required|exists:domains,id',
        'language' => 'required|string',
        'traffic_source' => 'required|string',
        'safe_url' => 'required|url',
        'safe_method' => 'required|in:ml_redirect,pre_page', 
        'offer_url' => 'required|url',
        'offer_method' => 'required|in:redirect,twr_mirror',
        'countries' => 'required|array',
        'devices' => 'required|array',
        'tags' => 'nullable|array',
    ]);
    
    // Gerar valores aleatórios
    $token = Str::random(8);
    $unique_id = Str::random(8);
    $uniqueParam = '_' . Str::random(7);
    $xid = Str::random(10);
    
    \Log::info('Gerando nova campanha', [
        'token' => $token,
        'unique_id' => $unique_id,
        'xid' => $xid
    ]);
    
    $campaign = Campaign::create([
        'name' => $validated['name'],
        'domain_id' => $validated['domain_id'],
        'language' => $validated['language'],
        'traffic_source' => $validated['traffic_source'],
        'safe_url' => $validated['safe_url'],
        'safe_method' => $validated['safe_method'],
        'offer_url' => $validated['offer_url'],
        'offer_method' => $validated['offer_method'],
        'countries' => json_encode($validated['countries']),
        'devices' => json_encode($validated['devices']),
        'tags' => $validated['tags'] ? json_encode($validated['tags']) : null,
        'token' => $token,
        'unique_id' => $unique_id,
        'unique_params' => json_encode([$uniqueParam => $unique_id]),
        'xid' => $xid, // Certifique-se de que o XID está sendo incluído 
        'is_active' => $request->has('is_active')
    ]);

    // Verificar se o XID foi corretamente salvo
    $savedCampaign = Campaign::find($campaign->id);
    \Log::info('Campanha criada - verificando XID', [
        'xid_original' => $xid,
        'xid_salvo' => $savedCampaign->xid
    ]);

    // Se o XID não foi salvo corretamente, atualize-o explicitamente
    if ($savedCampaign->xid !== $xid) {
        \Log::warning('XID não salvo corretamente, atualizando diretamente', [
            'xid_original' => $xid,
            'xid_banco' => $savedCampaign->xid
        ]);
        
        // Atualização direta no banco
        \DB::table('campaigns')
            ->where('id', $campaign->id)
            ->update(['xid' => $xid]);
            
        // Recarregar a campanha
        $campaign = Campaign::find($campaign->id);
    }
    
    // Gerar URL e parâmetros para o usuário
    $domain = Domain::find($validated['domain_id']);
    $campaignUrl = "https://{$domain->name}/r/{$campaign->id}";
    
    // Use FB_OPEN e FB_CLOSE como marcadores temporários 
    // que o Blade não vai tentar interpretar
    $params = "cwr={$campaign->id}"
        . "&tok={$token}"
        . "&{$uniqueParam}={$unique_id}"
        . "&utm_id=FB_OPENutm_idFB_CLOSE"
        . "&fbclid=FB_OPENfbclidFB_CLOSE"
        . "&cname=FB_OPENcampaign.nameFB_CLOSE"
        . "&domain=FB_OPENdomainFB_CLOSE"
        . "&placement=FB_OPENplacementFB_CLOSE"
        . "&adset=FB_OPENadset.nameFB_CLOSE"
        . "&adname=FB_OPENad.nameFB_CLOSE"
        . "&site=FB_OPENsite_source_nameFB_CLOSE"
        . "&xid={$xid}";
    
    // Substituir os marcadores temporários para armazenar na sessão
    $paramsForSession = str_replace(['FB_OPEN', 'FB_CLOSE'], ['{{', '}}'], $params);
    
    $instructions = "Parâmetros prontos para uso no Facebook Ads!";
    
    return redirect()->route('campaigns.show', $campaign)
        ->with('success', 'Campaign created successfully')
        ->with('campaignUrl', $campaignUrl)
        ->with('params', $paramsForSession) // A versão que será exibida na página
        ->with('instructions', $instructions);
}
    
public function show(Campaign $campaign)
{
    \Log::info('DOMAIN DEBUG:', [
        'campaign_id' => $campaign->id,
        'domain_id' => $campaign->domain_id,
        'domain' => optional($campaign->domain)->name
    ]);

    // Verifique se o domínio existe no banco
    $domain = \App\Models\Domain::find($campaign->domain_id);
    \Log::info('DOMÍNIO NO BANCO:', optional($domain)->toArray());

    // Carregue manualmente a relação
    if (!$campaign->relationLoaded('domain')) {
        $campaign->load('domain');
    }
        
    \Log::info('RELACIONAMENTO domain APÓS load():', [
        'is_object' => is_object($campaign->domain),
        'loaded?' => $campaign->relationLoaded('domain'),
        'domain_model' => optional($campaign->domain)->toArray()
    ]);
        
    // Gerar URL e parâmetros para exibição
    $campaignUrl = $campaign->resolved_domain
    ? "https://{$campaign->resolved_domain}/r/{$campaign->id}"
    : "Domínio não configurado";

    // Verificar se unique_params existe e definir um valor padrão caso contrário
    $uniqueParam = '_param'; // Valor padrão simplificado
    
    // Se unique_params existir e for um JSON válido, use-o
    if ($campaign->unique_params && json_decode($campaign->unique_params)) {
        $paramsArray = json_decode($campaign->unique_params, true);
        if (is_array($paramsArray) && count($paramsArray) > 0) {
            $uniqueParam = array_keys($paramsArray)[0];
        }
    }
    
    // Use FB_OPEN e FB_CLOSE como marcadores temporários
    $params = "cwr={$campaign->id}"
        . "&tok={$campaign->token}"
        . "&{$uniqueParam}={$campaign->unique_id}"
        . "&utm_id=FB_OPENutm_idFB_CLOSE"
        . "&fbclid=FB_OPENfbclidFB_CLOSE"
        . "&cname=FB_OPENcampaign.nameFB_CLOSE"
        . "&domain=FB_OPENdomainFB_CLOSE"
        . "&placement=FB_OPENplacementFB_CLOSE"
        . "&adset=FB_OPENadset.nameFB_CLOSE"
        . "&adname=FB_OPENad.nameFB_CLOSE"
        . "&site=FB_OPENsite_source_nameFB_CLOSE"
        . "&xid={$campaign->xid}";
    
    // Substituir os marcadores temporários pelos do Facebook
    $params = str_replace(['FB_OPEN', 'FB_CLOSE'], ['{{', '}}'], $params);
    
    // Obter estatísticas para esta campanha 
    $last30Days = now()->subDays(30);

    $stats = [
        'total' => TrafficLog::where('campaign_id', $campaign->id)
            ->where('created_at', '>=', $last30Days)
            ->count(),

        'safe' => TrafficLog::where('campaign_id', $campaign->id)
            ->where('destination', 'safe')
            ->where('created_at', '>=', $last30Days)
            ->count(),

        'offer' => TrafficLog::where('campaign_id', $campaign->id)
            ->where('destination', 'offer')
            ->where('created_at', '>=', $last30Days)
            ->count(),
    ];
    
    // Obter logs recentes  
    $logs = TrafficLog::where('campaign_id', $campaign->id)
        ->where('created_at', '>=', now()->subDays(2))
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    
    return view('campaigns.show', compact('campaign', 'campaignUrl', 'params', 'stats', 'logs'));
}
    
    public function edit(Campaign $campaign)
    {
        $campaign->load('domain');
        
        return view('campaigns.edit', [
            'campaign' => $campaign,
            'domains' => Domain::where('verified', true)->get()
        ]);
    }
    
    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain_id' => 'required|exists:domains,id',
            'language' => 'required|string',
            'traffic_source' => 'required|string',
            'safe_url' => 'required|url',
            'safe_method' => 'required|in:ml_redirect,pre_page',
            'offer_url' => 'required|url',
            'offer_method' => 'required|in:redirect,twr_mirror',
            'countries' => 'required|array', 
            'devices' => 'required|array',
            'tags' => 'nullable|array',
        ]);
        
        $campaign->update([
            'name' => $validated['name'],
            'domain_id' => $validated['domain_id'],
            'language' => $validated['language'],
            'traffic_source' => $validated['traffic_source'],
            'safe_url' => $validated['safe_url'],
            'safe_method' => $validated['safe_method'],
            'offer_url' => $validated['offer_url'],
            'offer_method' => $validated['offer_method'],
            'countries' => json_encode($validated['countries']),
            'devices' => json_encode($validated['devices']),
            'tags' => $validated['tags'] ? json_encode($validated['tags']) : null,
            'is_active' => $request->has('is_active'),
        ]);
        
        return redirect()->route('campaigns.show', $campaign) 
            ->with('success', 'Campaign updated successfully');
    }
    
    public function destroy(Campaign $campaign)
{
    $campaign->trafficLogs()->delete();
    $campaign->delete();

    return redirect()->route('campaigns.index')
        ->with('success', 'Campaign deleted successfully');
}

public function prePageProceed(Request $request)
{
    $url = base64_decode($request->input('url'));

    if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
        return redirect()->to($url, 301);
    }

    return redirect('https://google.com'); // fallback 
}



} 