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
        Schema::table('partnerships', function (Blueprint $table) {
            // auth_codeカラムを追加（invitation_tokenの後ろに）
            $table->string('auth_code', 64)
                ->nullable()
                ->after('invitation_token');
            
            // auth_codeのインデックスも追加
            $table->index('auth_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partnerships', function (Blueprint $table) {
            // インデックスを削除
            $table->dropIndex(['auth_code']);
            
            // カラムを削除
            $table->dropColumn('auth_code');
        });
    }
};