<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class Provisioning extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'router_id',
        'device_brand',
        'device_type',
        'device_sn',
        'device_mac',
        'device_conn',
        'pppoe_name',
        'pppoe_password',
        'static_ip',
        'static_gateway',
        'activation_date',
        'technician_name',
        'document_speedtest',
        'technician_notes',
        'created_by',
    ];

    protected $hidden = [
        'pppoe_password',
    ];

    protected function casts(): array
    {
        return [
            'activation_date' => 'date',
        ];
    }

    public function setPppoePasswordAttribute($value): void
    {
        if ($value) {
            $this->attributes['pppoe_password'] = Crypt::encryptString($value);
        }
    }

    public function getDecryptedPppoePassword(): ? string
    {
        if ($this->pppoe_password) {
            return Crypt::decryptString($this->pppoe_password);
        }
        return null;
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPPPoE(): bool
    {
        return $this->device_conn === 'PPPoE';
    }

    public function isStaticIP(): bool
    {
        return $this->device_conn === 'Static-IP';
    }
}