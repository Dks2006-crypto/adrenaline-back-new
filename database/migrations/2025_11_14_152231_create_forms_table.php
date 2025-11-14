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
    Schema::create('forms', function (Blueprint $table) {
        $table->id();
        $table->foreignId('service_id')->constrained()->onDelete('cascade');
        $table->foreignId('trainer_id')->nullable()->constrained()->onDelete('set null');
        $table->foreignId('branch_id')->constrained()->onDelete('cascade');
        $table->dateTime('starts_at');
        $table->dateTime('ends_at');
        $table->integer('capacity');
        $table->string('recurrence_rule')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
