<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('previous_pregnancy_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mother_id')->constrained('mothers', 'mother_id')->onDelete('cascade');
            $table->date('date_of_birth')->nullable();
            $table->decimal('birth_weight', 4, 2)->nullable();
            $table->enum('gender', ['Male', 'Female'])->nullable();
            $table->string('which_pregnancy')->nullable();
            $table->string('results')->nullable();
            $table->string('place_of_pregnancy')->nullable();
            $table->boolean('rubella_vaccinated')->default(false);
            $table->boolean('folic_acid_vaccinated')->default(false);
            $table->boolean('infertility')->default(false);
            $table->boolean('blood_relation_marriage')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('previous_pregnancy_histories');
    }
};
