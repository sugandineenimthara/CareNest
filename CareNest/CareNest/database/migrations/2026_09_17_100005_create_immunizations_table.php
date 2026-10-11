<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('immunizations', function (Blueprint $table) {
            $table->id('immunization_id');
            $table->foreignId('child_id')->constrained('children', 'child_id')->onDelete('cascade');
            $table->foreignId('midwife_id')->constrained('midwives', 'midwife_id')->onDelete('cascade');
            $table->string('batch_no');
            $table->string('vaccine_name');
            $table->string('dose')->nullable();
            $table->string('age')->nullable();
            $table->date('immunization_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('immunizations');
    }
};
