<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('triposha_books', function (Blueprint $table) {
            // Make mother_id nullable so child-only records can exist
            $table->unsignedBigInteger('mother_id')->nullable()->change();
            // Add opening stock for the day's distribution session
            $table->integer('opening_stock')->nullable()->after('recipient_type')
                  ->comment('Packets in storage at start of distribution session');
            // Group records by distribution session (date + midwife batch)
            $table->string('session_label')->nullable()->after('opening_stock')
                  ->comment('e.g. "2026-09-30 Morning Session"');
        });
    }

    public function down(): void
    {
        Schema::table('triposha_books', function (Blueprint $table) {
            $table->unsignedBigInteger('mother_id')->nullable(false)->change();
            $table->dropColumn(['opening_stock', 'session_label']);
        });
    }
};
