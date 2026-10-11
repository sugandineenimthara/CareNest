<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('triposha_books', function (Blueprint $table) {
            $table->id('serial_no');
            $table->foreignId('mother_id')->constrained('mothers', 'mother_id')->onDelete('cascade');
            $table->foreignId('child_id')->nullable()->constrained('children', 'child_id')->onDelete('cascade');
            $table->foreignId('midwife_id')->constrained('midwives', 'midwife_id')->onDelete('cascade');
            $table->integer('no_of_packets')->default(1);
            $table->date('issuing_date');
            $table->enum('recipient_type', ['Mother', 'Child'])->default('Mother');
            $table->string('name_of_mother')->nullable();
            $table->string('name_of_child')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('triposha_books');
    }
};
