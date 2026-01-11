<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Subscription:: with(['customer', 'product', 'provisioning']);

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $subscriptions = $query->latest()->paginate($request->per_page ?? 15);

        return $this->paginatedResponse($subscriptions);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists: customers,id',
            'product_id' => 'required|exists:products,id',
            'registration_date' => 'required|date',
            'email_consent' => 'boolean',
            'sms_consent' => 'boolean',
            'whatsapp_consent' => 'boolean',
            'document_sf' => 'nullable|file|max:5120',
            'document_asf' => 'nullable|file|max:5120',
            'document_pks' => 'nullable|file|max:5120',
        ]);

        $validated['status'] = 'Registered';
        $validated['created_by'] = auth()->id();

        foreach (['document_sf', 'document_asf', 'document_pks'] as $doc) {
            if ($request->hasFile($doc)) {
                $validated[$doc] = $request->file($doc)->store('subscriptions/documents', 'public');
            }
        }

        $subscription = Subscription::create($validated);

        AuditLog::log(
            'create',
            'Subscription',
            $subscription->id,
            null,
            $subscription->toArray(),
            "Created subscription for customer ID: {$subscription->customer_id}"
        );

        return $this->successResponse(
            $subscription->load(['customer', 'product']),
            'Subscription created successfully',
            201
        );
    }

    public function show(Subscription $subscription): JsonResponse
    {
        $subscription->load(['customer', 'product. internetService', 'provisioning. router', 'invoices']);

        return $this->successResponse($subscription);
    }

    public function update(Request $request, Subscription $subscription): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'sometimes|exists:products,id',
            'email_consent' => 'boolean',
            'sms_consent' => 'boolean',
            'whatsapp_consent' => 'boolean',
            'status' => 'sometimes|in: Registered,Active,Soft-Limit,Suspend,Terminated',
            'document_sf' => 'nullable|file|max:5120',
            'document_asf' => 'nullable|file|max: 5120',
            'document_pks' => 'nullable|file|max:5120',
        ]);

        $oldData = $subscription->toArray();

        foreach (['document_sf', 'document_asf', 'document_pks'] as $doc) {
            if ($request->hasFile($doc)) {
                $validated[$doc] = $request->file($doc)->store('subscriptions/documents', 'public');
            }
        }

        $subscription->update($validated);

        AuditLog::log(
            'update',
            'Subscription',
            $subscription->id,
            $oldData,
            $subscription->toArray(),
            "Updated subscription ID: {$subscription->id}"
        );

        return $this->successResponse(
            $subscription->load(['customer', 'product']),
            'Subscription updated successfully'
        );
    }

    public function destroy(Subscription $subscription): JsonResponse
    {
        $oldData = $subscription->toArray();
        
        $subscription->delete();

        AuditLog::log(
            'delete',
            'Subscription',
            $subscription->id,
            $oldData,
            null,
            "Deleted subscription ID:  {$subscription->id}"
        );

        return $this->successResponse(null, 'Subscription deleted successfully');
    }
}