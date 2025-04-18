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
    Schema::create('campaigns', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->foreignId('domain_id')->constrained();
        $table->string('language');
        $table->string('traffic_source');
        $table->string('safe_url');
        $table->string('safe_method')->default('twr_redirect');
        $table->string('offer_url');
        $table->string('offer_method')->default('redirect');
        $table->json('countries');
        $table->json('devices');
        $table->string('token')->unique();
        $table->string('unique_id')->unique();
        $table->json('tags')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
