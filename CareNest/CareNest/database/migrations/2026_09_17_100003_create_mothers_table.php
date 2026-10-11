<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mothers', function (Blueprint $table) {
            $table->id('mother_id');
            $table->string('mother_name');
            $table->string('phone_no')->nullable();
            $table->foreignId('midwife_id')->constrained('midwives', 'midwife_id')->onDelete('cascade');
            $table->decimal('height', 5, 2)->nullable(); // in cm or m
            $table->decimal('weight', 5, 2)->nullable(); // in kg
            $table->string('husband_name')->nullable();
            $table->string('husband_occupation')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mothers');
    }
};
