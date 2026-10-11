<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->string('child_name')->nullable()->after('child_id');
            $table->string('gender')->nullable()->after('child_name');
            $table->decimal('birth_length', 5, 2)->nullable()->after('birth_weight');
            $table->boolean('bcg_vaccinated_at_birth')->default(false)->after('birth_length');
            $table->string('bcg_batch_no')->nullable()->after('bcg_vaccinated_at_birth');
            $table->date('bcg_vaccinated_date')->nullable()->after('bcg_batch_no');
            $table->foreignId('area_id')->nullable()->after('midwife_id')->constrained('areas', 'area_id')->onDelete('set null');
        });

        Schema::table('immunizations', function (Blueprint $table) {
            $table->unsignedBigInteger('child_id')->nullable()->change();
            $table->foreignId('mother_id')->nullable()->after('child_id')->constrained('mothers', 'mother_id')->onDelete('cascade');
            $table->string('status')->default('Completed')->after('vaccine_name');
            $table->text('remarks')->nullable()->after('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropColumn([
                'child_name',
                'gender',
                'birth_length',
                'bcg_vaccinated_at_birth',
                'bcg_batch_no',
                'bcg_vaccinated_date',
                'area_id',
            ]);
        });

        Schema::table('immunizations', function (Blueprint $table) {
            $table->dropForeign(['mother_id']);
            $table->dropColumn(['mother_id', 'status', 'remarks']);
        });
    }
};
