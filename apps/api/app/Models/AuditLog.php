<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'ip_address',
        'action',
        'resource_type',
        'resource_id',
        'old_value',
        'new_value',
        'description',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_value' => 'array',
            'new_value' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(
        string $action,
        ? string $resourceType = null,
        ?int $resourceId = null,
        ?array $oldValue = null,
        ?array $newValue = null,
        ?string $description = null
    ): self {
        $user = auth()->user();
        
        return self::create([
            'user_id' => $user? ->id,
            'user_name' => $user?->name ??  'System',
            'ip_address' => request()->ip(),
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'description' => $description,
            'created_at' => now(),
        ]);
    }
}