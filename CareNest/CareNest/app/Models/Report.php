<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Report extends Model
{
    use HasFactory;

    protected $primaryKey = 'report_id';

    protected $fillable = [
        'midwife_id',
        'batch_no',
        'date',
        'vaccine_used',
        'dose',
        'no_of_vaccine_performed',
        'items_received',
        'items_returned',
        'opening_stock',
        'closing_stock',
        'expiry_date',
    ];

    protected $casts = [
        'date' => 'date',
        'expiry_date' => 'date',
        'no_of_vaccine_performed' => 'integer',
        'items_received' => 'integer',
        'items_returned' => 'integer',
        'opening_stock' => 'integer',
        'closing_stock' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Report $report) {
            $report->closing_stock = $report->opening_stock 
                + $report->items_received 
                - $report->items_returned 
                - $report->no_of_vaccine_performed;
        });
    }

    public function midwife(): BelongsTo
    {
        return $this->belongsTo(Midwife::class, 'midwife_id', 'midwife_id');
    }

    public function dailyClinicSummary(): HasOne
    {
        return $this->hasOne(DailyClinicSummary::class, 'report_id', 'report_id');
    }
}
