<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('simulaciones', function (Blueprint $table) {
            // User who created the simulation
            $table->unsignedBigInteger('creator_id')->nullable()->after('id');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
        });

        // Pivot table to assign volunteers (users) to simulations
        Schema::create('simulacion_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('simulacion_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->foreign('simulacion_id')->references('id')->on('simulaciones')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['simulacion_id','user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('simulaciones', function (Blueprint $table) {
            $table->dropForeign(['creator_id']);
            $table->dropColumn('creator_id');
        });

        Schema::dropIfExists('simulacion_user');
    }
};
