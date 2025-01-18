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
        Schema::create('menstruations', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');  // 生理開始日
            $table->date('end_date')->nullable();  // 生理終了日（nullableにして後から更新可能に）
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menstruations');
    }
};
