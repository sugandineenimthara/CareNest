<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_health_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mother_id')->constrained('mothers', 'mother_id')->onDelete('cascade');
            $table->foreignId('midwife_id')->nullable()->constrained('midwives', 'midwife_id')->onDelete('set null');
            $table->string('diabetes')->nullable();
            $table->string('high_blood_pressure')->nullable();
            $table->string('blood_related_diseases')->nullable();
            $table->text('other')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_health_histories');
    }
};
