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
        Schema::create('partnerships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user1_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user2_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('invitation_token')->nullable()->unique();
            $table->timestamp('invitation_sent_at')->nullable();
            $table->timestamp('matched_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // インデックスの追加
            $table->index('invitation_token');
            $table->index(['user1_id', 'user2_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partnerships');
    }
};
