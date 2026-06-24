<?php

use App\Support\UnifiedPostgres;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = UnifiedPostgres::enabled() ? 'core' : config('database.default');

        if (Schema::connection($connection)->hasTable('personal_access_tokens')) {
            return;
        }

        Schema::connection($connection)->create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $connection = UnifiedPostgres::enabled() ? 'core' : config('database.default');
        Schema::connection($connection)->dropIfExists('personal_access_tokens');
    }
};
