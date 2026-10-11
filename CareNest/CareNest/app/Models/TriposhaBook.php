<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TriposhaBook extends Model
{
    use HasFactory;

    protected $primaryKey = 'serial_no';

    protected $fillable = [
        'mother_id',
        'child_id',
        'midwife_id',
        'no_of_packets',
        'issuing_date',
        'recipient_type',
        'name_of_mother',
        'name_of_child',
    ];

    protected $casts = [
        'issuing_date' => 'date',
        'no_of_packets' => 'integer',
    ];

    protected $appends = ['age'];

    public function age(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->recipient_type === 'Child' && $this->child && $this->child->date_of_birth) {
                    return Carbon::parse($this->child->date_of_birth)->age . ' years';
                }
                if ($this->mother && $this->mother->date_of_birth) {
                    return Carbon::parse($this->mother->date_of_birth)->age . ' years';
                }
                return null;
            }
        );
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Mother::class, 'mother_id', 'mother_id');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id', 'child_id');
    }

    public function midwife(): BelongsTo
    {
        return $this->belongsTo(Midwife::class, 'midwife_id', 'midwife_id');
    }
}
