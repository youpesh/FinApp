<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'journal_entry_id',
        'file_path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
