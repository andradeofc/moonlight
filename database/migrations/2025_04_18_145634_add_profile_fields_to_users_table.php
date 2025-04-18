<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('surname')->nullable()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->boolean('is_active')->default(false)->after('remember_token');
            $table->unsignedBigInteger('current_plan_id')->nullable()->after('is_active');
            $table->timestamp('plan_expires_at')->nullable()->after('current_plan_id');
            $table->string('subscription_id')->nullable()->after('plan_expires_at');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'surname', 
                'phone', 
                'is_active', 
                'current_plan_id', 
                'plan_expires_at',
                'subscription_id'
            ]);
        });
    }
};