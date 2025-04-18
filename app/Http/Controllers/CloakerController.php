<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\TrafficLog;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Cache;
use App\Services\TrafficFilterService;
use Detection\MobileDetect;

class CloakerController extends Controller
{
    protected $filter;

    public function __construct(TrafficFilterService $filter)
    {
        $this->filter = $filter;
    }

    public function redirect(Request $request, $campaign_id)
{
    \Log::info('Iniciando redirecionamento', [
        'campaign_id' => $campaign_id, 
        'params' => $request->all()
    ]);

    $campaign = Campaign::find($campaign_id);

    if (!$campaign || !$campaign->is_active) {
        \Log::info('Campanha não encontrada ou inativa');
        return redirect('https://google.com');
    }

    // Verificação de token e unique_id
    $tok = $request->input('tok');
    $unique = collect($request->all())
        ->filter(fn($v, $k) => str_starts_with($k, '_') && $k !== 'tok')
        ->first();

    \Log::info('Verificando token e unique_id', [
        'token_recebido' => $tok,
        'token_esperado' => $campaign->token,
        'unique_recebido' => $unique,
        'unique_esperado' => $campaign->unique_id
    ]);

    // Verificação básica de token e unique_id
    // Verificação básica de token e unique_id 
    if ($tok !== $campaign->token || $unique !== $campaign->unique_id) {
        \Log::info('Falha na verificação de token/unique_id');
        
        // Registrar o tráfego antes de redirecionar
        $reason = '';
        if ($tok !== $campaign->token) {
            $reason = 'Invalid token';
        } 
        if ($unique !== $campaign->unique_id) {
            $reason = $reason ? $reason . ' and invalid unique ID' : 'Invalid unique ID';
        }
        
        $this->logTraffic($request, $campaign, 'safe', $reason);
        
        return redirect($campaign->safe_url);
    }

    // Verificação de parâmetros mínimos (apenas os essenciais)
    $requiredParams = ['cwr', 'xid', 'fbclid', 'placement'];
    $missingParams = [];
    
    foreach ($requiredParams as $param) {
        if (!$request->has($param) || empty($request->input($param))) {
            $missingParams[] = $param;
        }
    }
    
    if (!empty($missingParams)) {
        \Log::info('Parâmetros obrigatórios ausentes', ['missing' => $missingParams]);
        return redirect($campaign->safe_url);
    }

    // Verificação de correspondência de ID de campanha e CWR
    if ((int)$request->input('cwr') !== (int)$campaign->id) {
        \Log::info('ID de campanha não corresponde');
        return redirect($campaign->safe_url);
    }
    
    // Verificação rigorosa de XID - chave de segurança crucial
    $xid_recebido = $request->input('xid');
    $xid_esperado = $campaign->xid;
    
    \Log::info('Verificando XID', [
        'xid_recebido' => $xid_recebido,
        'xid_esperado' => $xid_esperado,
        'iguais' => ($xid_recebido === $xid_esperado)
    ]);
    
    if ($xid_recebido !== $xid_esperado) {
        \Log::info('XID não corresponde - Segurança violada');
        return redirect($campaign->safe_url);
    }

    // Análise completa de tráfego
    $destination = $this->analyzeTraffic($request, $campaign);
    
    \Log::info('Resultado da análise', [
        'destination_type' => $destination['type'],
        'reason' => $destination['reason']
    ]);

    // Registrar o tráfego
    $this->logTraffic($request, $campaign, $destination['type'], $destination['reason']);
    
    // Se for POST da PrePage, redireciona direto
    if ($request->isMethod('post')) {
        return redirect()->to(base64_decode($request->input('url')), 301);
    }

    // Se destino for SAFE e método for pre_page, renderiza a prepage
    if (
        $destination['type'] === 'safe' &&
        isset($destination['method']) &&
        $destination['method'] === 'pre_page'
    ) {
        $encoded_url = base64_encode($destination['url']);
        return view('prepage', [
            'encoded_url' => $encoded_url,
            'campaign' => $campaign
        ]);
    }

    // Para todos os outros casos, redireciona normalmente 
    return redirect()->to($destination['url'], 301);
}
    private function analyzeTraffic(Request $request, Campaign $campaign)
    {
        $ip = $request->header('X-Forwarded-For') ?? $request->ip();
        $userAgent = $request->header('User-Agent');
        $cacheKey = 'cloak:' . md5($ip . $userAgent . $campaign->id);
    
        // Limpar cache para testes mais confiáveis
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
    
        \Log::info('Analisando tráfego', [
            'ip' => $ip,
            'user_agent' => $userAgent,
            'params' => $request->all()
        ]);
    
        // 🚨 Verificação dos parâmetros essenciais
        $requiredParams = ['cwr', 'tok', 'fbclid', 'placement', 'xid'];
        $fbPlaceholders = [
            'fbclid' => '/\{\{fbclid\}\}/',
            'placement' => '/\{\{placement\}\}/',
        ];
        
        // Verificamos apenas CWR, TOK, FBCLID, PLACEMENT e XID
        foreach ($requiredParams as $param) {
            // Verifica se está presente
            if (!$request->has($param) || empty($request->input($param))) {
                \Log::info("Parâmetro obrigatório ausente: {$param}");
                return $this->cacheAndReturn($cacheKey, $campaign, 'Missing critical param: ' . $param);
            }
    
            // Verifica placeholders apenas para fbclid e placement
            if (isset($fbPlaceholders[$param])) {
                $value = $request->input($param);
                if (preg_match($fbPlaceholders[$param], $value)) {
                    \Log::info("Detectado placeholder não substituído em {$param}: {$value}");
                    return $this->cacheAndReturn($cacheKey, $campaign, 'Template placeholder in: ' . $param);
                }
            }
        }
    
        // Verificação de correspondência de ID de campanha e CWR
        if ((int)$request->input('cwr') !== (int)$campaign->id) {
            \Log::info('ID de campanha não corresponde');
            return $this->cacheAndReturn($cacheKey, $campaign, 'Campaign ID mismatch');
        }
        
        // Verificação rigorosa de XID - chave de segurança crucial
        $xid_recebido = $request->input('xid');
        $xid_esperado = $campaign->xid;
        
        \Log::info('Verificando XID', [
            'xid_recebido' => $xid_recebido,
            'xid_esperado' => $xid_esperado,
            'iguais' => ($xid_recebido === $xid_esperado)
        ]);
        
        if ($xid_recebido !== $xid_esperado) {
            \Log::info('XID não corresponde - Segurança violada');
            return $this->cacheAndReturn($cacheKey, $campaign, 'Invalid XID - security violation');
        }
    
        // Verificação do parâmetro único (começa com _)
        $uniqueParamFound = false;
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, '_') && $key !== 'tok') {
                $uniqueParamFound = true;
                // Verifica se o valor do parâmetro único corresponde ao esperado
                if ($value !== $campaign->unique_id) {
                    \Log::info('Parâmetro único não corresponde', [
                        'param' => $key,
                        'valor_recebido' => $value,
                        'valor_esperado' => $campaign->unique_id
                    ]);
                    return $this->cacheAndReturn($cacheKey, $campaign, 'Invalid unique parameter value');
                }
                break;
            }
        }
        
        if (!$uniqueParamFound) {
            \Log::info('Parâmetro único não encontrado');
            return $this->cacheAndReturn($cacheKey, $campaign, 'Missing unique parameter');
        }
    
        // 🚨 GEO por API externa
        $countryCode = Cache::remember("geoip_{$ip}", 60, function () use ($ip) {
            return $this->filter->getCountryCode($ip);
        });
        \Log::info('IP detectado: ' . $ip);
        \Log::info('Country detectado: ' . $countryCode);
    
        $allowedCountries = json_decode($campaign->countries, true);
        if (!$countryCode || $this->filter->isBlockedCountry($countryCode, $allowedCountries)) {
            return $this->cacheAndReturn($cacheKey, $campaign, 'Geo not allowed: ' . ($countryCode ?? 'unknown'));
        }
    
        // 🌐 LANG por navegador
        $lang = $this->filter->getLanguage($request);
        $blockedLangs = ['ru', 'cn', 'vi']; // você pode tornar isso dinâmico futuramente 
        if ($this->filter->isBlockedLanguage($lang, $blockedLangs)) {
            return $this->cacheAndReturn($cacheKey, $campaign, 'Blocked language: ' . $lang);
        }
    
        // 🚫 IP suspeito
        $suspiciousIPs = ['173.252.127.', '66.220.149.', '2a03:2880:', '216.58.194.', '172.217.', '69.171.251.'];
        foreach ($suspiciousIPs as $range) {
            if (str_starts_with($ip, $range)) {
                return $this->cacheAndReturn($cacheKey, $campaign, 'Suspicious IP: ' . $ip);
            }
        }

        // 🚫 Faixas CIDR da Meta (Facebook / Instagram / WhatsApp)
        $metaCIDRs = [
            '69.63.176.0/20',
            '69.171.224.0/19',
            '66.220.144.0/20',
            '129.134.0.0/16',
            '157.240.0.0/16',
            '173.252.64.0/18',
            '204.15.20.0/22',
            '31.13.24.0/21',
            '31.13.64.0/18',
            '185.60.216.0/22',
            '103.4.96.0/22',
            '45.64.40.0/22',
            '74.119.76.0/22',
        ];

        foreach ($metaCIDRs as $cidr) {
            if ($this->ipInCIDR($ip, $cidr)) {
                return $this->cacheAndReturn($cacheKey, $campaign, 'Meta CIDR blocked: ' . $cidr);
            }
        }

    
        // 🤖 Bot detection - versão melhorada  
        $botSignatures = ['bot', 'crawler', 'spider', 'lighthouse', 'slurp', 'googlebot', 'bingbot', 'yandex', 'baidu', 'facebookexternalhit', 'facebookcatalog', 'Facebot', 'metainspector'];
        foreach ($botSignatures as $sig) {
            if (stripos($userAgent, $sig) !== false) {
                return $this->cacheAndReturn($cacheKey, $campaign, 'Bot detected: ' . $sig);
            }
        }
    
        // 📱 Verificação de dispositivo - método aprimorado
        $detect = new MobileDetect();
        $detect->setUserAgent($request->header('User-Agent'));
    
        $deviceType = 'desktop';
        if ($detect->isTablet()) {
            $deviceType = 'tablet';
        } elseif ($detect->isMobile()) {
            $deviceType = 'mobile';
        }
    
        // Verificação de dispositivos permitidos
        $allowedDevices = json_decode($campaign->devices, true);
        if (!is_array($allowedDevices)) {
            $allowedDevices = ['desktop', 'mobile', 'tablet'];
        }
    
        \Log::info("Dispositivo detectado: {$deviceType}");
        \Log::info("Dispositivos permitidos: " . json_encode($allowedDevices));
    
        if (!in_array($deviceType, $allowedDevices)) {
            return $this->cacheAndReturn($cacheKey, $campaign, 'Device not allowed: ' . $deviceType);
        }
    
        // 🚫 Detecção de emuladores e browsers automatizados
        $suspiciousAgentPatterns = [
            'Headless', 'PhantomJS', 'Nightmare', 'Selenium', 'Puppeteer', 
            'webdriver', 'chromedriver', 'phantomjs', 'TOR', 'Tor Browser'
        ];
        
        foreach ($suspiciousAgentPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return $this->cacheAndReturn($cacheKey, $campaign, 'Suspicious browser/agent: ' . $pattern);
            }
        }
    
        // ✅ Se passou em tudo, agora pode liberar o tráfego para a offer   
        return $this->cacheAndReturn($cacheKey, $campaign, 'All checks passed', 'offer', $campaign->offer_url);
    }
    

    private function cacheAndReturn($cacheKey, Campaign $campaign, $reason, $type = 'safe', $url = null)
{
    $result = [
        'type' => $type,
        'url' => $url ?? $campaign->safe_url,
        'reason' => $reason,
        'method' => ($type === 'safe') ? $campaign->safe_method : $campaign->offer_method
    ];

    Cache::put($cacheKey, $result, now()->addMinutes(10));
    return $result;
}


