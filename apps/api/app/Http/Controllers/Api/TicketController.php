<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Ticket::with(['customer', 'assignee']);

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $tickets = $query->latest()->paginate($request->per_page ?? 15);

        return $this->paginatedResponse($tickets);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'subscription_id' => 'nullable|exists:subscriptions,id',
            'caller_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'category' => 'required|in:information,technical,billing,complaint',
            'priority' => 'required|in:low,medium,high,critical',
            'subject' => 'required|string|max:200',
            'description' => 'required|string',
        ]);

        $validated['ticket_number'] = Ticket::generateTicketNumber();
        $validated['status'] = 'open';
        $validated['created_by'] = auth()->id();

        // Set SLA based on priority
        $slaHours = match ($validated['priority']) {
            'critical' => 2,
            'high' => 4,
            'medium' => 8,
            'low' => 24,
        };
        $validated['sla_due_date'] = now()->addHours($slaHours);

        $ticket = Ticket::create($validated);

        AuditLog::log(
            'create',
            'Ticket',
            $ticket->id,
            null,
            $ticket->toArray(),
            "Created ticket:  {$ticket->ticket_number}"
        );

        return $this->successResponse(
            $ticket->load(['customer', 'subscription']),
            'Ticket created successfully',
            201
        );
    }

    public function show(Ticket $ticket): JsonResponse
    {
        $ticket->load(['customer', 'subscription. product', 'assignee', 'creator']);

        return $this->successResponse($ticket);
    }

    public function update(Request $request, Ticket $ticket): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'sometimes|in: information,technical,billing,complaint',
            'priority' => 'sometimes|in:low,medium,high,critical',
            'subject' => 'sometimes|string|max:200',
            'description' => 'sometimes|string',
        ]);

        $oldData = $ticket->toArray();

        $ticket->update($validated);

        AuditLog::log(
            'update',
            'Ticket',
            $ticket->id,
            $oldData,
            $ticket->toArray(),
            "Updated ticket: {$ticket->ticket_number}"
        );

        return $this->successResponse($ticket, 'Ticket updated successfully');
    }

    public function assign(Request $request, Ticket $ticket): JsonResponse
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $oldData = $ticket->toArray();

        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'assigned_at' => now(),
            'status' => 'assigned',
        ]);

        AuditLog::log(
            'update',
            'Ticket',
            $ticket->id,
            $oldData,
            $ticket->toArray(),
            "Assigned ticket:  {$ticket->ticket_number}"
        );

        return $this->successResponse(
            $ticket->load('assignee'),
            'Ticket assigned successfully'
        );
    }

    public function resolve(Request $request, Ticket $ticket): JsonResponse
    {
        $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $oldData = $ticket->toArray();

        $ticket->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_notes' => $request->resolution_notes,
        ]);

        AuditLog::log(
            'update',
            'Ticket',
            $ticket->id,
            $oldData,
            $ticket->toArray(),
            "Resolved ticket: {$ticket->ticket_number}"
        );

        return $this->successResponse($ticket, 'Ticket resolved successfully');
    }

    public function close(Request $request, Ticket $ticket): JsonResponse
    {
        $request->validate([
            'customer_rating' => 'nullable|integer|min:1|max:5',
            'customer_feedback' => 'nullable|string',
        ]);

        $oldData = $ticket->toArray();

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
            'customer_rating' => $request->customer_rating,
            'customer_feedback' => $request->customer_feedback,
        ]);

        AuditLog::log(
            'update',
            'Ticket',
            $ticket->id,
            $oldData,
            $ticket->toArray(),
            "Closed ticket: {$ticket->ticket_number}"
        );

        return $this->successResponse($ticket, 'Ticket closed successfully');
    }
}