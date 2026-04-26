<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foco_tracks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('foco_incendio_id');
            $table->timestamp('recorded_at')->nullable();
            $table->json('coordinates'); // {lat:.., lng:..}
            $table->float('intensidad')->nullable();
            $table->timestamps();

            $table->foreign('foco_incendio_id')->references('id')->on('focos_incendios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foco_tracks');
    }
};
