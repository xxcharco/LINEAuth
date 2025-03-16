<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // conditionsテーブルの修正
        Schema::table('conditions', function (Blueprint $table) {
            // 一時的にNULLABLEで追加
            $table->foreignId('user_id')
                ->after('id')
                ->nullable()
                ->constrained();
            $table->boolean('shared_with_partner')
                ->after('condition')
                ->default(false);
        });

        // menstruationsテーブルの修正
        Schema::table('menstruations', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->after('id')
                ->nullable()
                ->constrained();
            $table->enum('intensity', ['light', 'medium', 'heavy'])
                ->after('end_date')
                ->nullable();
            $table->json('symptoms')
                ->after('intensity')
                ->nullable();
        });
    }

    public function down()
    {
        Schema::table('conditions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'shared_with_partner']);
        });

        Schema::table('menstruations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'intensity', 'symptoms']);
        });
    }
};
