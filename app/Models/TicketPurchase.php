<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TicketPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lottery_id',
        'ticket_price',
        'quantity',
        'total_price',
        'payment_method',
        'transaction_id',
        'screenshot_path',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'ticket_price'  => 'decimal:2',
            'total_price'   => 'decimal:2',
            'quantity'      => 'integer',
            'reviewed_at'   => 'datetime',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function lotteryNumber(): HasOne
    {
        return $this->hasOne(LotteryNumber::class);
    }

    public function lotteryNumbers(): HasMany
    {
        return $this->hasMany(LotteryNumber::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Helpers
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'badge-warning',
            'approved'  => 'badge-success',
            'rejected'  => 'badge-danger',
            'cancelled' => 'badge-secondary',
            default     => 'badge-secondary',
        };
    }
}
