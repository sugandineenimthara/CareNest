<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mothers', function (Blueprint $table) {
            $table->string('blood_pressure')->nullable()->after('weight');
        });

        Schema::table('children', function (Blueprint $table) {
            $table->decimal('height', 5, 2)->nullable()->after('birth_length'); 
            $table->decimal('weight', 5, 2)->nullable()->after('height');       
            $table->decimal('bmi', 5, 2)->nullable()->after('weight');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->string('blood_pressure')->nullable()->after('remarks');
            $table->decimal('height', 5, 2)->nullable()->after('blood_pressure');
            $table->decimal('weight', 5, 2)->nullable()->after('height');
            $table->decimal('bmi', 5, 2)->nullable()->after('weight');
        });
    }

    public function down(): void
    {
        Schema::table('mothers', function (Blueprint $table) {
            $table->dropColumn(['blood_pressure']);
        });

        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn(['height', 'weight', 'bmi']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['blood_pressure', 'height', 'weight', 'bmi']);
        });
    }
};
