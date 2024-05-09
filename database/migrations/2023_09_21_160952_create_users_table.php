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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique()->nullable(false);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('image')->nullable();
            $table->date('birthdate')->nullable();
            $table->text('biography')->nullable();
            $table->string('position')->nullable();
            $table->boolean('is_private')->default(false);
            $table->foreignId('badge_id')->default(1)->constrained(
                table: 'rewards',
                indexName: 'id'
            )->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};