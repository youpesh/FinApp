<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityResetToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'answer_verified',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'answer_verified' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }
}
