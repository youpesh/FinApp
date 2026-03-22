<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Account extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'account_name',
        'account_number',
        'account_description',
        'normal_side',
        'account_category',
        'account_subcategory',
        'initial_balance',
        'debit',
        'credit',
        'balance',
        'order',
        'statement',
        'comment',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'account_number' => 'integer',
            'initial_balance' => 'decimal:2',
            'debit' => 'decimal:2',
            'credit' => 'decimal:2',
            'balance' => 'decimal:2',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Activity log options for Spatie.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'account_name',
                'account_number',
                'account_description',
                'normal_side',
                'account_category',
                'account_subcategory',
                'initial_balance',
                'debit',
                'credit',
                'balance',
                'order',
                'statement',
                'comment',
                'is_active',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ── Relationships ────────────────────────────────────────────

    /**
     * The user who created this account.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Event logs for this account.
     */
    public function eventLogs()
    {
        return $this->hasMany(AccountEventLog::class);
    }

    // ── Scopes ───────────────────────────────────────────────────

    /**
     * Only active accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Search by account name or account number.
     */
    public function scopeSearch($query, ?string $term)
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('account_name', 'like', "%{$term}%")
                ->orWhere('account_number', 'like', "%{$term}%");
        });
    }

    /**
     * Filter by category.
     */
    public function scopeFilterCategory($query, ?string $category)
    {
        return $category ? $query->where('account_category', $category) : $query;
    }

    /**
     * Filter by subcategory.
     */
    public function scopeFilterSubcategory($query, ?string $subcategory)
    {
        return $subcategory ? $query->where('account_subcategory', $subcategory) : $query;
    }

    /**
     * Filter by statement type.
     */
    public function scopeFilterStatement($query, ?string $statement)
    {
        return $statement ? $query->where('statement', $statement) : $query;
    }

    /**
     * Filter by active status.
     */
    public function scopeFilterStatus($query, ?string $status)
    {
        if ($status === 'active') {
            return $query->where('is_active', true);
        }
        if ($status === 'inactive') {
            return $query->where('is_active', false);
        }
        return $query;
    }

    // ── Accessors ────────────────────────────────────────────────

    /**
     * Balance formatted with commas and 2 decimal places.
     */
    public function getFormattedBalanceAttribute(): string
    {
        return number_format((float) $this->balance, 2);
    }

    /**
     * Initial balance formatted.
     */
    public function getFormattedInitialBalanceAttribute(): string
    {
        return number_format((float) $this->initial_balance, 2);
    }

    /**
     * Debit formatted.
     */
    public function getFormattedDebitAttribute(): string
    {
        return number_format((float) $this->debit, 2);
    }

    /**
     * Credit formatted.
     */
    public function getFormattedCreditAttribute(): string
    {
        return number_format((float) $this->credit, 2);
    }

    /**
     * Category label map for account number starting digits.
     */
    public static function categoryStartDigit(): array
    {
        return [
            'asset' => 1,
            'liability' => 2,
            'equity' => 3,
            'revenue' => 4,
            'expense' => 5,
        ];
    }

    /**
     * Full snapshot of the account for event log images.
     */
    public function toSnapshot(): array
    {
        return [
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'account_description' => $this->account_description,
            'normal_side' => $this->normal_side,
            'account_category' => $this->account_category,
            'account_subcategory' => $this->account_subcategory,
            'initial_balance' => $this->initial_balance,
            'debit' => $this->debit,
            'credit' => $this->credit,
            'balance' => $this->balance,
            'order' => $this->order,
            'statement' => $this->statement,
            'comment' => $this->comment,
            'is_active' => $this->is_active,
        ];
    }
}
