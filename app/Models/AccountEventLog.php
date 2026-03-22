<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountEventLog extends Model
{
    protected $fillable = [
        'account_id',
        'user_id',
        'event_type',
        'before_image',
        'after_image',
    ];

    protected function casts(): array
    {
        return [
            'before_image' => 'array',
            'after_image' => 'array',
        ];
    }

    /**
     * The account this event belongs to.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * The user who triggered this event.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
