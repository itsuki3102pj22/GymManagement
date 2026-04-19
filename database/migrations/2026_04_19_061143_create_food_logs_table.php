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
        Schema::create('food_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete(); //　顧客ID
            $table->text('meal_text')->comment('LINEの食事記録'); //　Lineの食事記録
            $table->integer('total_calories')->nullable(); //　総カロリー
            $table->decimal('p_balance', 4, 1)->nullable()->comment('タンパク質量'); //　タンパク質量
            $table->decimal('f_balance', 4, 1)->nullable()->comment('脂質量'); //　脂質量
            $table->decimal('c_balance', 4, 1)->nullable()->comment('炭水化物量'); //　炭水化物量
            $table->date('logged_at')->comment('記録日'); //　記録日
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_logs');
    }
};
