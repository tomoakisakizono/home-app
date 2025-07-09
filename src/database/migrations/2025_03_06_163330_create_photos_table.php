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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pair_id')->constrained('pairs')->onDelete('cascade'); // ペアIDで管理
            // $table->string('image_path'); // 画像ファイルのパス
            $table->text('comment')->nullable(); // コメント
            $table->date('photo_date'); // 写真の日付
            $table->string('category'); // カテゴリ（例：子育て、ご飯）
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
