<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\TemplatePurchase;
use Carbon\Carbon;

class InvoiceService
{
    /**
     * Create an invoice for a payment (subscription).
     */
    public function createInvoiceForPayment(Payment $payment): Invoice
    {
        // Check if invoice already exists
        $existingInvoice = Invoice::where('invoiceable_type', Payment::class)
            ->where('invoiceable_id', $payment->id)
            ->first();

        if ($existingInvoice) {
            return $existingInvoice;
        }

        // Determine invoice status based on payment status
        $invoiceStatus = $this->mapPaymentStatusToInvoiceStatus($payment->status);

        // Create invoice
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'user_id' => $payment->user_id,
            'invoiceable_type' => Payment::class,
            'invoiceable_id' => $payment->id,
            'amount' => $payment->amount,
            'currency' => $payment->currency ?? 'SAR',
            'status' => $invoiceStatus,
            'type' => Invoice::TYPE_SUBSCRIPTION,
            'invoice_date' => $payment->created_at ?? now(),
            'due_date' => $payment->created_at ? $payment->created_at->addDays(30) : now()->addDays(30),
            'paid_at' => $payment->paid_at,
            'description' => $this->generateSubscriptionDescription($payment),
            'metadata' => [
                'payment_id' => $payment->id,
                'payment_gateway_id' => $payment->payment_gateway_id ?? null,
                'subscription_id' => $payment->subscription_id,
                'payment_method' => $payment->payment_method ?? null,
            ],
        ]);

        return $invoice;
    }

    /**
     * Create an invoice for a template purchase.
     */
    public function createInvoiceForTemplatePurchase(TemplatePurchase $templatePurchase): Invoice
    {
        // Check if invoice already exists
        $existingInvoice = Invoice::where('invoiceable_type', TemplatePurchase::class)
            ->where('invoiceable_id', $templatePurchase->id)
            ->first();

        if ($existingInvoice) {
            return $existingInvoice;
        }

        // Determine invoice status based on template purchase status
        $invoiceStatus = $this->mapTemplatePurchaseStatusToInvoiceStatus($templatePurchase->status);

        // Create invoice
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'user_id' => $templatePurchase->user_id,
            'invoiceable_type' => TemplatePurchase::class,
            'invoiceable_id' => $templatePurchase->id,
            'amount' => $templatePurchase->amount,
            'currency' => $templatePurchase->currency ?? 'SAR',
            'status' => $invoiceStatus,
            'type' => Invoice::TYPE_TEMPLATE,
            'invoice_date' => $templatePurchase->created_at ?? now(),
            'due_date' => $templatePurchase->created_at ? $templatePurchase->created_at->addDays(30) : now()->addDays(30),
            'paid_at' => $templatePurchase->paid_at,
            'description' => $this->generateTemplateDescription($templatePurchase),
            'metadata' => [
                'template_purchase_id' => $templatePurchase->id,
                'payment_gateway_id' => $templatePurchase->payment_gateway_id ?? null,
                'template_id' => $templatePurchase->template_id,
                'payment_method' => $templatePurchase->payment_method ?? null,
            ],
        ]);

        return $invoice;
    }

    /**
     * Update invoice status when payment status changes.
     */
    public function updateInvoiceForPayment(Payment $payment): ?Invoice
    {
        $invoice = Invoice::where('invoiceable_type', Payment::class)
            ->where('invoiceable_id', $payment->id)
            ->first();

        if (!$invoice) {
            return null;
        }

        $newStatus = $this->mapPaymentStatusToInvoiceStatus($payment->status);
        
        $invoice->update([
            'status' => $newStatus,
            'paid_at' => $payment->paid_at,
        ]);

        return $invoice;
    }

    /**
     * Update invoice status when template purchase status changes.
     */
    public function updateInvoiceForTemplatePurchase(TemplatePurchase $templatePurchase): ?Invoice
    {
        $invoice = Invoice::where('invoiceable_type', TemplatePurchase::class)
            ->where('invoiceable_id', $templatePurchase->id)
            ->first();

        if (!$invoice) {
            return null;
        }

        $newStatus = $this->mapTemplatePurchaseStatusToInvoiceStatus($templatePurchase->status);
        
        $invoice->update([
            'status' => $newStatus,
            'paid_at' => $templatePurchase->paid_at,
        ]);

        return $invoice;
    }

    /**
     * Map payment status to invoice status.
     */
    private function mapPaymentStatusToInvoiceStatus(string $paymentStatus): string
    {
        return match($paymentStatus) {
            'succeeded', 'paid' => Invoice::STATUS_PAID,
            'pending' => Invoice::STATUS_PENDING,
            'failed' => Invoice::STATUS_FAILED,
            'canceled' => Invoice::STATUS_CANCELED,
            'refunded' => Invoice::STATUS_REFUNDED,
            default => Invoice::STATUS_PENDING,
        };
    }

    /**
     * Map template purchase status to invoice status.
     */
    private function mapTemplatePurchaseStatusToInvoiceStatus(string $purchaseStatus): string
    {
        return match($purchaseStatus) {
            'paid' => Invoice::STATUS_PAID,
            'pending' => Invoice::STATUS_PENDING,
            'failed' => Invoice::STATUS_FAILED,
            'refunded' => Invoice::STATUS_REFUNDED,
            default => Invoice::STATUS_PENDING,
        };
    }

    /**
     * Generate description for subscription invoice.
     */
    private function generateSubscriptionDescription(Payment $payment): string
    {
        $subscription = $payment->subscription;
        
        if (!$subscription) {
            return 'دفع اشتراك';
        }

        $description = "اشتراك {$subscription->name}";
        
        if ($subscription->duration_days) {
            $description .= " ({$subscription->duration_days} يوم)";
        } elseif ($subscription->type) {
            $typeLabels = [
                'weekly' => 'أسبوعي',
                'monthly' => 'شهري',
                'yearly' => 'سنوي',
                'annual' => 'سنوي'
            ];
            $typeLabel = $typeLabels[$subscription->type] ?? $subscription->type;
            $description .= " ({$typeLabel})";
        }

        return $description;
    }

    /**
     * Generate description for template purchase invoice.
     */
    private function generateTemplateDescription(TemplatePurchase $templatePurchase): string
    {
        $template = $templatePurchase->template;
        
        if (!$template) {
            return 'شراء قالب';
        }

        $description = "شراء قالب: {$template->name}";
        
        if ($template->category) {
            $description .= " (فئة: {$template->category->name})";
        }

        return $description;
    }
}
