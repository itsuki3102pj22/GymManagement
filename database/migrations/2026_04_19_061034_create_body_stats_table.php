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
        Schema::create('body_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete(); //　顧客ID
            $table->decimal('weight', 5, 2)->comment('体重(kg)'); //　体重
            $table->decimal('body_fat_percentage', 4, 1)->nullable()->comment('体脂肪率(%)'); //　体脂肪率
            $table->decimal('muscle_mass', 5, 2)->nullable()->comment('筋肉量(kg)'); //　筋肉量
            $table->decimal('bmi', 4, 1)->nullable()->comment('BMI'); //　BMI
            $table->date('measured_at')->comment('測定日'); //　測定日
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('body_stats');
    }
};
