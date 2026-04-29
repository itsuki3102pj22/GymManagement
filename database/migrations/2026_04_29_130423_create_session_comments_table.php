<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('users');
            $table->date('session_date');
            $table->text('comment');
            $table->boolean('is_public')->default(false)
                  ->comment('trueなら公開URLにも表示');
            $table->json('badges')->nullable()
                  ->comment('達成バッジ配列');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_comments');
    }
};