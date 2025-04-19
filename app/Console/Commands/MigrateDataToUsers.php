<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Domain;
use App\Models\Campaign;

class MigrateDataToUsers extends Command
{
    protected $signature = 'migrate:data-to-users {user_id? : ID do usuário que receberá os dados}';
    protected $description = 'Migra domínios e campanhas existentes para usuários';

    public function handle()
    {
        $this->info('Iniciando migração de dados para usuários...');
        
        // Verificar se há usuários no sistema
        $userCount = User::count();
        if ($userCount === 0) {
            $this->error('Nenhum usuário encontrado no sistema!');
            return 1;
        }
        
        // Determinar o usuário que receberá os dados
        $userId = $this->argument('user_id');
        
        if ($userId) {
            $admin = User::find($userId);
            if (!$admin) {
                $this->error("Usuário com ID {$userId} não encontrado!");
                return 1;
            }
        } else {
            // Se nenhum ID foi fornecido, listar usuários e pedir para escolher
            $users = User::select('id', 'name', 'email')->get();
            $this->info("Usuários disponíveis:");
            
            foreach ($users as $user) {
                $this->line("ID: {$user->id} - Nome: {$user->name} - Email: {$user->email}");
            }
            
            $userId = $this->ask('Digite o ID do usuário que receberá os dados:');
            $admin = User::find($userId);
            
            if (!$admin) {
                $this->error("Usuário com ID {$userId} não encontrado!");
                return 1;
            }
        }
        
        $this->info("Associando todos os domínios ao usuário: {$admin->name} (ID: {$admin->id})");
        
        // Atualizar domínios sem usuário associado
        $domains = Domain::whereNull('user_id')->get();
        $domainCount = $domains->count();
        
        if ($domainCount === 0) {
            $this->info("Nenhum domínio sem usuário encontrado.");
        } else {
            $bar = $this->output->createProgressBar($domainCount);
            $bar->start();
            
            foreach ($domains as $domain) {
                $domain->user_id = $admin->id;
                $domain->save();
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info("{$domainCount} domínios associados ao usuário {$admin->name}.");
        }
        
        // Atualizar campanhas sem usuário associado
        $campaigns = Campaign::whereNull('user_id')->get();
        $campaignCount = $campaigns->count();
        
        if ($campaignCount === 0) {
            $this->info("Nenhuma campanha sem usuário encontrada.");
        } else {
            $bar = $this->output->createProgressBar($campaignCount);
            $bar->start();
            
            foreach ($campaigns as $campaign) {
                $campaign->user_id = $admin->id;
                $campaign->save();
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info("{$campaignCount} campanhas associadas ao usuário {$admin->name}.");
        }
        
        $this->info('Migração de dados concluída!');
        return 0;
    }
}