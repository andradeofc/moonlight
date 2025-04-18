<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plans = [
            [
                'name' => 'Plano Básico',
                'description' => 'O plano mais competitivo e popular com restrições de cliques e domínios registrados.',
                'price' => 97.00,
                'clicks' => 20000,
                'domains' => 3,
                'extra_clicks_price' => 0.01,
                'traffic_sources' => ['Facebook'],
                'is_active' => true,
                'payment_url' => 'https://checkout.perfectpay.com.br/pay/PPU38CNEOP3',
                'perfect_pay_id' => 'PPU38CNEOP3'
            ],
            [
                'name' => 'Plano TikTok',
                'description' => 'O plano mais competitivo e popular com restrições de cliques e domínios registrados, agora com TikTok.',
                'price' => 97.00,
                'clicks' => 20000,
                'domains' => 3,
                'extra_clicks_price' => 0.01,
                'traffic_sources' => ['TikTok'],
                'is_active' => true,
                'payment_url' => 'https://checkout.perfectpay.com.br/pay/PPU38CNEO4K',
                'perfect_pay_id' => 'PPU38CNEO4K'
            ],
            [
                'name' => 'Plano PRO',
                'description' => 'O plano PRO foi projetado para atender empresas com um grande número de serviços.',
                'price' => 297.00,
                'clicks' => 100000,
                'domains' => 10,
                'extra_clicks_price' => 0.004,
                'traffic_sources' => ['Facebook', 'Google', 'TikTok', 'Kwai', 'MGID', 'Snapchat', 'Microsoft', 'SMS'],
                'is_active' => true,
                'payment_url' => 'https://checkout.perfectpay.com.br/pay/PPU38CNEO5F',
                'perfect_pay_id' => 'PPU38CNEO5F'
            ],
            [
                'name' => 'Plano Freedom',
                'description' => 'Nosso melhor plano para atender empresas com muitos acessos e com vários domínios.',
                'price' => 497.00,
                'clicks' => 300000,
                'domains' => 20,
                'extra_clicks_price' => 0.002,
                'traffic_sources' => [
                    'Facebook', 'Google', 'TikTok', 'Kwai', 'MGID', 'Outbrain', 'Taboola', 
                    'Revcontent', 'Snapchat', 'Microsoft', 'Traffic-Factory', 'SMS', 
                    'Twitter - X', 'Propeller Ads', 'MediaGo'
                ],
                'is_active' => true,
                'payment_url' => 'https://checkout.perfectpay.com.br/pay/PPU38CNEO6J',
                'perfect_pay_id' => 'PPU38CNEO6J'
            ],
            [
                'name' => 'Plano Enterprise',
                'description' => 'Enterprise Plan Conquest',
                'price' => 997.00,
                'clicks' => 1000000,
                'domains' => 25,
                'extra_clicks_price' => 0.001,
                'traffic_sources' => [
                    'Facebook', 'Google', 'TikTok', 'Kwai', 'MGID', 'Outbrain', 'Taboola', 
                    'Revcontent', 'Snapchat', 'Microsoft', 'Traffic-Factory', 'SMS', 
                    'Twitter - X', 'Propeller Ads', 'MediaGo'
                ],
                'is_active' => true,
                'payment_url' => 'https://checkout.perfectpay.com.br/pay/PPU38CNEO7N',
                'perfect_pay_id' => 'PPU38CNEO7N'
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}