<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternetService extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'router_id',
        'profile',
        'rate_limit',
        'limit_at',
        'priority',
        'auto_soft_limit',
        'auto_suspend',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function getSoftLimitRateLimit(): string
    {
        // Return 50% of the rate_limit
        $parts = explode('/', $this->rate_limit);
        if (count($parts) === 2) {
            $download = $this->parseSpeed($parts[0]) / 2;
            $upload = $this->parseSpeed($parts[1]) / 2;
            return $this->formatSpeed($download) . '/' . $this->formatSpeed($upload);
        }
        return $this->rate_limit;
    }

    private function parseSpeed(string $speed): int
    {
        $speed = strtoupper(trim($speed));
        $value = (int) $speed;
        
        if (str_contains($speed, 'G')) {
            return $value * 1000000000;
        } elseif (str_contains($speed, 'M')) {
            return $value * 1000000;
        } elseif (str_contains($speed, 'K')) {
            return $value * 1000;
        }
        
        return $value;
    }

    private function formatSpeed(int $bps): string
    {
        if ($bps >= 1000000000) {
            return ($bps / 1000000000) . 'G';
        } elseif ($bps >= 1000000) {
            return ($bps / 1000000) . 'M';
        } elseif ($bps >= 1000) {
            return ($bps / 1000) . 'K';
        }
        return $bps . '';
    }
}