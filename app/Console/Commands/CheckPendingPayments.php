<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Services\MoyasarService;
use App\Services\Moyasar\InvoiceService;

class CheckPendingPayments extends Command
{
    protected $signature = 'payments:check-pending';
    protected $description = 'Check pending payments and update their status';

    public function handle()
    {
        $this->info('🔍 Checking pending payments...');
        
        // Get all pending payments from the last 24 hours
        $pendingPayments = Payment::where('status', Payment::STATUS_PENDING)
            ->where('created_at', '>=', now()->subHours(24))
            ->get();

        $this->info("Found {$pendingPayments->count()} pending payments");

        $invoiceService = app(InvoiceService::class);
        $moyasarService = app(MoyasarService::class);
        
        $processed = 0;
        $successful = 0;

        foreach ($pendingPayments as $payment) {
            try {
                $this->line("Checking payment {$payment->id} (Invoice: {$payment->payment_gateway_id})");
                
                // Get invoice status from Moyasar
                $moyasarInvoice = $invoiceService->fetch($payment->payment_gateway_id);
                
                if ($moyasarInvoice['status'] === 'paid') {
                    $this->info("✅ Payment {$payment->id} is PAID - Processing...");
                    
                    // Process successful payment
                    $moyasarService->handlePaymentSuccess($payment);
                    $successful++;
                    
                    $this->info("✅ Payment {$payment->id} processed successfully!");
                    
                } elseif ($moyasarInvoice['status'] === 'failed') {
                    $this->warn("❌ Payment {$payment->id} FAILED");
                    
                    // Process failed payment
                    $moyasarService->handlePaymentFailure($payment, 'Payment failed');
                    
                } else {
                    $this->line("⏳ Payment {$payment->id} still pending (Status: {$moyasarInvoice['status']})");
                }
                
                $processed++;
                
            } catch (\Exception $e) {
                $this->error("❌ Error checking payment {$payment->id}: " . $e->getMessage());
            }
        }

        $this->info("✅ Processed {$processed} payments, {$successful} successful");
        
        return 0;
    }
}
