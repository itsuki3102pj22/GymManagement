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
        Schema::create('food_master', function (Blueprint $table) {
            $table->id();
            $table->string('food_name')->comment('料理名・食品名'); //　両地名・食品名
            $table->integer('calories')->comment('カロリー(kcal)'); //　カロリー
            $table->decimal('protein', 5, 1)->comment('タンパク質(g)'); //　タンパク質
            $table->decimal('fat', 5, 1)->comment('脂質(g)'); //　脂質
            $table->decimal('carb', 5, 1)->comment('炭水化物(g)'); //　炭水化物
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_master');
    }
};
