// database/migrations/[timestamp]_modify_users_table_for_line_auth.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 既存のline_idカラムが存在する場合は削除
            if (Schema::hasColumn('users', 'line_id')) {
                $table->dropColumn('line_id');
            }
            
            // providerカラムが存在する場合は削除
            if (Schema::hasColumn('users', 'provider')) {
                $table->dropColumn('provider');
            }

            // 新しい認証関連カラムを追加
            $table->string('line_user_id')->nullable()->unique()->after('email');
            $table->string('line_access_token')->nullable()->after('line_user_id');
            $table->timestamp('line_token_expires_at')->nullable()->after('line_access_token');
            $table->string('line_refresh_token')->nullable()->after('line_token_expires_at');
            
            // インデックスの追加
            $table->index('line_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 追加したカラムを削除
            $table->dropIndex(['line_user_id']);
            $table->dropColumn([
                'line_user_id',
                'line_access_token',
                'line_token_expires_at',
                'line_refresh_token'
            ]);

            // 元のカラムを復元
            $table->string('line_id')->nullable();
            $table->string('provider')->nullable();
        });
    }
};