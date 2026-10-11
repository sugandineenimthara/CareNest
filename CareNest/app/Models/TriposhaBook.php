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
        'opening_stock',
        'session_label',
    ];

    protected $casts = [
        'issuing_date'   => 'date',
        'no_of_packets'  => 'integer',
        'opening_stock'  => 'integer',
    ];

    // ─── Relationships ──────────────────────────────────────────────
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

    // ─── Computed Attributes ────────────────────────────────────────
    /** Display name of the recipient */
    public function getRecipientNameAttribute(): string
    {
        if ($this->recipient_type === 'Child') {
            return $this->name_of_child
                ?? ($this->child ? $this->child->display_name : 'Unknown Child');
        }
        return $this->name_of_mother
            ?? ($this->mother ? $this->mother->mother_name : 'Unknown Mother');
    }

    /** Remaining stock after this session */
    public function getRemainingStockAttribute(): ?int
    {
        if ($this->opening_stock === null) return null;

        $distributed = self::where('issuing_date', $this->issuing_date)
            ->where('session_label', $this->session_label)
            ->sum('no_of_packets');

        return $this->opening_stock - $distributed;
    }
}
