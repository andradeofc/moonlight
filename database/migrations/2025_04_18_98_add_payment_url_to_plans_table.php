// Migração para adicionar payment_url à tabela plans
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('payment_url')->nullable()->after('is_active');
            $table->string('perfect_pay_id')->nullable()->after('payment_url');
        });
    }

    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['payment_url', 'perfect_pay_id']);
        });
    }
};
