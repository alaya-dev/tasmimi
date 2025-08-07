<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\InvoiceService;

class PaymentObserver
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        // Create invoice when payment is created
        try {
            $this->invoiceService->createInvoiceForPayment($payment);
        } catch (\Exception $e) {
            \Log::error('Failed to create invoice for payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        // Update invoice when payment status changes
        if ($payment->wasChanged('status') || $payment->wasChanged('paid_at')) {
            try {
                $this->invoiceService->updateInvoiceForPayment($payment);
            } catch (\Exception $e) {
                \Log::error('Failed to update invoice for payment', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        // Optionally handle invoice deletion or marking as canceled
        // For now, we'll leave invoices as they are for audit purposes
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }
}
