<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('travel_requests', function (Blueprint $table) {
            $table->index('status');
            $table->index('destino');
            $table->index('data_ida');
            $table->index('data_volta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_requests', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['destino']);
            $table->dropIndex(['data_ida']);
            $table->dropIndex(['data_volta']);
        });
    }
};
