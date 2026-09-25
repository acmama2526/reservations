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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');   // 氏名
            $table->unsignedInteger('people'); // 人数
            $table->date('reservation_date');  // 日付
            $table->time('start_time');        // 利用開始時間
            $table->time('end_time');          // 利用終了時間
            $table->string('phone');           // ご連絡先
            $table->string('status')->default('temporary'); // 予約状況
            $table->text('description')->nullable();  // メモ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
