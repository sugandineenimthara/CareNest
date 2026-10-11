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
        'child_name',
        'gender',
        'mother_id',
        'midwife_id',
        'area_id',
        'date_of_birth',
        'birth_weight',
        'birth_length',
        'bcg_vaccinated_at_birth',
        'bcg_batch_no',
        'bcg_vaccinated_date',
        'health_details',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'birth_weight' => 'decimal:2',
        'birth_length' => 'decimal:2',
        'bcg_vaccinated_at_birth' => 'boolean',
        'bcg_vaccinated_date' => 'date',
    ];

    protected $appends = ['age', 'display_name'];

    public function displayName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->child_name ?: ('Child #' . $this->child_id . ($this->mother ? ' of ' . $this->mother->mother_name : ''))
        );
    }

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
                    return $diff->y . ' yr' . ($diff->y > 1 ? 's ' : ' ') . $diff->m . ' mo';
                }
                if ($diff->m > 0) {
                    return $diff->m . ' mo ' . $diff->d . ' d';
                }
                return $diff->d . ' days';
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

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id', 'area_id');
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

    /**
     * Standard National Immunization Schedule for Children
     */
    public static function getVaccinationSchedule(): array
    {
        return [
            [
                'id' => 'bcg',
                'vaccine_name' => 'BCG',
                'target_period' => 'At Birth (within 24 hrs)',
                'months_due' => 0,
                'dose' => 'Single Dose',
                'description' => 'Tuberculosis vaccine administered at birth'
            ],
            [
                'id' => 'penta_1',
                'vaccine_name' => 'Penta 1',
                'target_period' => '2 Months',
                'months_due' => 2,
                'dose' => '1st Dose',
                'description' => 'Diphtheria, Pertussis, Tetanus, Hep B, Hib'
            ],
            [
                'id' => 'polio_1',
                'vaccine_name' => 'Polio 1 (OPV)',
                'target_period' => '2 Months',
                'months_due' => 2,
                'dose' => '1st Dose',
                'description' => 'Oral Polio Vaccine'
            ],
            [
                'id' => 'fitv_1',
                'vaccine_name' => 'FITV 1',
                'target_period' => '4 Months',
                'months_due' => 4,
                'dose' => '1st Dose',
                'description' => 'Fractional Inactivated Poliovirus Vaccine'
            ],
            [
                'id' => 'penta_2',
                'vaccine_name' => 'Penta 2',
                'target_period' => '6 Months',
                'months_due' => 6,
                'dose' => '2nd Dose',
                'description' => 'Diphtheria, Pertussis, Tetanus, Hep B, Hib'
            ],
            [
                'id' => 'polio_2',
                'vaccine_name' => 'Polio 2 (OPV)',
                'target_period' => '6 Months',
                'months_due' => 6,
                'dose' => '2nd Dose',
                'description' => 'Oral Polio Vaccine'
            ],
            [
                'id' => 'mmr_1',
                'vaccine_name' => 'MMR 1',
                'target_period' => '9 Months',
                'months_due' => 9,
                'dose' => '1st Dose',
                'description' => 'Measles, Mumps, Rubella'
            ],
            [
                'id' => 'fitv_2',
                'vaccine_name' => 'FITV 2',
                'target_period' => '9 Months',
                'months_due' => 9,
                'dose' => '2nd Dose',
                'description' => 'Fractional Inactivated Poliovirus Vaccine'
            ],
            [
                'id' => 'je',
                'vaccine_name' => 'JE',
                'target_period' => '12 Months (1 Year)',
                'months_due' => 12,
                'dose' => 'Single Dose',
                'description' => 'Japanese Encephalitis Vaccine'
            ],
            [
                'id' => 'dpt',
                'vaccine_name' => 'DPT',
                'target_period' => '18 Months (1.5 Years)',
                'months_due' => 18,
                'dose' => 'Booster Dose',
                'description' => 'Diphtheria, Pertussis, Tetanus Booster'
            ],
            [
                'id' => 'polio_3',
                'vaccine_name' => 'Polio 3 (OPV)',
                'target_period' => '18 Months (1.5 Years)',
                'months_due' => 18,
                'dose' => '3rd Dose',
                'description' => 'Oral Polio Vaccine'
            ],
            [
                'id' => 'mmr_2',
                'vaccine_name' => 'MMR 2',
                'target_period' => '3 Years',
                'months_due' => 36,
                'dose' => '2nd Dose',
                'description' => 'Measles, Mumps, Rubella Booster'
            ],
            [
                'id' => 'dt',
                'vaccine_name' => 'DT',
                'target_period' => '5 Years',
                'months_due' => 60,
                'dose' => 'Booster Dose',
                'description' => 'Diphtheria & Tetanus Booster'
            ],
        ];
    }
}
