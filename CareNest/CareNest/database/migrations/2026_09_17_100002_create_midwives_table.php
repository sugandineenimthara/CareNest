<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('midwives', function (Blueprint $table) {
            $table->id('midwife_id');
            $table->string('midwife_name');
            $table->foreignId('area_id')->constrained('areas', 'area_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('midwives');
    }
};
