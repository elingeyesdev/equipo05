<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('simulacion_id');
            $table->timestamp('predicted_at')->nullable();
            $table->json('path')->nullable(); // array of lat/lng/time points
            $table->json('meta')->nullable(); // extra metadata like wind, confidence
            $table->timestamps();

            $table->foreign('simulacion_id')->references('id')->on('simulaciones')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
