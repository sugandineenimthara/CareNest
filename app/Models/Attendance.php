<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'mother_id',
        'child_id',
        'midwife_id',
        'clinic_name',
        'clinic_type',
        'clinic_date',
        'remarks',
        'blood_pressure',
        'height',
        'weight',
        'bmi',
    ];

    protected $casts = [
        'clinic_date' => 'date',
        'height'      => 'decimal:2',
        'weight'      => 'decimal:2',
        'bmi'         => 'decimal:2',
    ];

    public function isHighBloodPressure(): bool
    {
        $bp = $this->blood_pressure ?: $this->remarks;
        if (!$bp) {
            return false;
        }
        if (preg_match('/(\d+)\s*[\/\-]\s*(\d+)/', $bp, $m)) {
            return ((int)$m[1] >= 140 || (int)$m[2] >= 90);
        }
        return false;
    }

    public function getAttendeeNameAttribute(): string
    {
        if ($this->clinic_type === 'pediatric') {
            return $this->child?->display_name ?? 'Child #' . $this->child_id;
        }
        return $this->mother?->mother_name ?? 'Mother #' . $this->mother_id;
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Mother::class, 'mother_id', 'mother_id');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id', 'child_id');
    }

    public function midwife(): BelongsTo
    {
        return $this->belongsTo(Midwife::class, 'midwife_id', 'midwife_id');
    }
}
