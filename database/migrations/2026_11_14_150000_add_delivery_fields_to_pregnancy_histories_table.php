<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pregnancy_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('pregnancy_histories', 'is_delivered')) {
                $table->boolean('is_delivered')->default(false)->after('first_fetal_movements_date');
            }
            if (!Schema::hasColumn('pregnancy_histories', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('is_delivered');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pregnancy_histories', function (Blueprint $table) {
            if (Schema::hasColumn('pregnancy_histories', 'delivery_date')) {
                $table->dropColumn('delivery_date');
            }
            if (Schema::hasColumn('pregnancy_histories', 'is_delivered')) {
                $table->dropColumn('is_delivered');
            }
        });
    }
};
