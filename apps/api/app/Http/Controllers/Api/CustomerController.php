<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Customer::with(['subscriptions. product']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('group_area')) {
            $query->where('group_area', $request->group_area);
        }

        if ($request->has('status')) {
            $query->whereHas('subscriptions', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        $customers = $query->latest()->paginate($request->per_page ?? 15);

        return $this->paginatedResponse($customers);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'id_card_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pos' => 'nullable|string|max:10',
            'group_area' => 'nullable|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'document_id_card' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        $validated['code'] = Customer::generateCode();
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('document_id_card')) {
            $validated['document_id_card'] = $request->file('document_id_card')
                ->store('customers/id_cards', 'public');
        }

        $customer = Customer::create($validated);

        AuditLog::log(
            'create',
            'Customer',
            $customer->id,
            null,
            $customer->toArray(),
            "Created customer:  {$customer->name}"
        );

        return $this->successResponse($customer, 'Customer created successfully', 201);
    }

    public function show(Customer $customer): JsonResponse
    {
        $customer->load(['subscriptions.product', 'subscriptions.provisioning', 'invoices', 'tickets']);

        return $this->successResponse($customer);
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:200',
            'id_card_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pos' => 'nullable|string|max:10',
            'group_area' => 'nullable|string|max:100',
            'phone' => 'sometimes|string|max:20',
            'email' => 'nullable|email|max:100',
            'document_id_card' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        $oldData = $customer->toArray();

        if ($request->hasFile('document_id_card')) {
            $validated['document_id_card'] = $request->file('document_id_card')
                ->store('customers/id_cards', 'public');
        }

        $customer->update($validated);

        AuditLog::log(
            'update',
            'Customer',
            $customer->id,
            $oldData,
            $customer->toArray(),
            "Updated customer: {$customer->name}"
        );

        return $this->successResponse($customer, 'Customer updated successfully');
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $oldData = $customer->toArray();
        
        $customer->delete();

        AuditLog::log(
            'delete',
            'Customer',
            $customer->id,
            $oldData,
            null,
            "Deleted customer:  {$customer->name}"
        );

        return $this->successResponse(null, 'Customer deleted successfully');
    }
}