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
        Schema::create('pairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user1_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user2_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('invite_code')->default('');
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending'); // 招待状況
            $table->timestamps();

            // ユーザーのペア関係をユニークにする
            $table->unique(['user1_id', 'user2_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pairs');
    }
};
