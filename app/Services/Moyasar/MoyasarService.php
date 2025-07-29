<?php

namespace App\Services\Moyasar;

use App\Models\Payment;
use App\Models\TemplatePurchase;
use App\Models\UserSubscription;
use App\Models\Subscription;
use App\Models\Template;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class MoyasarService
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Create subscription payment
     */
    public function createSubscriptionPayment(User $user, Subscription $subscription, string $callbackUrl): array
    {
        try {
            DB::beginTransaction();

            // Create local payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'payment_gateway' => Payment::GATEWAY_MOYASAR,
                'payment_gateway_id' => 'pending_' . uniqid(),
                'amount' => $subscription->price,
                'currency' => 'SAR',
                'status' => Payment::STATUS_PENDING,
                'description' => "اشتراك {$subscription->name}",
                'metadata' => [
                    'type' => 'subscription',
                    'subscription_id' => $subscription->id,
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]
            ]);

            // Prepare Moyasar config
            $moyasarConfig = $this->paymentService->createPaymentConfig([
                'amount' => $subscription->price * 100, // Convert to halalas
                'currency' => 'SAR',
                'description' => "اشتراك {$subscription->name}",
                'callback_url' => $callbackUrl,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'type' => 'subscription',
                    'user_email' => $user->email,
                ]
            ]);

            DB::commit();

            return [
                'payment' => $payment,
                'moyasar_config' => $moyasarConfig
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Subscription payment creation failed', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create template purchase payment
     */
    public function createTemplatePayment(User $user, Template $template, string $callbackUrl): array
    {
        try {
            DB::beginTransaction();

            // Check if user already purchased this template
            $existingPurchase = TemplatePurchase::where('user_id', $user->id)
                ->where('template_id', $template->id)
                ->where('status', TemplatePurchase::STATUS_PAID)
                ->first();

            if ($existingPurchase) {
                throw new Exception('Template already purchased');
            }

            // Create local payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'template_id' => $template->id,
                'payment_gateway' => Payment::GATEWAY_MOYASAR,
                'payment_gateway_id' => 'pending_' . uniqid(),
                'amount' => $template->price,
                'currency' => 'SAR',
                'status' => Payment::STATUS_PENDING,
                'description' => "شراء قالب: {$template->name}",
                'metadata' => [
                    'type' => 'template',
                    'template_id' => $template->id,
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]
            ]);

            // Create template purchase record
            $templatePurchase = TemplatePurchase::create([
                'user_id' => $user->id,
                'template_id' => $template->id,
                'payment_id' => $payment->id,
                'amount' => $template->price,
                'currency' => 'SAR',
                'status' => TemplatePurchase::STATUS_PENDING,
                'metadata' => [
                    'payment_id' => $payment->id,
                ]
            ]);

            // Prepare Moyasar config
            $moyasarConfig = $this->paymentService->createPaymentConfig([
                'amount' => $template->price * 100, // Convert to halalas
                'currency' => 'SAR',
                'description' => "شراء قالب: {$template->name}",
                'callback_url' => $callbackUrl,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'template_purchase_id' => $templatePurchase->id,
                    'type' => 'template',
                    'user_email' => $user->email,
                ]
            ]);

            DB::commit();

            return [
                'payment' => $payment,
                'template_purchase' => $templatePurchase,
                'moyasar_config' => $moyasarConfig
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Template payment creation failed', [
                'user_id' => $user->id,
                'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle successful subscription payment
     */
    public function handleSubscriptionPaymentSuccess(Payment $payment): void
    {
        try {
            DB::beginTransaction();

            // Mark payment as successful
            $payment->markAsPaid();

            // Create or update user subscription
            $subscription = $payment->subscription;
            $user = $payment->user;

            // Cancel any existing active subscription
            UserSubscription::where('user_id', $user->id)
                ->where('status', UserSubscription::STATUS_ACTIVE)
                ->update([
                    'status' => UserSubscription::STATUS_CANCELED,
                    'canceled_at' => now()
                ]);

            // Create new subscription
            UserSubscription::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'status' => UserSubscription::STATUS_ACTIVE,
                'starts_at' => now(),
                'ends_at' => now()->addDays($subscription->duration_days),
                'auto_renew' => false,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'activated_via' => 'moyasar_payment'
                ]
            ]);

            DB::commit();

            Log::info('Subscription activated successfully', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'payment_id' => $payment->id
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Subscription activation failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle successful template payment
     */
    public function handleTemplatePaymentSuccess(Payment $payment): void
    {
        try {
            DB::beginTransaction();

            // Mark payment as successful
            $payment->markAsPaid();

            // Mark template purchase as successful
            $templatePurchase = TemplatePurchase::where('payment_id', $payment->id)->first();
            if ($templatePurchase) {
                $templatePurchase->markAsPaid();
            }

            DB::commit();

            Log::info('Template purchase completed successfully', [
                'user_id' => $payment->user_id,
                'template_id' => $payment->template_id,
                'payment_id' => $payment->id
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Template purchase completion failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Verify payment with Moyasar API
     */
    public function verifyPayment(string $paymentId): array
    {
        return $this->paymentService->fetch($paymentId);
    }
}
