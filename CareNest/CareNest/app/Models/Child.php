<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Child extends Model
{
    use HasFactory;

    protected $table = 'children';
    protected $primaryKey = 'child_id';

    protected $fillable = [
        'mother_id',
        'midwife_id',
        'date_of_birth',
        'birth_weight',
        'health_details',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'birth_weight' => 'decimal:2',
    ];

    protected $appends = ['age'];

    public function age(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->date_of_birth) {
                    return null;
                }
                $dob = Carbon::parse($this->date_of_birth);
                $diff = $dob->diff(now());
                if ($diff->y > 0) {
                    return $diff->y . ' years ' . $diff->m . ' months';
                }
                return $diff->m . ' months ' . $diff->d . ' days';
            }
        );
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Mother::class, 'mother_id', 'mother_id');
    }

    public function midwife(): BelongsTo
    {
        return $this->belongsTo(Midwife::class, 'midwife_id', 'midwife_id');
    }

    public function immunizations(): HasMany
    {
        return $this->hasMany(Immunization::class, 'child_id', 'child_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'child_id', 'child_id');
    }

    public function triposhaBooks(): HasMany
    {
        return $this->hasMany(TriposhaBook::class, 'child_id', 'child_id');
    }
}
