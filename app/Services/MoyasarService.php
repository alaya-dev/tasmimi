<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Models\TemplatePurchase;
use GuzzleHttp\Client;
use Exception;

class MoyasarService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.moyasar.secret_key');
        $this->baseUrl = config('services.moyasar.base_url', 'https://api.moyasar.com/v1/');

        $clientConfig = [
            'base_uri' => $this->baseUrl,
            'auth' => [$this->apiKey, ''],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ];

        // Disable SSL verification in development
        if (config('app.env') === 'local') {
            $clientConfig['verify'] = false;
        }

        $this->client = new Client($clientConfig);
    }

    /**
     * Create payment configuration for subscription (using Moyasar Form)
     */
    public function createSubscriptionPaymentConfig($user, Subscription $subscription)
    {
        return [
            'amount' => $subscription->price * 100, // Convert to halalas
            'currency' => 'SAR',
            'description' => 'اشتراك ' . $subscription->name,
            'publishable_api_key' => config('services.moyasar.publishable_key'),
            'callback_url' => url('/client/payment/callback'),
            'methods' => ['creditcard', 'stcpay'], // Removed Apple Pay
            'metadata' => [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'user_email' => $user->email,
                'payment_type' => 'subscription',
            ],
        ];
    }

    /**
     * Create payment configuration for template purchase (using Moyasar Form)
     */
    public function createTemplatePurchasePaymentConfig($user, $template)
    {
        return [
            'amount' => $template->price * 100, // Convert to halalas
            'currency' => 'SAR',
            'description' => 'شراء قالب: ' . $template->name,
            'publishable_api_key' => config('services.moyasar.publishable_key'),
            'callback_url' => url('/client/template-purchase/callback'),
            'methods' => ['creditcard', 'stcpay'], // Removed Apple Pay
            'metadata' => [
                'user_id' => $user->id,
                'template_id' => $template->id,
                'user_email' => $user->email,
                'payment_type' => 'template',
            ],
        ];
    }

    /**
     * Save payment ID for tracking (called from frontend after Moyasar form completion)
     */
    public function savePaymentIdForSubscription($user, Subscription $subscription, $paymentId)
    {
        try {
            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'amount' => $subscription->price,
                'currency' => 'SAR',
                'status' => Payment::STATUS_PENDING,
                'payment_gateway_id' => $paymentId,
                'payment_method' => 'moyasar_form',
            ]);

            \Log::info('Payment ID saved for subscription', [
                'payment_id' => $payment->id,
                'moyasar_payment_id' => $paymentId,
                'user_id' => $user->id,
                'subscription_id' => $subscription->id
            ]);

            return $payment;
        } catch (Exception $e) {
            \Log::error('Failed to save payment ID for subscription', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'moyasar_payment_id' => $paymentId
            ]);
            throw $e;
        }
    }

    /**
     * Save payment ID for template purchase (called from frontend after Moyasar form completion)
     */
    public function savePaymentIdForTemplate($user, $template, $paymentId)
    {
        try {
            // Check if purchase already exists
            $purchase = TemplatePurchase::where('user_id', $user->id)
                ->where('template_id', $template->id)
                ->where('status', '!=', TemplatePurchase::STATUS_PAID)
                ->first();

            if (!$purchase) {
                $purchase = TemplatePurchase::create([
                    'user_id' => $user->id,
                    'template_id' => $template->id,
                    'amount' => $template->price,
                    'currency' => 'SAR',
                    'status' => TemplatePurchase::STATUS_PENDING,
                ]);
            }

            // Update with payment gateway ID
            $purchase->update([
                'payment_gateway_id' => $paymentId,
                'metadata' => ['moyasar_payment_id' => $paymentId],
            ]);

            \Log::info('Payment ID saved for template purchase', [
                'purchase_id' => $purchase->id,
                'moyasar_payment_id' => $paymentId,
                'user_id' => $user->id,
                'template_id' => $template->id
            ]);

            return $purchase;
        } catch (Exception $e) {
            \Log::error('Failed to save payment ID for template', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'template_id' => $template->id,
                'moyasar_payment_id' => $paymentId
            ]);
            throw $e;
        }
    }

    /**
     * Create payment with token (for saved cards)
     */
    public function createPaymentWithToken($user, Subscription $subscription, $token)
    {
        try {
            $response = $this->client->post('payments', [
                'json' => [
                    'amount' => $subscription->price * 100,
                    'currency' => 'SAR',
                    'description' => 'اشتراك ' . $subscription->name,
                    'source' => [
                        'type' => 'token',
                        'token' => $token,
                    ],
                    'callback_url' => url('/client/payment/callback'),
                    'metadata' => [
                        'user_id' => $user->id,
                        'subscription_id' => $subscription->id,
                        'user_email' => $user->email,
                    ],
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('فشل في إنشاء الدفع: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve payment by ID
     */
    public function getPayment($paymentId)
    {
        try {
            $response = $this->client->get("payments/{$paymentId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('فشل في استرداد معلومات الدفع: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment success
     */
    public function handlePaymentSuccess(Payment $payment)
    {
        $payment->update([
            'status' => Payment::STATUS_SUCCEEDED,
            'paid_at' => now(),
        ]);

        $this->createUserSubscription($payment);

        return $payment;
    }

    /**
     * Handle payment failure
     */
    public function handlePaymentFailure(Payment $payment, $errorMessage = null)
    {
        $payment->update([
            'status' => Payment::STATUS_FAILED,
            'metadata' => array_merge($payment->metadata ?? [], [
                'error_message' => $errorMessage,
                'failed_at' => now()->toISOString(),
            ]),
        ]);

        return $payment;
    }

    /**
     * Create user subscription after successful payment
     */
    private function createUserSubscription(Payment $payment)
    {
        $subscription = $payment->subscription;
        $startDate = now();
        $endDate = $subscription->calculateEndDate($startDate);

        // Cancel any existing active subscription
        UserSubscription::where('user_id', $payment->user_id)
            ->where('status', UserSubscription::STATUS_ACTIVE)
            ->update([
                'status' => UserSubscription::STATUS_CANCELED,
                'canceled_at' => now(),
            ]);

        // Create new subscription
        return UserSubscription::create([
            'user_id' => $payment->user_id,
            'subscription_id' => $payment->subscription_id,
            'status' => UserSubscription::STATUS_ACTIVE,
            'starts_at' => $startDate,
            'ends_at' => $endDate,
            'auto_renew' => false,
            'metadata' => [
                'payment_id' => $payment->id,
                'moyasar_payment_id' => $payment->payment_gateway_id, // Moyasar payment ID
            ],
        ]);
    }

    /**
     * Calculate prorated amount for plan change
     */
    public function calculateProratedAmount(UserSubscription $currentSubscription, Subscription $newSubscription)
    {
        $remainingDays = $currentSubscription->daysRemaining();
        $currentPlan = $currentSubscription->subscription;
        
        $currentDuration = $currentPlan->duration_days ?: $this->getDefaultDuration($currentPlan->type);
        $newDuration = $newSubscription->duration_days ?: $this->getDefaultDuration($newSubscription->type);
        
        $dailyRateOld = $currentPlan->price / $currentDuration;
        $dailyRateNew = $newSubscription->price / $newDuration;
        
        $refundAmount = $dailyRateOld * $remainingDays;
        $newAmount = max(0, $newSubscription->price - $refundAmount);

        return [
            'refund_amount' => $refundAmount,
            'new_amount' => $newAmount,
            'remaining_days' => $remainingDays,
        ];
    }

    /**
     * Get default duration for subscription type
     */
    private function getDefaultDuration($type)
    {
        return match($type) {
            'weekly' => 7,
            'monthly' => 30,
            'annual' => 365,
            default => 30,
        };
    }

    /**
     * Refund payment
     */
    public function refundPayment($paymentId, $amount = null)
    {
        try {
            $payment = $this->getPayment($paymentId);

            // Moyasar refund logic
            $refundAmount = $amount ? $amount * 100 : $payment['amount'];

            $response = $this->client->post("payments/{$paymentId}/refund", [
                'json' => [
                    'amount' => $refundAmount,
                    'description' => 'استرداد الاشتراك'
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('فشل في معالجة الاسترداد: ' . $e->getMessage());
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        $webhookSecret = config('services.moyasar.webhook_secret');
        
        if (!$webhookSecret) {
            return true; // Skip verification if no secret is set
        }

        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Verify payment by ID with Moyasar API
     */
    public function verifyPayment($paymentId)
    {
        try {
            $response = $this->client->get("payments/{$paymentId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('فشل في التحقق من الدفع: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful subscription payment
     */
    public function handleSubscriptionPaymentSuccess(Payment $payment)
    {
        // Update payment status
        $payment->update([
            'status' => Payment::STATUS_SUCCEEDED,
            'paid_at' => now(),
        ]);

        // Create user subscription
        $this->createUserSubscription($payment);

        \Log::info('Subscription payment processed successfully', [
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'subscription_id' => $payment->subscription_id
        ]);

        return $payment;
    }

    /**
     * Handle successful template payment
     */
    public function handleTemplatePaymentSuccess(Payment $payment)
    {
        // Update payment status
        $payment->update([
            'status' => Payment::STATUS_SUCCEEDED,
            'paid_at' => now(),
        ]);

        // Update or create template purchase record
        if ($payment->template_id) {
            $purchase = TemplatePurchase::where('user_id', $payment->user_id)
                ->where('template_id', $payment->template_id)
                ->first();

            if ($purchase) {
                $purchase->update([
                    'status' => TemplatePurchase::STATUS_PAID,
                    'payment_gateway_id' => $payment->payment_gateway_id,
                ]);
            } else {
                TemplatePurchase::create([
                    'user_id' => $payment->user_id,
                    'template_id' => $payment->template_id,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'status' => TemplatePurchase::STATUS_PAID,
                    'payment_gateway_id' => $payment->payment_gateway_id,
                ]);
            }
        }

        \Log::info('Template payment processed successfully', [
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'template_id' => $payment->template_id
        ]);

        return $payment;
    }

    /**
     * Get payment methods available in Saudi Arabia
     */
    public function getAvailablePaymentMethods()
    {
        return [
            'creditcard' => [
                'name' => 'بطاقة ائتمان',
                'description' => 'فيزا، ماستركارد، مدى',
                'icon' => 'credit-card'
            ],
            'stcpay' => [
                'name' => 'STC Pay',
                'description' => 'محفظة STC Pay الرقمية',
                'icon' => 'stc-pay'
            ],
            'applepay' => [
                'name' => 'Apple Pay',
                'description' => 'الدفع عبر Apple Pay',
                'icon' => 'apple-pay'
            ],
        ];
    }
}
