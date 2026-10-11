<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PregnancyHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'mother_id',
        'no_living_children',
        'age_of_youngest_child',
        'last_menstrual_period',
        'expected_date_of_delivery',
        'date_confirmed_by_US',
        'no_of_weeks_pregnant_at_registration',
        'first_fetal_movements_date',
    ];

    protected $casts = [
        'last_menstrual_period' => 'date:Y-m-d',
        'expected_date_of_delivery' => 'date:Y-m-d',
        'date_confirmed_by_US' => 'date:Y-m-d',
        'first_fetal_movements_date' => 'date:Y-m-d',
        'no_living_children' => 'integer',
        'age_of_youngest_child' => 'integer',
        'no_of_weeks_pregnant_at_registration' => 'integer',
    ];

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Mother::class, 'mother_id', 'mother_id');
    }
}
