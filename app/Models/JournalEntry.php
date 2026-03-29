<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'reference_id',
        'date',
        'description',
        'status',
        'created_by',
        'approved_by',
        'rejection_reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];

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
