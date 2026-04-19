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
        Schema::create('workout_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete(); // 顧客ID
            $table->foreignId('menu_id')->constrained('menus'); //　種目ID
            $table->decimal('weight', 5, 2)->comment('使用重量(kg)'); // 使用重量
            $table->integer('reps')->comment('回数'); //　回数
            $table->integer('sets')->comment('セット数'); //　セット数
            $table->tinyInteger('intensity')->comment('1:弱, 2:中, 3:強'); //　強度
            $table->decimal('total_volume', 8, 2)->comment('weight × reps × sets'); //　総負荷
            $table->text('condition_notes')->nullable()->comment('当日の体調など'); //　当日の体調など
            $table->date('logged_at')->comment('記録日'); //　記録日
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_logs');
    }
};
