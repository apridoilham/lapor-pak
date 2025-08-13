<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rw_id');
            $table->string('number', 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rts');
    }
};