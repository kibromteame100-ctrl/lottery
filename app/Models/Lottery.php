<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lottery extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'ticket_price',
        'draw_date',
        'status',
        'number_prefix',
        'number_length',
        'max_tickets',
    ];

    protected function casts(): array
    {
        return [
            'draw_date'    => 'datetime',
            'ticket_price' => 'decimal:2',
            'number_length' => 'integer',
            'max_tickets'  => 'integer',
        ];
    }

    // Relationships
    public function ticketPurchases(): HasMany
    {
        return $this->hasMany(TicketPurchase::class);
    }

    public function lotteryNumbers(): HasMany
    {
        return $this->hasMany(LotteryNumber::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function soldTicketsCount(): int
    {
        return (int) $this->ticketPurchases()
            ->where('status', 'approved')
            ->sum('quantity');
    }

    public function remainingTickets(): ?int
    {
        if ($this->max_tickets === null) {
            return null;
        }
        return max(0, $this->max_tickets - $this->soldTicketsCount());
    }

    public function totalRevenue(): float
    {
        return (float) $this->ticketPurchases()
            ->where('status', 'approved')
            ->sum('total_price');
    }

    public function nextNumberSequence(): int
    {
        return $this->lotteryNumbers()->count() + 1;
    }
}
