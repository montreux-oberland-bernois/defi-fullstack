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
        Schema::create('distances', function (Blueprint $table) {
            $table->id();
            $table->string('line_name', 50);
            $table->foreignId('parent_station_id')->constrained('stations')->onDelete('cascade');
            $table->foreignId('child_station_id')->constrained('stations')->onDelete('cascade');
            $table->decimal('distance_km', 10, 2);
            $table->timestamps();

            $table->unique(['parent_station_id', 'child_station_id', 'line_name']);
            $table->index(['parent_station_id', 'child_station_id']);
            $table->index('line_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distances');
    }
};
