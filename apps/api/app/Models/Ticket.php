<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'customer_id',
        'subscription_id',
        'caller_name',
        'phone',
        'email',
        'category',
        'priority',
        'subject',
        'description',
        'status',
        'assigned_to',
        'assigned_at',
        'resolved_at',
        'closed_at',
        'sla_due_date',
        'resolution_notes',
        'customer_rating',
        'customer_feedback',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'sla_due_date' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['open', 'assigned', 'in_progress']);
    }

    public function isSlaBreached(): bool
    {
        return $this->sla_due_date && $this->sla_due_date->isPast() && $this->isOpen();
    }

    public static function generateTicketNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        
        $lastTicket = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTicket) {
            $lastNumber = (int) substr($lastTicket->ticket_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('TKT-%s-%s-%04d', $year, $month, $newNumber);
    }
}