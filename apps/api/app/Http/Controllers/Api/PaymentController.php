<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\AuditLog;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['invoice. customer']);

        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->latest()->paginate($request->per_page ?? 15);

        return $this->paginatedResponse($payments);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_method' => 'required|in:cash,transfer,virtual_account',
            'payment_gateway' => 'nullable|in: Midtrans,Xendit,Manual',
            'amount' => 'required|numeric|min:0',
            'reference_number' => 'nullable|string|max:100',
            'document_proof' => 'nullable|file|max:5120',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'pending';
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('document_proof')) {
            $validated['document_proof'] = $request->file('document_proof')
                ->store('payments/proofs', 'public');
        }

        if ($validated['payment_method'] === 'cash') {
            $validated['payment_gateway'] = 'Manual';
        }

        $payment = Payment:: create($validated);

        AuditLog::log(
            'create',
            'Payment',
            $payment->id,
            null,
            $payment->toArray(),
            "Created payment for invoice:  {$payment->invoice->invoice_no}"
        );

        return $this->successResponse(
            $payment->load(['invoice. customer']),
            'Payment created successfully',
            201
        );
    }

    public function show(Payment $payment): JsonResponse
    {
        $payment->load(['invoice.customer', 'creator', 'verifier']);

        return $this->successResponse($payment);
    }

    public function verify(Request $request, Payment $payment): JsonResponse
    {
        if ($payment->status !== 'pending') {
            return $this->errorResponse('Payment is not in pending status', 422);
        }

        $oldData = $payment->toArray();

        $this->paymentService->verifyPayment($payment, auth()->user());

        AuditLog:: log(
            'update',
            'Payment',
            $payment->id,
            $oldData,
            $payment->fresh()->toArray(),
            "Verified payment for invoice: {$payment->invoice->invoice_no}"
        );

        return $this->successResponse(
            $payment->fresh()->load(['invoice.customer']),
            'Payment verified successfully'
        );
    }

    public function reject(Request $request, Payment $payment): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($payment->status !== 'pending') {
            return $this->errorResponse('Payment is not in pending status', 422);
        }

        $oldData = $payment->toArray();

        $payment->update([
            'status' => 'rejected',
            'notes' => $request->reason,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        AuditLog::log(
            'update',
            'Payment',
            $payment->id,
            $oldData,
            $payment->toArray(),
            "Rejected payment for invoice: {$payment->invoice->invoice_no}"
        );

        return $this->successResponse($payment, 'Payment rejected');
    }
}