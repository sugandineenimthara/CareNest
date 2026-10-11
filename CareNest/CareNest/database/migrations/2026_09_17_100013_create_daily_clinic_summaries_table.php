<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_clinic_summaries', function (Blueprint $table) {
            $table->foreignId('report_id')->primary()->constrained('reports', 'report_id')->onDelete('cascade');
            $table->foreignId('midwife_id')->constrained('midwives', 'midwife_id')->onDelete('cascade');
            $table->foreignId('area_id')->constrained('areas', 'area_id')->onDelete('cascade');
            $table->string('batch_no');
            $table->date('date');
            $table->string('vaccine_used');
            $table->integer('opening_stock')->default(0);
            $table->integer('closing_stock')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_clinic_summaries');
    }
};
