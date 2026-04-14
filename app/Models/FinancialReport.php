<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialReport extends Model
{
    protected $fillable = [
        'type',
        'title',
        'parameters',
        'payload',
        'generated_by',
        'generated_at',
    ];

    protected $casts = [
        'parameters' => 'array',
        'payload' => 'array',
        'generated_at' => 'datetime',
    ];

    public const TYPES = [
        'trial_balance' => 'Trial Balance',
        'income_statement' => 'Income Statement',
        'balance_sheet' => 'Balance Sheet',
        'retained_earnings' => 'Retained Earnings Statement',
    ];

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
