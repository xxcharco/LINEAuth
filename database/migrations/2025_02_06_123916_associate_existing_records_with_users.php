<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 最初のユーザーを取得
        $user = DB::table('users')->first();
        
        if ($user) {
            // 既存のconditionsレコードを関連付け
            DB::table('conditions')
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);
                
            // 既存のmenstruationsレコードを関連付け
            DB::table('menstruations')
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);
        }

        // user_idをNOT NULL に変更
        Schema::table('conditions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });
        
        Schema::table('menstruations', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }

    public function down()
    {
        // ロールバック処理
    }
};
