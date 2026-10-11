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
        'clinic_date',
    ];

    protected $casts = [
        'clinic_date' => 'date',
    ];

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
