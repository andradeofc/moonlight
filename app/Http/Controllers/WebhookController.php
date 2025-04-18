<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{
    /**
     * Processa webhook de pagamento da PerfectPay
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handlePerfectPayWebhook(Request $request)
    {
        // Log da requisição para depuração
        Log::info('PerfectPay Webhook recebido', $request->all());
        
        $response = $request->all();
        
        // Iniciar transação para garantir integridade dos dados
        DB::beginTransaction();
        
        try {
            // Verificação do token de segurança (ajuste para o seu token)
            if ($request->input('token') != 'a17320512e1a36a95255e48906190360') {
                Log::warning('Token de webhook inválido', ['ip' => $request->ip()]);
                return response()->json(['status' => 'error', 'message' => 'Token inválido'], 403);
            }
            
            // Busca o plano associado
            $plan = Plan::where('perfect_pay_id', $response['plan']['code'])->first();
            
            if (!$plan) {
                Log::warning('Plano não encontrado no webhook', [
                    'perfect_pay_id' => $response['plan']['code'] ?? 'null'
                ]);
                return response()->json(['status' => 'error', 'message' => 'Plano não encontrado'], 404);
            }
            
            // Busca o usuário pelo email
            $user = User::where('email', $response['customer']['email'])->first();
            
            if ($user) {
                // Usuário existente
                if ($response['sale_status_enum_key'] == 'approved') {
                    // Atualiza o status do plano do usuário
                    $user->update([
                        'is_active' => true,
                        'current_plan_id' => $plan->id,
                        'subscription_id' => $response['code'] ?? '',
                    ]);
                    
                    // Se o usuário já tem uma data de expiração, adiciona mais 30 dias
                    // Caso contrário, define a data de expiração como 30 dias a partir de hoje
                    if (empty($user->expiry)) {
                        $expiry = now()->addDays(30)->toDateString();
                    } else {
                        $expiry = \Carbon\Carbon::parse($user->expiry)->addDays(30)->toDateString();
                    }
                    
                    $user->update([
                        'plan_expires_at' => $expiry
                    ]);
                    
                    // Registrar histórico de pagamento
                    PaymentHistory::create([
                        'user_id' => $user->id,
                        'status' => $response['sale_status_enum_key'],
                        'amount' => $response['sale_amount'] ?? 0,
                        'provider' => 'perfectpay',
                        'transaction_id' => $response['code'] ?? '',
                        'data' => json_encode($response)
                    ]);
                    
                    // Enviar email (opcional)
                    // Mail::to($response['customer']['email'])->send(new \App\Mail\PlanUpdatedMail());
                    
                } else if (in_array($response['sale_status_enum_key'], ['charged_back', 'refunded', 'canceled'])) {
                    // Desativa o plano do usuário em caso de estorno/reembolso
                    $user->update([
                        'is_active' => false,
                        'plan_expires_at' => now()->toDateString()
                    ]);
                    
                    // Registrar histórico de pagamento
                    PaymentHistory::create([
                        'user_id' => $user->id,
                        'status' => $response['sale_status_enum_key'],
                        'amount' => $response['sale_amount'] ?? 0,
                        'provider' => 'perfectpay',
                        'transaction_id' => $response['code'] ?? '',
                        'data' => json_encode($response)
                    ]);
                }
            } else {
                // Usuário não existe, criar novo usuário apenas se a venda for aprovada
                if ($response['sale_status_enum_key'] == 'approved') {
                    // Gera senha aleatória para o novo usuário
                    $randomPassword = Str::random(10);
                    
                    // Cria o novo usuário
                    $user = User::create([
                        'name' => $response['customer']['full_name'] ?? '',
                        'surname' => '',  // Extrair sobrenome se possível
                        'email' => $response['customer']['email'],
                        'phone' => $response['customer']['phone'] ?? '',
                        'password' => bcrypt($randomPassword),
                        'is_active' => true,
                        'current_plan_id' => $plan->id,
                        'plan_expires_at' => now()->addDays(30),
                        'subscription_id' => $response['code'] ?? '',
                    ]);
                    
                    // Registrar histórico de pagamento
                    PaymentHistory::create([
                        'user_id' => $user->id,
                        'status' => $response['sale_status_enum_key'],
                        'amount' => $response['sale_amount'] ?? 0,
                        'provider' => 'perfectpay',
                        'transaction_id' => $response['code'] ?? '',
                        'data' => json_encode($response)
                    ]);
                    
                    // Enviar email com as credenciais
                    $mailData = [
                        'name' => $response['customer']['full_name'] ?? '',
                        'email' => $response['customer']['email'],
                        'password' => $randomPassword,
                    ];
                    
                    // Mail::to($response['customer']['email'])->send(new \App\Mail\NewUserRegisterEmail($mailData));
                }
            }
            
            DB::commit();
            return response()->json(['status' => 'success']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao processar webhook', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['status' => 'error', 'message' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }
}