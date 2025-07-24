<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\UserSubscription;
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
     * Create payment for subscription
     */
    public function createSubscriptionPayment($user, Subscription $subscription, $paymentData)
    {
        try {
            \Log::info('Creating Moyasar payment', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'amount' => $subscription->price * 100
            ]);

            $response = $this->client->post('payments', [
                'json' => [
                    'amount' => $subscription->price * 100, // Convert to halalas (smallest currency unit)
                    'currency' => 'SAR',
                    'description' => 'اشتراك ' . $subscription->name,
                    'source' => [
                        'type' => 'creditcard',
                        'name' => $paymentData['name'],
                        'number' => $paymentData['number'],
                        'cvc' => $paymentData['cvc'],
                        'month' => $paymentData['month'],
                        'year' => $paymentData['year'],
                    ],
                    'callback_url' => route('payment.callback'),
                    'metadata' => [
                        'user_id' => $user->id,
                        'subscription_id' => $subscription->id,
                        'user_email' => $user->email,
                    ],
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            \Log::info('Moyasar payment response received', [
                'status_code' => $response->getStatusCode(),
                'payment_id' => $result['id'] ?? 'unknown'
            ]);

            return $result;
        } catch (Exception $e) {
            \Log::error('Moyasar payment creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'subscription_id' => $subscription->id
            ]);

            throw new Exception('فشل في إنشاء الدفع: ' . $e->getMessage());
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
                    'callback_url' => route('payment.callback'),
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
            'auto_renew' => true,
            'metadata' => [
                'payment_id' => $payment->id,
                'moyasar_payment_id' => $payment->stripe_payment_intent_id, // Reusing this field for Moyasar ID
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
