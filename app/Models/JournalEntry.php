<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'reference_id',
        'date',
        'description',
        'is_adjusting',
        'status',
        'created_by',
        'approved_by',
        'rejection_reason',
        'submitted_at',
    ];

    protected $casts = [
        'date' => 'date',
        'is_adjusting' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    /**
     * Only adjusting journal entries.
     */
    public function scopeAdjusting($query)
    {
        return $query->where('is_adjusting', true);
    }

    /**
     * Only regular (non-adjusting) journal entries.
     */
    public function scopeRegular($query)
    {
        return $query->where('is_adjusting', false);
    }

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
