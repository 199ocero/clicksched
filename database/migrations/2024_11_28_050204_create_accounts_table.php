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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('sociable_id')->nullable()->default(null);
            $table->string('sociable_type')->nullable()->default(null);
            $table->string('platform');
            $table->string('name')->nullable()->default(null);
            $table->string('handle')->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->string('profile_image_url')->nullable()->default(null);
            $table->string('access_token')->nullable()->default(null);
            $table->string('refresh_token')->nullable()->default(null);
            $table->timestamp('token_expires_at')->nullable()->default(null);
            $table->timestamps();

            $table->unique(['user_id', 'sociable_id', 'sociable_type', 'handle']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
