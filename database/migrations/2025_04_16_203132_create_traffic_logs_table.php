<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('traffic_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('campaign_id')->constrained();
        $table->string('ip_address');
        $table->string('country')->nullable();
        $table->string('device_type')->nullable();
        $table->string('browser')->nullable();
        $table->string('user_agent');
        $table->string('referrer')->nullable();
        $table->string('destination')->comment('safe ou offer');
        $table->text('reason')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traffic_logs');
    }
};
