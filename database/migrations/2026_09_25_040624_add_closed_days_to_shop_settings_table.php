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
        Schema::table('shop_settings', function (Blueprint $table) {
            // slot_minutes の後ろに json型の closed_days を追加（null許容）
            $table->json('closed_days')->nullable()->after('slot_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_settings', function (Blueprint $table) {
            // ロールバック時に closed_days カラムを削除
            $table->dropColumn('closed_days');
        });
    }
};
