<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('avatar')->nullable();
            $table->string('phone')->nullable(); // <-- Kolom ini sudah ada di sini
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};