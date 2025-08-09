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
        Schema::table('rts', function (Blueprint $table) {
            // Menambahkan foreign key constraint di sini
            $table->foreign('rw_id')->references('id')->on('rws')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rts', function (Blueprint $table) {
            $table->dropForeign(['rw_id']);
        });
    }
};