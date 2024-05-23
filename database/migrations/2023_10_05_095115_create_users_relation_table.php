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
        Schema::create('users_relation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained(
                table: 'users',
                indexName: 'follower_id'
            )->cascadeOnDelete();
            $table->foreignId('following_id')->constrained(
                table: 'users',
                indexName: 'following_id'
            )->cascadeOnDelete();
            $table->enum('status', ['approved', 'pending', 'denied', 'blocked']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};