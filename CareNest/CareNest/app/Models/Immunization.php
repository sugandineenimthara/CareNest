<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Immunization extends Model
{
    use HasFactory;

    protected $primaryKey = 'immunization_id';

    protected $fillable = [
        'child_id',
        'midwife_id',
        'batch_no',
        'vaccine_name',
        'dose',
        'age',
        'immunization_date',
        'expiry_date',
    ];

    protected $casts = [
        'immunization_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id', 'child_id');
    }

    public function midwife(): BelongsTo
    {
        return $this->belongsTo(Midwife::class, 'midwife_id', 'midwife_id');
    }
}
