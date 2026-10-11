<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyHealthHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'mother_id',
        'midwife_id',
        'diabetes',
        'high_blood_pressure',
        'blood_related_diseases',
        'other',
    ];

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Mother::class, 'mother_id', 'mother_id');
    }

    public function midwife(): BelongsTo
    {
        return $this->belongsTo(Midwife::class, 'midwife_id', 'midwife_id');
    }
}
