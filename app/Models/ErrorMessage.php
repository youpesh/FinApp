<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorMessage extends Model
{
    protected $fillable = [
        'code',
        'message',
        'severity',
    ];

    /**
     * Get error message by code.
     */
    public static function getByCode(string $code): ?string
    {
        $error = static::where('code', $code)->first();
        return $error ? $error->message : null;
    }
}
