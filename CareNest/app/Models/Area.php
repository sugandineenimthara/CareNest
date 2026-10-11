<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    use HasFactory;

    protected $primaryKey = 'area_id';

    protected $fillable = [
        'area_name',
    ];

    public function midwives(): HasMany
    {
        return $this->hasMany(Midwife::class, 'area_id', 'area_id');
    }

    public function dailyClinicSummaries(): HasMany
    {
        return $this->hasMany(DailyClinicSummary::class, 'area_id', 'area_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Child::class, 'area_id', 'area_id');
    }
}
