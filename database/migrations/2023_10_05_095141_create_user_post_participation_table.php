<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_post_participation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained(
                table: 'posts',
                indexName: 'id'
            )->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained(
                table: 'users',
                indexName: 'participant_id'
            )->cascadeOnDelete();
            $table->boolean('is_completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_post_participation');
    }
};
