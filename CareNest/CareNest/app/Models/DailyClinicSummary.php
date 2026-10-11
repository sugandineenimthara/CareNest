<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyClinicSummary extends Model
{
    use HasFactory;

    protected $primaryKey = 'report_id';
    public $incrementing = false;

    protected $fillable = [
        'report_id',
        'midwife_id',
        'area_id',
        'batch_no',
        'date',
        'vaccine_used',
        'opening_stock',
        'closing_stock',
    ];

    protected $casts = [
        'date' => 'date',
        'opening_stock' => 'integer',
        'closing_stock' => 'integer',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'report_id', 'report_id');
    }

    public function midwife(): BelongsTo
    {
        return $this->belongsTo(Midwife::class, 'midwife_id', 'midwife_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id', 'area_id');
    }
}
