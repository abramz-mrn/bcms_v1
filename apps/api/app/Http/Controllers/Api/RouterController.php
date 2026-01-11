<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Models\AuditLog;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RouterController extends Controller
{
    public function __construct(
        protected MikrotikService $mikrotikService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Router::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $routers = $query->latest()->paginate($request->per_page ?? 15);

        return $this->paginatedResponse($routers);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'location' => 'nullable|string|max:200',
            'description' => 'nullable|string',
            'ip_address' => 'required|ip',
            'api_port' => 'required|integer|min:1|max:65535',
            'ssh_port' => 'required|integer|min:1|max: 65535',
            'api_username' => 'required|string|max:100',
            'api_password' => 'required|string',
            'tls_enabled' => 'boolean',
            'ssh_enabled' => 'boolean',
            'sync_interval' => 'integer|min:60',
        ]);

        $validated['status'] = 'offline';

        $router = Router::create($validated);

        AuditLog::log(
            'create',
            'Router',
            $router->id,
            null,
            array_diff_key($router->toArray(), ['api_password' => '']),
            "Created router: {$router->name}"
        );

        return $this->successResponse($router, 'Router created successfully', 201);
    }

    public function show(Router $router): JsonResponse
    {
        $router->load(['internetServices. product', 'provisionings']);

        return $this->successResponse($router);
    }

    public function update(Request $request, Router $router): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'location' => 'nullable|string|max:200',
            'description' => 'nullable|string',
            'ip_address' => 'sometimes|ip',
            'api_port' => 'sometimes|integer|min:1|max:65535',
            'ssh_port' => 'sometimes|integer|min: 1|max:65535',
            'api_username' => 'sometimes|string|max:100',
            'api_password' => 'sometimes|string',
            'tls_enabled' => 'boolean',
            'ssh_enabled' => 'boolean',
            'status' => 'sometimes|in: online,offline,maintenance',
            'sync_interval' => 'integer|min:60',
        ]);

        $oldData = array_diff_key($router->toArray(), ['api_password' => '']);

        $router->update($validated);

        AuditLog::log(
            'update',
            'Router',
            $router->id,
            $oldData,
            array_diff_key($router->toArray(), ['api_password' => '']),
            "Updated router: {$router->name}"
        );

        return $this->successResponse($router, 'Router updated successfully');
    }

    public function destroy(Router $router): JsonResponse
    {
        if ($router->provisionings()->count() > 0) {
            return $this->errorResponse('Cannot delete router with active provisionings', 422);
        }

        $oldData = array_diff_key($router->toArray(), ['api_password' => '']);
        
        $router->delete();

        AuditLog::log(
            'delete',
            'Router',
            $router->id,
            $oldData,
            null,
            "Deleted router:  {$router->name}"
        );

        return $this->successResponse(null, 'Router deleted successfully');
    }

    public function testConnection(Router $router): JsonResponse
    {
        try {
            $result = $this->mikrotikService->testConnection($router);

            if ($result['success']) {
                $router->update(['status' => 'online', 'last_sync_at' => now()]);
                
                AuditLog::log(
                    'mikrotik',
                    'Router',
                    $router->id,
                    null,
                    ['action' => 'test_connection', 'result' => 'success'],
                    "Connection test successful for router: {$router->name}"
                );

                return $this->successResponse($result, 'Connection successful');
            }

            return $this->errorResponse($result['message'], 422);
        } catch (\Exception $e) {
            $router->update(['status' => 'offline']);
            
            return $this->errorResponse('Connection failed: ' . $e->getMessage(), 422);
        }
    }

    public function sync(Router $router): JsonResponse
    {
        try {
            $result = $this->mikrotikService->syncRouter($router);

            AuditLog::log(
                'mikrotik',
                'Router',
                $router->id,
                null,
                ['action' => 'sync', 'result' => $result],
                "Synced router: {$router->name}"
            );

            return $this->successResponse($result, 'Router synced successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Sync failed: ' . $e->getMessage(), 422);
        }
    }
}