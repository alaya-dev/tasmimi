<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\WebhookEvent;
use App\Services\MoyasarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MoyasarWebhookController extends Controller
{
    protected $moyasarService;

    public function __construct(MoyasarService $moyasarService)
    {
        $this->moyasarService = $moyasarService;
    }

    /**
     * Handle Moyasar webhook events
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Moyasar-Signature');

        // Verify webhook signature
        if (!$this->moyasarService->verifyWebhookSignature($payload, $signature)) {
            Log::error('Invalid Moyasar webhook signature');
            return response('Invalid signature', 400);
        }

        $data = json_decode($payload, true);

        if (!$data || !isset($data['type']) || !isset($data['data'])) {
            Log::error('Invalid Moyasar webhook payload', ['payload' => $payload]);
            return response('Invalid payload', 400);
        }

        Log::info('Moyasar webhook received', [
            'type' => $data['type'],
            'id' => $data['data']['id'] ?? 'unknown'
        ]);

        // Store webhook event
        $webhookEvent = WebhookEvent::firstOrCreate(
            ['stripe_event_id' => $data['data']['id'] ?? uniqid()], // Reusing this field for Moyasar
            [
                'type' => $data['type'],
                'data' => $data['data'],
                'status' => WebhookEvent::STATUS_PENDING,
            ]
        );

        // Skip if already processed
        if ($webhookEvent->status === WebhookEvent::STATUS_PROCESSED) {
            Log::info('Webhook event already processed', ['event_id' => $data['data']['id'] ?? 'unknown']);
            return response('Event already processed', 200);
        }

        try {
            // Handle the event
            $this->processWebhookEvent($data['type'], $data['data']);

            // Mark as processed
            $webhookEvent->markAsProcessed();

        } catch (\Exception $e) {
            Log::error('Error processing Moyasar webhook event', [
                'event_id' => $data['data']['id'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            $webhookEvent->markAsFailed($e->getMessage());
        }

        return response('Webhook handled', 200);
    }

    /**
     * Process individual webhook event
     */
    private function processWebhookEvent($eventType, $eventData)
    {
        switch ($eventType) {
            case 'payment_paid':
                $this->handlePaymentPaid($eventData);
                break;

            case 'payment_failed':
                $this->handlePaymentFailed($eventData);
                break;

            case 'payment_refunded':
                $this->handlePaymentRefunded($eventData);
                break;

            default:
                Log::info('Unhandled Moyasar webhook event', ['type' => $eventType]);
        }
    }

    /**
     * Handle successful payment
     */
    private function handlePaymentPaid($paymentData)
    {
        $payment = Payment::where('payment_gateway_id', $paymentData['id'])->first();

        if (!$payment) {
            Log::warning('Payment not found for paid Moyasar payment', [
                'payment_id' => $paymentData['id']
            ]);
            return;
        }

        if ($payment->status !== Payment::STATUS_SUCCEEDED) {
            $this->moyasarService->handlePaymentSuccess($payment);

            Log::info('Payment marked as succeeded via Moyasar webhook', [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
                'amount' => $payment->amount
            ]);
        }
    }

    /**
     * Handle failed payment
     */
    private function handlePaymentFailed($paymentData)
    {
        $payment = Payment::where('payment_gateway_id', $paymentData['id'])->first();

        if (!$payment) {
            Log::warning('Payment not found for failed Moyasar payment', [
                'payment_id' => $paymentData['id']
            ]);
            return;
        }

        $errorMessage = $paymentData['source']['message'] ?? 'Payment failed';
        $this->moyasarService->handlePaymentFailure($payment, $errorMessage);

        Log::info('Payment marked as failed via Moyasar webhook', [
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'error' => $errorMessage
        ]);
    }

    /**
     * Handle payment refund
     */
    private function handlePaymentRefunded($paymentData)
    {
        $payment = Payment::where('payment_gateway_id', $paymentData['id'])->first();

        if (!$payment) {
            Log::warning('Payment not found for refunded Moyasar payment', [
                'payment_id' => $paymentData['id']
            ]);
            return;
        }

        $payment->update([
            'status' => 'refunded',
            'metadata' => array_merge($payment->metadata ?? [], [
                'refunded_at' => now()->toISOString(),
                'refund_amount' => $paymentData['refunded_amount'] ?? $payment->amount,
            ])
        ]);

        Log::info('Payment marked as refunded via Moyasar webhook', [
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'refund_amount' => $paymentData['refunded_amount'] ?? $payment->amount
        ]);
    }
}
