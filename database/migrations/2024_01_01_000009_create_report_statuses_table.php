<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->string('image')->nullable();
            $table->enum('status', ['delivered', 'in_process', 'completed', 'rejected']);
            $table->longText('description');
            $table->string('created_by_role')->nullable()->after('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_statuses');
    }
};