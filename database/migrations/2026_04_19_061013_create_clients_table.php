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
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete(); // トレーナーID
            $table->string('name'); //　顧客名
            $table->decimal('height', 5, 1)->comment('身長(cm)'); //　身長
            $table->tinyInteger('gender')->comment('1:男性, 2:女性'); //　性別
            $table->date('birth_date'); //　生年月日
            $table->tinyInteger('pal_level')->comment('1:低, 2:中, 3:高'); //　身体活動レベル
            $table->char('uuid', 36)->unique(); //　UUID
            $table->decimal('target_weight', 5, 2)->nullable()->comment('目標体重(kg)'); //　目標体重
            $table->text('medical_notes')->nullable()->comment('既往歴・アレルギー等'); //　既往歴・アレルギー等
            $table->string('line_user_id')->nullable(); //　LineユーザID
            $table->boolean('is_active')->default(true); //　アクティブフラグ
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
