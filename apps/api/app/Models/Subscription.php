<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'product_id',
        'registration_date',
        'email_consent',
        'sms_consent',
        'whatsapp_consent',
        'document_sf',
        'document_asf',
        'document_pks',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'registration_date' => 'date',
            'email_consent' => 'boolean',
            'sms_consent' => 'boolean',
            'whatsapp_consent' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function provisioning(): HasOne
    {
        return $this->hasOne(Provisioning::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'Active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'Suspend';
    }

    public function isSoftLimited(): bool
    {
        return $this->status === 'Soft-Limit';
    }
}