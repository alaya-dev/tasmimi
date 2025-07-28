<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Services\Moyasar\PaymentService;
use Exception;

class MoyasarService
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
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

            $data = [
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
                'callback_url' => config('moyasar.finish_payment_url'),
                'metadata' => [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'user_email' => $user->email,
                ],
            ];

            $result = $this->paymentService->create($data);

            \Log::info('Moyasar payment response received', [
                'payment_id' => $result['id'] ?? 'unknown'
            ]);

            return $result;
        } catch (Exception $e) {
            \Log::error('Moyasar payment creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'subscription_id' => $subscription->id
            ]);

            // Parse error message for better user experience
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'source.number') !== false) {
                throw new Exception('رقم البطاقة غير صحيح');
            } elseif (strpos($errorMessage, 'source.cvc') !== false) {
                throw new Exception('رمز الأمان غير صحيح');
            } elseif (strpos($errorMessage, 'source.month') !== false || strpos($errorMessage, 'source.year') !== false) {
                throw new Exception('تاريخ انتهاء البطاقة غير صحيح');
            }

            throw new Exception('فشل في إنشاء الدفع: ' . $errorMessage);
        }
    }

    /**
     * Create payment for template purchase
     */
    public function createTemplatePurchasePayment($user, $template, $paymentData)
    {
        try {
            \Log::info('Creating Moyasar template payment', [
                'user_id' => $user->id,
                'template_id' => $template->id,
                'amount' => $template->price * 100
            ]);

            $data = [
                'amount' => $template->price * 100, // Convert to halalas
                'currency' => 'SAR',
                'description' => 'شراء قالب: ' . $template->name,
                'source' => [
                    'type' => 'creditcard',
                    'name' => $paymentData['name'],
                    'number' => $paymentData['number'],
                    'cvc' => $paymentData['cvc'],
                    'month' => $paymentData['month'],
                    'year' => $paymentData['year'],
                ],
                'callback_url' => url('/client/template-purchase/callback'),
                'metadata' => [
                    'user_id' => $user->id,
                    'template_id' => $template->id,
                    'user_email' => $user->email,
                    'purchase_type' => 'template',
                ],
            ];

            $result = $this->paymentService->create($data);

            \Log::info('Moyasar template payment response received', [
                'payment_id' => $result['id'] ?? 'unknown'
            ]);

            return $result;
        } catch (Exception $e) {
            \Log::error('Moyasar template payment creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'template_id' => $template->id
            ]);

            // Parse error message for better user experience
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'source.number') !== false) {
                throw new Exception('رقم البطاقة غير صحيح أو غير مكتمل. يرجى التحقق من الرقم والمحاولة مرة أخرى.');
            } elseif (strpos($errorMessage, 'source.cvc') !== false) {
                throw new Exception('رمز الأمان (CVC) غير صحيح. يرجى إدخال الرقم المكون من 3 أرقام خلف البطاقة.');
            } elseif (strpos($errorMessage, 'source.month') !== false || strpos($errorMessage, 'source.year') !== false) {
                throw new Exception('تاريخ انتهاء البطاقة غير صحيح. يرجى التحقق من الشهر والسنة.');
            } elseif (strpos($errorMessage, 'source.name') !== false) {
                throw new Exception('اسم حامل البطاقة مطلوب.');
            }

            throw new Exception('فشل في إنشاء الدفع: ' . $errorMessage);
        }
    }

    /**
     * Create payment with token (for saved cards)
     */
    public function createPaymentWithToken($user, Subscription $subscription, $token)
    {
        try {
            $data = [
                'amount' => $subscription->price * 100,
                'currency' => 'SAR',
                'description' => 'اشتراك ' . $subscription->name,
                'source' => [
                    'type' => 'token',
                    'token' => $token,
                ],
                'callback_url' => config('moyasar.finish_payment_url'),
                'metadata' => [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'user_email' => $user->email,
                ],
            ];

            return $this->paymentService->create($data);
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
            return $this->paymentService->fetch($paymentId);
        } catch (Exception $e) {
            throw new Exception('فشل في استرداد معلومات الدفع: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment success
     */
    public function handlePaymentSuccess(Payment $payment)
    {
        // Vérifier si le paiement a déjà été traité
        if ($payment->status === Payment::STATUS_SUCCEEDED) {
            \Log::info('⚠️ Payment already processed, skipping', ['payment_id' => $payment->id]);
            return $payment;
        }

        $payment->update([
            'status' => Payment::STATUS_SUCCEEDED,
            'paid_at' => now(),
        ]);

        // Check if this is a subscription or template purchase
        if ($payment->subscription_id) {
            $this->createUserSubscription($payment);
        } else {
            $this->processTemplatePurchase($payment);
        }

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
        try {
            \Log::info('🚀 Starting subscription creation', [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
                'subscription_id' => $payment->subscription_id
            ]);

            // Vérifier si un abonnement existe déjà pour ce paiement
            $existingSubscription = UserSubscription::where('user_id', $payment->user_id)
                ->whereJsonContains('metadata->payment_id', $payment->id)
                ->first();

            if ($existingSubscription) {
                \Log::info('⚠️ Subscription already exists for this payment', [
                    'payment_id' => $payment->id,
                    'existing_subscription_id' => $existingSubscription->id
                ]);
                return $existingSubscription;
            }

            $subscription = $payment->subscription;
            if (!$subscription) {
                \Log::error('❌ Subscription not found for payment', ['payment_id' => $payment->id]);
                throw new \Exception('Subscription not found');
            }

            $startDate = now();
            $endDate = $subscription->calculateEndDate($startDate);

            \Log::info('📅 Subscription dates calculated', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            // Cancel any existing active subscription
            $canceledCount = UserSubscription::where('user_id', $payment->user_id)
                ->where('status', UserSubscription::STATUS_ACTIVE)
                ->update([
                    'status' => UserSubscription::STATUS_CANCELED,
                    'canceled_at' => now(),
                ]);

            \Log::info('🔄 Canceled existing subscriptions', ['count' => $canceledCount]);

            // Create new subscription
            $userSubscription = UserSubscription::create([
                'user_id' => $payment->user_id,
                'subscription_id' => $payment->subscription_id,
                'status' => UserSubscription::STATUS_ACTIVE,
                'starts_at' => $startDate,
                'ends_at' => $endDate,
                'auto_renew' => false,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'moyasar_payment_id' => $payment->payment_gateway_id,
                ],
            ]);

            \Log::info('✅ User subscription created successfully', [
                'user_subscription_id' => $userSubscription->id,
                'user_id' => $userSubscription->user_id,
                'subscription_id' => $userSubscription->subscription_id,
                'status' => $userSubscription->status
            ]);

            return $userSubscription;

        } catch (\Exception $e) {
            \Log::error('❌ Failed to create user subscription', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Process template purchase after successful payment
     */
    private function processTemplatePurchase(Payment $payment)
    {
        try {
            \Log::info('🛒 Starting template purchase processing', [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id
            ]);

            // Find the template purchase record
            $templatePurchase = \App\Models\TemplatePurchase::where('payment_id', $payment->id)->first();

            if (!$templatePurchase) {
                \Log::error('❌ Template purchase not found for payment', ['payment_id' => $payment->id]);
                throw new \Exception('Template purchase not found');
            }

            // Update template purchase status to paid
            $templatePurchase->update([
                'status' => \App\Models\TemplatePurchase::STATUS_PAID,
                'paid_at' => now(),
            ]);

            \Log::info('✅ Template purchase processed successfully', [
                'template_purchase_id' => $templatePurchase->id,
                'template_id' => $templatePurchase->template_id,
                'user_id' => $templatePurchase->user_id,
                'status' => $templatePurchase->status
            ]);

            return $templatePurchase;

        } catch (\Exception $e) {
            \Log::error('❌ Failed to process template purchase', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
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
