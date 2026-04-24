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
        Schema::table('food_logs', function (Blueprint $table) {
            $table->decimal('protein_grams', 7, 1)->nullable()->after('c_balance')->comment('タンパク質（グラム）');
            $table->decimal('fat_grams', 7, 1)->nullable()->after('protein_grams')->comment('脂質（グラム）');
            $table->decimal('carbs_grams', 7, 1)->nullable()->after('fat_grams')->comment('炭水化物（グラム）');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_logs', function (Blueprint $table) {
            $table->dropColumn(['protein_grams', 'fat_grams', 'carbs_grams']);
        });
    }
};
