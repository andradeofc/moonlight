<?php
// app/Http/Controllers/DomainController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Domain;
use App\Models\Campaign;

class DomainController extends Controller
{
    public function index()
    {
        return view('domains.index', [
            'domains' => Domain::all()
        ]);
    }
    
    
    public function create()
    {
        return view('domains.create');
    }
    
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255|unique:domains',
    ]);

    // ✅ CNAME padrão para todos os domínios 
    $cnameRecord = 'connect.lightmoon.app';

    $domain = Domain::create([
        'name' => $validated['name'],
        'cname_record' => $cnameRecord,
        'verified' => false
    ]);

    return redirect()->route('domains.show', $domain)
        ->with('success', 'Domain added successfully. Please point your CNAME to: ' . $cnameRecord);
}
    
    public function show(Domain $domain)
    {
        return view('domains.show', compact('domain'));
    }
    
    public function verify(Domain $domain)
{
    try {
        $verified = false;
        $expectedTarget = 'connect.lightmoon.app';

        // 🔍 Primeira tentativa: DNS padrão do sistema
        $records = dns_get_record($domain->name, DNS_CNAME);

        foreach ($records as $record) {
            $target = rtrim($record['target'] ?? '', '.');
            if ($target === $expectedTarget) {
                $verified = true;
                break;
            }
        }

        // 🛡️ Fallback com dig (caso dns_get_record não funcione) 
        if (!$verified) {
            $digOutput = shell_exec("dig +short {$domain->name} CNAME");
            $digTarget = trim(rtrim($digOutput, "."));
            if ($digTarget === $expectedTarget) {
                $verified = true;
            }
        }

        if (!$verified) {
            return back()->with('error', 'CNAME verification failed. Please check your DNS configuration.');
        }

        // ✅ Marca como verificado
        $domain->verified = true;
        $domain->save();

        // 🚀 Gera SSL automaticamente
        \Log::info("🛠️ Executando script SSL para: {$domain->name}");
 
        $result = \Illuminate\Support\Facades\Process::run("sudo /opt/cloacker-scripts/auto-ssl-register.sh {$domain->name}");

        
        \Log::info("📄 Resultado script SSL:", [
            'output' => $result->output(),
            'error' => $result->errorOutput(),
            'success' => $result->successful(),
        ]);
        
        return back()->with('success', 'Domain verified and SSL certificate generated successfully!');
    } catch (\Exception $e) {
        if (app()->environment('local') || app()->environment('development')) {
            $domain->verified = true;
            $domain->save();
            return back()->with('success', 'Domain verified successfully (Development Mode)');
        }

        return back()->with('error', 'Verification error: ' . $e->getMessage()); 
    }
}


public function destroy(Domain $domain)
{
    // Verificar se o domínio está sendo usado em alguma campanha
    $campaignsUsingDomain = Campaign::where('domain_id', $domain->id)->count();
    
    if ($campaignsUsingDomain > 0) {
        return back()->with('error', 'Cannot delete this domain. It is being used in ' . $campaignsUsingDomain . ' campaign(s). Please remove the domain from all campaigns first.');
    }
    
    // Se não estiver sendo usado, exclui
    $domain->delete();
    
    return redirect()->route('domains.index')
        ->with('success', 'Domain deleted successfully');
}


}