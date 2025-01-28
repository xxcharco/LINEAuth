<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;  // DB::statement用に追加

return new class extends Migration
{
    public function up(): void
    {
        // desire_levelの変更
        DB::statement('ALTER TABLE conditions MODIFY desire_level INT NOT NULL COMMENT "セックスしたい度（1: したくない, 2: ややしたくない, 3: ややしたい, 4: したい）"');

        // conditionの変更
        Schema::table('conditions', function (Blueprint $table) {
            // まず既存のカラムを削除
            $table->dropColumn('condition');
        });

        Schema::table('conditions', function (Blueprint $table) {
            // 新しい定義でカラムを追加
            $table->enum('condition', ['良い', 'やや良い', 'やや悪い', '悪い']);
        });
    }

    public function down(): void
    {
        // desire_levelを元に戻す
        DB::statement('ALTER TABLE conditions MODIFY desire_level INT NOT NULL COMMENT "セックスしたい度（1-5）"');

        // conditionを元に戻す
        Schema::table('conditions', function (Blueprint $table) {
            $table->dropColumn('condition');
        });

        Schema::table('conditions', function (Blueprint $table) {
            $table->enum('condition', ['良い', '普通', '悪い']);
        });
    }
};