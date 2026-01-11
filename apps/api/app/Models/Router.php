<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class Router extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'description',
        'ip_address',
        'api_port',
        'ssh_port',
        'api_username',
        'api_password',
        'api_certificate',
        'tls_enabled',
        'ssh_enabled',
        'status',
        'sync_interval',
        'last_sync_at',
        'config_backup',
    ];

    protected $hidden = [
        'api_password',
    ];

    protected function casts(): array
    {
        return [
            'tls_enabled' => 'boolean',
            'ssh_enabled' => 'boolean',
            'last_sync_at' => 'datetime',
            'config_backup' => 'array',
        ];
    }

    public function setApiPasswordAttribute($value): void
    {
        $this->attributes['api_password'] = Crypt::encryptString($value);
    }

    public function getDecryptedApiPassword(): string
    {
        return Crypt:: decryptString($this->api_password);
    }

    public function internetServices(): HasMany
    {
        return $this->hasMany(InternetService::class);
    }

    public function provisionings(): HasMany
    {
        return $this->hasMany(Provisioning::class);
    }

    public function isOnline(): bool
    {
        return $this->status === 'online';
    }
}