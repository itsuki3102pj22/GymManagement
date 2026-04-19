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
        Schema::create('reservation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete(); // 顧客ID
            $table->foreignId('trainer_id')->constrained('users'); //　トレーナーID
            $table->dateTime('start_at'); 
            $table->dateTime('end_at');
            $table->tinyInteger('status')->default(0)->comment('0:仮予約, 1:確定, 2:キャンセル'); //　予約ステータス
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation');
    }
};
