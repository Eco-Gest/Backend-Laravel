<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();     
            $table->foreignId('category_id')->constrained(
                table: 'categories',
                indexName: 'id'
            )->cascadeOnDelete();
            $table->foreignId('author_id')->constrained(
                table: 'users',
                indexName: 'post_user_id'
            )->cascadeOnDelete();
            $table->string('tag')->nullable();
            $table->string('title')->nullable();;
            $table->text('description')->nullable();;
            $table->string('image')->nullable();;
            $table->string('position')->nullable();;
            $table->enum('type', ['action', 'challenge']);
            $table->enum('level', ['easy', 'medium', 'hard']);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE posts ALTER COLUMN tag TYPE text[] USING ARRAY[tag]');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
