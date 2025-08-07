<?php

namespace App\Observers;

use App\Models\TemplatePurchase;
use App\Services\InvoiceService;

class TemplatePurchaseObserver
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Handle the TemplatePurchase "created" event.
     */
    public function created(TemplatePurchase $templatePurchase): void
    {
        // Create invoice when template purchase is created
        try {
            $this->invoiceService->createInvoiceForTemplatePurchase($templatePurchase);
        } catch (\Exception $e) {
            \Log::error('Failed to create invoice for template purchase', [
                'template_purchase_id' => $templatePurchase->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the TemplatePurchase "updated" event.
     */
    public function updated(TemplatePurchase $templatePurchase): void
    {
        // Update invoice when template purchase status changes
        if ($templatePurchase->wasChanged('status') || $templatePurchase->wasChanged('paid_at')) {
            try {
                $this->invoiceService->updateInvoiceForTemplatePurchase($templatePurchase);
            } catch (\Exception $e) {
                \Log::error('Failed to update invoice for template purchase', [
                    'template_purchase_id' => $templatePurchase->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Handle the TemplatePurchase "deleted" event.
     */
    public function deleted(TemplatePurchase $templatePurchase): void
    {
        // Optionally handle invoice deletion or marking as canceled
        // For now, we'll leave invoices as they are for audit purposes
    }

    /**
     * Handle the TemplatePurchase "restored" event.
     */
    public function restored(TemplatePurchase $templatePurchase): void
    {
        //
    }

    /**
     * Handle the TemplatePurchase "force deleted" event.
     */
    public function forceDeleted(TemplatePurchase $templatePurchase): void
    {
        //
    }
}
