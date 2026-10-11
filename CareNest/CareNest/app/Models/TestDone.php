<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestDone extends Model
{
    use HasFactory;

    protected $table = 'tests_done';

    protected $fillable = [
        'mother_id',
        'blood_group',
        'blood_sugar',
        'Haemoglobin',
        'Albumin_test',
        'urine_sugar_level',
        'VDRL',
        'HIV',
    ];

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Mother::class, 'mother_id', 'mother_id');
    }
}
