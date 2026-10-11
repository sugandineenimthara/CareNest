<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreviousPregnancyHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'mother_id',
        'date_of_birth',
        'birth_weight',
        'gender',
        'which_pregnancy',
        'results',
        'place_of_pregnancy',
        'rubella_vaccinated',
        'folic_acid_vaccinated',
        'infertility',
        'blood_relation_marriage',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'birth_weight' => 'decimal:2',
        'rubella_vaccinated' => 'boolean',
        'folic_acid_vaccinated' => 'boolean',
        'infertility' => 'boolean',
        'blood_relation_marriage' => 'boolean',
    ];

    protected $appends = ['age'];

    public function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->date_of_birth ? Carbon::parse($this->date_of_birth)->age : null
        );
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Mother::class, 'mother_id', 'mother_id');
    }
}
