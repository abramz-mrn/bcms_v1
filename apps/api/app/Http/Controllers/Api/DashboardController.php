<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Subscription;
use App\Models\Invoice;
use App\Models\Ticket;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $totalCustomers = Customer::whereNull('deleted_at')->count();
        
        $activeCustomers = Subscription::whereNotIn('status', ['Suspend', 'Terminated'])
            ->distinct('customer_id')
            ->count('customer_id');

        $suspendedSubscriptions = Subscription::where('status', 'Suspend')->count();

        $unpaidInvoices = Invoice::where('status', 'Unpaid')->count();
        $unpaidAmount = Invoice::where('status', 'Unpaid')->sum('total_amount');

        $openTickets = Ticket::whereIn('status', ['open', 'assigned', 'in_progress'])->count();

        $monthlyRevenue = Invoice::where('status', 'Paid')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('total_amount');

        return $this->successResponse([
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'suspended_subscriptions' => $suspendedSubscriptions,
            'unpaid_invoices' => $unpaidInvoices,
            'unpaid_amount' => (float) $unpaidAmount,
            'open_tickets' => $openTickets,
            'monthly_revenue' => (float) $monthlyRevenue,
        ]);
    }

    public function suspendedCustomers(Request $request): JsonResponse
    {
        $suspended = Subscription::with(['customer', 'product'])
            ->where('status', 'Suspend')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'customer_code' => $subscription->customer->code,
                    'customer_name' => $subscription->customer->name,
                    'product_name' => $subscription->product->name,
                    'phone' => $subscription->customer->phone,
                ];
            });

        return $this->successResponse($suspended);
    }

    public function recentActivities(Request $request): JsonResponse
    {
        $activities = AuditLog::with('user')
            ->latest('created_at')
            ->limit(20)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user_name' => $log->user_name,
                    'action' => $log->action,
                    'resource_type' => $log->resource_type,
                    'description' => $log->description,
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return $this->successResponse($activities);
    }

    public function recentTickets(Request $request): JsonResponse
    {
        $tickets = Ticket::with(['customer', 'assignee'])
            ->whereIn('status', ['open', 'assigned', 'in_progress'])
            ->orderByRaw("CASE 
                WHEN priority = 'critical' THEN 1 
                WHEN priority = 'high' THEN 2 
                WHEN priority = 'medium' THEN 3 
                ELSE 4 END")
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'customer_name' => $ticket->customer? ->name ??  $ticket->caller_name,
                    'subject' => $ticket->subject,
                    'category' => $ticket->category,
                    'priority' => $ticket->priority,
                    'status' => $ticket->status,
                    'assigned_to' => $ticket->assignee?->name,
                    'sla_breached' => $ticket->isSlaBreached(),
                    'created_at' => $ticket->created_at->format('Y-m-d H:i: s'),
                ];
            });

        return $this->successResponse($tickets);
    }
}