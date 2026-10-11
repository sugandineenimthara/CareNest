<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Midwife extends Model
{
    use HasFactory;

    protected $primaryKey = 'midwife_id';

    protected $fillable = [
        'midwife_name',
        'area_id',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id', 'area_id');
    }

    public function mothers(): HasMany
    {
        return $this->hasMany(Mother::class, 'midwife_id', 'midwife_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Child::class, 'midwife_id', 'midwife_id');
    }

    public function immunizations(): HasMany
    {
        return $this->hasMany(Immunization::class, 'midwife_id', 'midwife_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'midwife_id', 'midwife_id');
    }

    public function triposhaBooks(): HasMany
    {
        return $this->hasMany(TriposhaBook::class, 'midwife_id', 'midwife_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'midwife_id', 'midwife_id');
    }

    public function dailyClinicSummaries(): HasMany
    {
        return $this->hasMany(DailyClinicSummary::class, 'midwife_id', 'midwife_id');
    }
}
