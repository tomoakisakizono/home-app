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
        Schema::create('shopping_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pair_id')->constrained()->cascadeOnDelete(); // ペアごとのリスト
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 追加したユーザー
            $table->string('item_name'); // 買うもの
            $table->integer('quantity')->default(1); // 数量
            $table->enum('status', ['未購入', '購入済み'])->default('未購入'); // ステータス
            $table->string('category')->nullable(); // カテゴリ（食材・日用品など）
            $table->date('due_date')->nullable(); // 期限
            $table->text('note')->nullable(); // メモ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_lists');
    }
};
