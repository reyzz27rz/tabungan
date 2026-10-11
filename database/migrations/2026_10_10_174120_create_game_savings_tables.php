<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Memperluas tabel users bawaan Laravel
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('total_savings', 12, 2)->default(0);
            $table->integer('points')->default(0);
            $table->integer('level')->default(1);
            $table->integer('current_xp')->default(0);
        });

        // Tabel Pet Pengguna
        Schema::create('user_pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('pet_name')->default('Mimi (Telur)');
            $table->integer('evolution_stage')->default(1); // 1: Telur, 2: Bayi, 3: Dewasa
            $table->integer('pet_xp')->default(0);
            $table->timestamps();
        });

        // Tabel Riwayat Menabung
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings');
        Schema::dropIfExists('user_pets');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['total_savings', 'points', 'level', 'current_xp']);
        });
    }
};