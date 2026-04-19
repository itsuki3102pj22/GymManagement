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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete(); // 顧客ID
            $table->string('stripe_payment_id'); //　決済ID
            $table->integer('amount')->comment('決済金額'); //　決済金額
            $table->string('currency', 10)->default('jpy'); //　通過
            $table->string('status', 50)->comment('successed/pending/failed'); //決済ステータス
            $table->dateTime('paid_at')->nullable(); //　支払い日時
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
