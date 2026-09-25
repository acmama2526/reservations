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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->string('seat_name');                  // 席名 (例: カウンターA、テーブル1)
            $table->string('type');                       // 種類 (カウンター・テーブル・座敷)
            $table->unsignedInteger('capacity');          // 人数制限
            $table->boolean('is_active')->default(true);  // 利用状況 (有効/無効)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
