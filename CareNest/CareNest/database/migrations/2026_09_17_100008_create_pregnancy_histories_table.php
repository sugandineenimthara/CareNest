<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pregnancy_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mother_id')->constrained('mothers', 'mother_id')->onDelete('cascade');
            $table->integer('no_living_children')->default(0);
            $table->integer('age_of_youngest_child')->nullable();
            $table->date('last_menstrual_period')->nullable();
            $table->date('expected_date_of_delivery')->nullable();
            $table->date('date_confirmed_by_US')->nullable();
            $table->integer('no_of_weeks_pregnant_at_registration')->nullable();
            $table->date('first_fetal_movements_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pregnancy_histories');
    }
};
