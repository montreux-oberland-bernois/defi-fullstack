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
        Schema::create('train_routes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('from_station_id')->constrained('stations')->onDelete('cascade');
            $table->foreignId('to_station_id')->constrained('stations')->onDelete('cascade');
            $table->string('analytic_code', 50);
            $table->decimal('distance_km', 10, 2);
            $table->json('path');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('analytic_code');
            $table->index(['analytic_code', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('train_routes');
    }
};