private function logTraffic(Request $request, Campaign $campaign, $destination, $reason)
{
    \Log::info('Chamando logTraffic...');
    \Log::info('Destino: ' . $destination);
    \Log::info('Motivo: ' . $reason);

    try {
        $agent = new \Jenssegers\Agent\Agent();
        $ip = $request->ip();

        // Captura a URL completa da requisição
        $fullUrl = $request->fullUrl();

        // Substituindo geoip por ipwho.is
        $response = @file_get_contents("http://ipwho.is/{$ip}");
        $data = json_decode($response);
        $countryCode = $data->country_code ?? null;

        // Usando horário com fuso configurado no Laravel 
        $now = now();

        TrafficLog::insert([
            'campaign_id' => $campaign->id,
            'ip_address' => $ip,
            'country' => $countryCode, 
            'device_type' => $agent->deviceType(),
            'browser' => $agent->browser(),
            'user_agent' => $request->header('User-Agent'),
            'referrer' => $request->header('referer'),
            'destination' => $destination,
            'reason' => $reason,
            'request_url' => $fullUrl, // Novo campo com a URL completa
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    } catch (\Throwable $e) {
        \Log::error('Failed to log traffic: ' . $e->getMessage() . " Stack trace: " . $e->getTraceAsString());
    }
}

public function handleDomain(Request $request)
{
    $domain = $request->getHost();
    
    \Log::info("Host acessado: " . $domain);
    
    // Se estamos no domínio principal, vamos para o dashboard
    if ($domain === 'lightmoon.app' || $domain === 'www.lightmoon.app') {
        // Se o usuário estiver autenticado, vá para o dashboard
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        // Se não, vá para a página de login
        return redirect()->route('login');
    }
    
    // Caso contrário, procuramos uma campanha para este domínio
    $campaign = Campaign::where('domain_id', function($query) use ($domain) {
        $query->select('id')
              ->from('domains')
              ->where('name', $domain)
              ->first();
    })->first();
    
    // Se não encontrarmos uma campanha, tentamos buscar pelo nome de domínio diretamente
    if (!$campaign) {
        $domain = Domain::where('name', $domain)->first();
        
        if ($domain) {
            $campaign = Campaign::where('domain_id', $domain->id)->first();
        }
    }
    
    if (!$campaign) {
        \Log::warning("Nenhuma campanha encontrada para o domínio: " . $domain);
        return redirect('https://google.com');
    }
    
    // Redireciona para a rota de campanha
    return redirect()->route('campaign.redirect', [
        'campaign_id' => $campaign->id,
        'tok' => $campaign->token,
        '_param' => $campaign->unique_id,
    ]);
}

public function prePage($encoded_url)
{
    try {
        // Decodificar a URL segura
        $safe_url = base64_decode($encoded_url);
        
        // Verificar se a URL decodificada é válida
        if (!filter_var($safe_url, FILTER_VALIDATE_URL)) {
            // Em vez de redirecionar para o Google, retornamos uma página de erro
            return response()->view('errors.invalid_url', [], 404);
        }
        
        return view('prepage', [
            'encoded_url' => $encoded_url,
            'safe_url' => $safe_url
        ]);
    } catch (\Exception $e) {
        \Log::error('PrePage error: ' . $e->getMessage());
        return response()->view('errors.invalid_url', [], 404);
    }
}

public function prePageProceed(Request $request)
{
    try {
        $encoded_url = $request->input('url');
        $safe_url = base64_decode($encoded_url);
        
        // Verificar se a URL decodificada é válida
        if (!filter_var($safe_url, FILTER_VALIDATE_URL)) {
            return response()->view('errors.invalid_url', [], 404);
        }
        
        return redirect($safe_url);
    } catch (\Exception $e) {
        \Log::error('PrePageProceed error: ' . $e->getMessage());
        return response()->view('errors.invalid_url', [], 404);
    }
}

private function ipInCIDR($ip, $cidr)
{
    list($subnet, $mask) = explode('/', $cidr);
    return (ip2long($ip) & ~((1 << (32 - $mask)) - 1)) === ip2long($subnet);
}

 
}
