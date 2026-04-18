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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('users');
            $table->string('name'); // 顧客名
            $table->decimal('height', 5, 1); // 身長
            $table->tinyInteger('gender'); // 性別 1:男性 2:女性
            $table->date('birth_date'); //　生年月日
            $table->tinyInteger('pal_level'); // 1:低い 2:ふつう 3:高い
            $table->char('uuid', 36)->unique();
            $table->decimal('target_weight', 5, 2)->nullable(); //　目標体重
            $table->text('medical_notes')->nullable(); //　既往歴
            $table->string('line_user_id')->nullable(); //　LineユーザID
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
