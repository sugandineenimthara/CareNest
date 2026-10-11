<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->foreignId('midwife_id')->constrained('midwives', 'midwife_id')->onDelete('cascade');
            $table->string('batch_no');
            $table->date('date');
            $table->string('vaccine_used');
            $table->string('dose')->nullable();
            $table->integer('no_of_vaccine_performed')->default(0);
            $table->integer('items_received')->default(0);
            $table->integer('items_returned')->default(0);
            $table->integer('opening_stock')->default(0);
            $table->integer('closing_stock')->default(0);
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
