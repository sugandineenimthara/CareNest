<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mother extends Model
{
    use HasFactory;

    protected $primaryKey = 'mother_id';

    protected $fillable = [
        'mother_name',
        'phone_no',
        'midwife_id',
        'height',
        'weight',
        'husband_name',
        'husband_occupation',
        'date_of_birth',
        'address',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'date_of_birth' => 'date',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    protected $appends = ['age', 'bmi'];

    public function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->date_of_birth ? Carbon::parse($this->date_of_birth)->age : null
        );
    }

    public function bmi(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->height || !$this->weight || $this->height <= 0) {
                    return null;
                }
                $heightInM = $this->height > 3 ? $this->height / 100 : $this->height;
                return round($this->weight / ($heightInM * $heightInM), 2);
            }
        );
    }

    public function midwife(): BelongsTo
    {
        return $this->belongsTo(Midwife::class, 'midwife_id', 'midwife_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Child::class, 'mother_id', 'mother_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'mother_id', 'mother_id');
    }

    public function previousPregnancyHistories(): HasMany
    {
        return $this->hasMany(PreviousPregnancyHistory::class, 'mother_id', 'mother_id');
    }

    public function pregnancyHistories(): HasMany
    {
        return $this->hasMany(PregnancyHistory::class, 'mother_id', 'mother_id');
    }

    public function familyHealthHistory(): HasOne
    {
        return $this->hasOne(FamilyHealthHistory::class, 'mother_id', 'mother_id');
    }

    public function testsDone(): HasMany
    {
        return $this->hasMany(TestDone::class, 'mother_id', 'mother_id');
    }

    public function triposhaBooks(): HasMany
    {
        return $this->hasMany(TriposhaBook::class, 'mother_id', 'mother_id');
    }

    public function immunizations(): HasMany
    {
        return $this->hasMany(Immunization::class, 'mother_id', 'mother_id');
    }

    public function getPregnancyNumberAttribute(): int
    {
        return $this->previousPregnancyHistories()->count() + 1;
    }

    public function isTetanusSafe(): bool
    {
        return $this->pregnancy_number > 4;
    }
}
