<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tests_done', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mother_id')->constrained('mothers', 'mother_id')->onDelete('cascade');
            $table->string('blood_group')->nullable();
            $table->string('blood_sugar')->nullable();
            $table->string('Haemoglobin')->nullable();
            $table->string('Albumin_test')->nullable();
            $table->string('urine_sugar_level')->nullable();
            $table->string('VDRL')->nullable();
            $table->string('HIV')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tests_done');
    }
};
