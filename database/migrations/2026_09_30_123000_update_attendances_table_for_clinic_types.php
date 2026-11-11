<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            
            $table->unsignedBigInteger('mother_id')->nullable()->change();
            
            $table->string('clinic_type')->default('mother')->after('clinic_name');
            
            $table->text('remarks')->nullable()->after('clinic_date');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('mother_id')->nullable(false)->change();
            $table->dropColumn(['clinic_type', 'remarks']);
        });
    }
};
