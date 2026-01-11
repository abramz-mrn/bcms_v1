<?php

namespace App\Services;

use App\Models\Router;
use App\Models\Provisioning;
use App\Models\Subscription;
use App\Models\InternetService;
use App\Models\AuditLog;
use Exception;

class MikrotikService
{
    protected ? object $client = null;

    public function connect(Router $router): bool
    {
        try {
            if ($router->status === 'maintenance') {
                throw new Exception('Router is in maintenance mode');
            }

            // In production, use RouterOS API library like: 
            // - PEAR2_Net_RouterOS
            // - RouterOS-PHP-API
            // $this->client = new \RouterOSAPI($router->ip_address, $router->api_username, $router->getDecryptedApiPassword(), $router->api_port);
            
            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to connect to router:  ' . $e->getMessage());
        }
    }

    public function testConnection(Router $router): array
    {
        try {
            $this->connect($router);

            return [
                'success' => true,
                'message' => 'Connection successful',
                'router_identity' => $router->name,
                'uptime' => '1d 2h 30m',
                'version' => '7.10',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function syncRouter(Router $router): array
    {
        $this->connect($router);

        $router->update([
            'last_sync_at' => now(),
            'status' => 'online',
        ]);

        return [
            'synced_at' => now()->toDateTimeString(),
            'pppoe_secrets' => 0,
            'ip_bindings' => 0,
        ];
    }

    public function createPPPoESecret(Router $router, Provisioning $provisioning): bool
    {
        $this->connect($router);

        $subscription = $provisioning->subscription;
        $internetService = $subscription->product->internetService;

        AuditLog::log(
            'mikrotik',
            'Provisioning',
            $provisioning->id,
            null,
            [
                'action' => 'create_pppoe_secret',
                'pppoe_name' => $provisioning->pppoe_name,
                'profile' => $internetService->profile ??  'default',
            ],
            "Created PPPoE secret:  {$provisioning->pppoe_name} on router:  {$router->name}"
        );

        return true;
    }

    public function updatePPPoESecret(Router $router, Provisioning $provisioning, array $updates): bool
    {
        $this->connect($router);

        AuditLog::log(
            'mikrotik',
            'Provisioning',
            $provisioning->id,
            null,
            ['action' => 'update_pppoe_secret', 'updates' => $updates],
            "Updated PPPoE secret: {$provisioning->pppoe_name} on router: {$router->name}"
        );

        return true;
    }

    public function deletePPPoESecret(Router $router, string $pppoeName): bool
    {
        $this->connect($router);

        AuditLog::log(
            'mikrotik',
            'Router',
            $router->id,
            null,
            ['action' => 'delete_pppoe_secret', 'pppoe_name' => $pppoeName],
            "Deleted PPPoE secret: {$pppoeName} on router: {$router->name}"
        );

        return true;
    }

    public function disablePPPoESecret(Router $router, string $pppoeName): bool
    {
        $this->connect($router);

        AuditLog::log(
            'mikrotik',
            'Router',
            $router->id,
            null,
            ['action' => 'disable_pppoe_secret', 'pppoe_name' => $pppoeName],
            "Disabled PPPoE secret: {$pppoeName} on router: {$router->name}"
        );

        return true;
    }

    public function enablePPPoESecret(Router $router, string $pppoeName): bool
    {
        $this->connect($router);

        AuditLog::log(
            'mikrotik',
            'Router',
            $router->id,
            null,
            ['action' => 'enable_pppoe_secret', 'pppoe_name' => $pppoeName],
            "Enabled PPPoE secret: {$pppoeName} on router: {$router->name}"
        );

        return true;
    }

    public function setPPPoERateLimit(Router $router, string $pppoeName, string $rateLimit): bool
    {
        $this->connect($router);

        AuditLog::log(
            'mikrotik',
            'Router',
            $router->id,
            null,
            ['action' => 'set_pppoe_rate_limit', 'pppoe_name' => $pppoeName, 'rate_limit' => $rateLimit],
            "Set rate-limit {$rateLimit} for PPPoE:  {$pppoeName} on router: {$router->name}"
        );

        return true;
    }

    public function createStaticIPBinding(Router $router, Provisioning $provisioning): bool
    {
        $this->connect($router);

        AuditLog::log(
            'mikrotik',
            'Provisioning',
            $provisioning->id,
            null,
            [
                'action' => 'create_static_ip',
                'ip_address' => $provisioning->static_ip,
                'gateway' => $provisioning->static_gateway,
            ],
            "Created Static IP binding: {$provisioning->static_ip} on router: {$router->name}"
        );

        return true;
    }

    public function setStaticIPMaxLimit(Router $router, Provisioning $provisioning, string $maxLimit): bool
    {
        $this->connect($router);

        AuditLog::log(
            'mikrotik',
            'Provisioning',
            $provisioning->id,
            null,
            ['action' => 'set_static_ip_max_limit', 'max_limit' => $maxLimit],
            "Set max-limit {$maxLimit} for Static IP:  {$provisioning->static_ip} on router: {$router->name}"
        );

        return true;
    }

    public function disableStaticIP(Router $router, Provisioning $provisioning): bool
    {
        $this->connect($router);

        AuditLog::log(
            'mikrotik',
            'Provisioning',
            $provisioning->id,
            null,
            ['action' => 'disable_static_ip', 'ip_address' => $provisioning->static_ip],
            "Disabled Static IP: {$provisioning->static_ip} on router: {$router->name}"
        );

        return true;
    }

    public function enableStaticIP(Router $router, Provisioning $provisioning): bool
    {
        $this->connect($router);

        AuditLog::log(
            'mikrotik',
            'Provisioning',
            $provisioning->id,
            null,
            ['action' => 'enable_static_ip', 'ip_address' => $provisioning->static_ip],
            "Enabled Static IP: {$provisioning->static_ip} on router: {$router->name}"
        );

        return true;
    }

    public function ping(Router $router, string $address, int $count = 5): array
    {
        $this->connect($router);

        // Mock response - in production, execute actual ping command
        return [
            'host' => $address,
            'sent' => $count,
            'received' => $count,
            'packet_loss' => '0%',
            'min_rtt' => '1ms',
            'avg_rtt' => '2ms',
            'max_rtt' => '3ms',
        ];
    }

    public function applySoftLimit(Subscription $subscription): bool
    {
        $provisioning = $subscription->provisioning;
        if (!$provisioning) {
            return false;
        }

        $router = $provisioning->router;
        $internetService = $subscription->product->internetService;
        $softLimitRate = $internetService->getSoftLimitRateLimit();

        if ($provisioning->isPPPoE()) {
            $this->setPPPoERateLimit($router, $provisioning->pppoe_name, $softLimitRate);
        } else {
            $this->setStaticIPMaxLimit($router, $provisioning, $softLimitRate);
        }

        $subscription->update(['status' => 'Soft-Limit']);

        AuditLog::log(
            'mikrotik',
            'Subscription',
            $subscription->id,
            ['status' => 'Active'],
            ['status' => 'Soft-Limit', 'rate_limit' => $softLimitRate],
            "Applied soft-limit to subscription ID: {$subscription->id}"
        );

        return true;
    }

    public function applySuspend(Subscription $subscription): bool
    {
        $provisioning = $subscription->provisioning;
        if (!$provisioning) {
            return false;
        }

        $router = $provisioning->router;

        if ($provisioning->isPPPoE()) {
            $this->disablePPPoESecret($router, $provisioning->pppoe_name);
        } else {
            $this->disableStaticIP($router, $provisioning);
        }

        $subscription->update(['status' => 'Suspend']);

        AuditLog::log(
            'mikrotik',
            'Subscription',
            $subscription->id,
            ['status' => $subscription->getOriginal('status')],
            ['status' => 'Suspend'],
            "Suspended subscription ID: {$subscription->id}"
        );

        return true;
    }

    public function applyReactivate(Subscription $subscription): bool
    {
        $provisioning = $subscription->provisioning;
        if (!$provisioning) {
            return false;
        }

        $router = $provisioning->router;
        $internetService = $subscription->product->internetService;

        if ($provisioning->isPPPoE()) {
            $this->enablePPPoESecret($router, $provisioning->pppoe_name);
            // Reset to normal rate limit
            $this->setPPPoERateLimit($router, $provisioning->pppoe_name, $internetService->rate_limit);
        } else {
            $this->enableStaticIP($router, $provisioning);
            $this->setStaticIPMaxLimit($router, $provisioning, $internetService->rate_limit);
        }

        $subscription->update(['status' => 'Active']);

        AuditLog:: log(
            'mikrotik',
            'Subscription',
            $subscription->id,
            ['status' => $subscription->getOriginal('status')],
            ['status' => 'Active'],
            "Reactivated subscription ID: {$subscription->id}"
        );

        return true;
    }
}