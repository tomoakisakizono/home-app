<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // 追加：pair_id カラム（unsignedBigInteger型）
            $table->unsignedBigInteger('pair_id')->after('id');

            // 外部キー制約（pairsテーブルに対して）
            $table->foreign('pair_id')
                ->references('id')
                ->on('pairs')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // 外部キー制約を先に削除
            $table->dropForeign(['pair_id']);
            // カラム削除
            $table->dropColumn('pair_id');
        });
    }
};
