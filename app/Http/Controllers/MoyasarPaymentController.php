<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Services\MoyasarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Exception;

class MoyasarPaymentController extends Controller
{
    protected $moyasarService;

    public function __construct(MoyasarService $moyasarService)
    {
        $this->moyasarService = $moyasarService;
    }

    /**
     * Show the payment page (redirect to simple Moyasar version)
     */
    public function show(Subscription $subscription)
    {
        // Redirect to the simple Moyasar payment page
        return redirect()->route('client.payment.simple', $subscription);
    }

    /**
     * Show the simple payment page (direct to Moyasar)
     */
    public function showSimple(Subscription $subscription)
    {
        return Inertia::render('Client/MoyasarPaymentSimple', [
            'subscription' => $subscription,
        ]);
    }

    /**
     * Create payment and redirect directly to Moyasar payment page
     */
    public function payWithMoyasar(Request $request, Subscription $subscription)
    {
        \Log::info('PayWithMoyasar method called', [
            'user_id' => auth()->id(),
            'subscription_id' => $subscription->id,
            'request_method' => $request->method()
        ]);

        try {
            $user = auth()->user();

            // Check if user already has this subscription active
            $existingSubscription = UserSubscription::where('user_id', $user->id)
                ->where('subscription_id', $subscription->id)
                ->where('status', UserSubscription::STATUS_ACTIVE)
                ->where('ends_at', '>', now())
                ->first();

            if ($existingSubscription) {
                return redirect()->back()
                    ->withErrors(['payment' => 'لديك اشتراك نشط بالفعل في هذه الخطة']);
            }

            // First create invoice via Moyasar API (hosted payment)
            $invoiceData = [
                'amount' => $subscription->price * 100, // Convert to halalas
                'currency' => 'SAR',
                'description' => 'اشتراك ' . $subscription->name,
                'callback_url' => route('client.payment.verify', ['payment' => '__PAYMENT_ID__']),
                'metadata' => [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'user_email' => $user->email,
                ],
            ];

            // Create local payment record first
            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'payment_gateway' => 'moyasar',
                'payment_gateway_id' => 'temp_' . time(), // Temporary ID
                'amount' => $subscription->price,
                'currency' => 'SAR',
                'status' => Payment::STATUS_PENDING,
                'payment_method' => 'moyasar_invoice',
                'metadata' => json_encode([])
            ]);

            // Update callback URL with real payment ID - redirect to return page
            $invoiceData['callback_url'] = route('client.payment.return', ['type' => 'subscription']);

            // Create invoice using InvoiceService (better for hosted payments)
            $invoiceService = app(\App\Services\Moyasar\InvoiceService::class);
            $moyasarInvoice = $invoiceService->create($invoiceData);

            // Update payment record with Moyasar invoice ID
            $payment->update([
                'payment_gateway_id' => $moyasarInvoice['id'],
                'metadata' => json_encode([
                    'moyasar_status' => $moyasarInvoice['status'],
                    'moyasar_amount' => $moyasarInvoice['amount'],
                ])
            ]);

            \Log::info('Subscription invoice created, redirecting to Moyasar', [
                'payment_id' => $payment->id,
                'moyasar_invoice_id' => $moyasarInvoice['id'],
                'user_id' => $user->id,
                'subscription_id' => $subscription->id
            ]);

            // Redirect directly to Moyasar invoice page in Arabic
            $invoiceUrl = $moyasarInvoice['url']; // Moyasar provides a direct URL for invoices

            // Add Arabic language parameter
            $invoiceUrl .= (strpos($invoiceUrl, '?') !== false ? '&' : '?') . 'lang=ar';

            return redirect()->away($invoiceUrl);

        } catch (Exception $e) {
            \Log::error('Moyasar payment creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'subscription_id' => $subscription->id
            ]);

            return redirect()->back()
                ->withErrors(['payment' => 'فشل في إنشاء الدفع: ' . $e->getMessage()]);
        }
    }

    /**
     * Process payment for subscription
     */
    public function processPayment(Request $request, Subscription $subscription)
    {
        \Log::info('Payment process started', [
            'user_id' => Auth::id(),
            'subscription_id' => $subscription->id,
            'payment_method' => $request->payment_method
        ]);

        $request->validate([
            'payment_method' => 'required|in:creditcard,stcpay,applepay',
            'card_name' => 'required_if:payment_method,creditcard|string|max:255',
            'card_number' => 'required_if:payment_method,creditcard|string',
            'card_cvc' => 'required_if:payment_method,creditcard|string|size:3',
            'card_month' => 'required_if:payment_method,creditcard|integer|between:1,12',
            'card_year' => 'required_if:payment_method,creditcard|integer|min:' . date('Y'),
            'token' => 'nullable|required_if:payment_method,stcpay|required_if:payment_method,applepay|string',
        ]);

        \Log::info('Payment data validated successfully');

        $user = Auth::user();

        // Check if user already has this subscription active
        $existingSubscription = UserSubscription::where('user_id', $user->id)
            ->where('subscription_id', $subscription->id)
            ->where('status', UserSubscription::STATUS_ACTIVE)
            ->where('ends_at', '>', now())
            ->first();

        if ($existingSubscription) {
            \Log::info('User already has active subscription', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id
            ]);

            return response()->json([
                'error' => 'لديك اشتراك نشط بالفعل في هذه الخطة'
            ], 400);
        }

        try {
            \Log::info('Creating payment record');

            // Create payment record first
            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'amount' => $subscription->price,
                'currency' => 'SAR',
                'status' => Payment::STATUS_PENDING,
                'payment_method' => $request->payment_method,
            ]);

            \Log::info('Payment record created', ['payment_id' => $payment->id]);

            // Process payment with Moyasar
            if ($request->payment_method === 'creditcard') {
                \Log::info('Processing credit card payment');

                $moyasarPayment = $this->moyasarService->createSubscriptionPayment($user, $subscription, [
                    'name' => $request->card_name,
                    'number' => $request->card_number,
                    'cvc' => $request->card_cvc,
                    'month' => $request->card_month,
                    'year' => $request->card_year,
                ]);
            } else {
                \Log::info('Processing token payment', ['payment_method' => $request->payment_method]);

                $moyasarPayment = $this->moyasarService->createPaymentWithToken($user, $subscription, $request->token);
            }

            \Log::info('Moyasar payment created', ['moyasar_payment_id' => $moyasarPayment['id'] ?? 'unknown']);

            // Update payment with Moyasar ID
            $payment->update([
                'payment_gateway_id' => $moyasarPayment['id'], // Moyasar payment ID
            ]);

            // Check payment status
            if ($moyasarPayment['status'] === 'paid') {
                // Payment is completed
                $this->moyasarService->handlePaymentSuccess($payment);

                if (request()->header('X-Inertia')) {
                    return redirect()->route('client.subscription.manage')
                        ->with('success', 'تم الدفع بنجاح! تم تفعيل اشتراكك.');
                }

                return response()->json([
                    'success' => true,
                    'message' => 'تم الدفع بنجاح',
                    'redirect' => route('client.subscription.manage')
                ]);
            } elseif ($moyasarPayment['status'] === 'initiated') {
                // Payment is created but needs to be completed via Moyasar interface
                \Log::info('Subscription payment initiated, redirecting to Moyasar', [
                    'payment_id' => $moyasarPayment['id'],
                    'moyasar_status' => $moyasarPayment['status']
                ]);

                // Redirect to Moyasar payment page
                if (isset($moyasarPayment['source']['transaction_url'])) {
                    // For Inertia requests, redirect directly to the payment URL
                    return redirect()->away($moyasarPayment['source']['transaction_url']);
                }

                // Fallback: redirect back with message
                return redirect()->back()->with('info', 'تم إنشاء الدفع بنجاح. يرجى إتمام عملية الدفع.');
            } elseif ($moyasarPayment['status'] === 'failed') {
                $errorMessage = $moyasarPayment['source']['message'] ?? 'فشل الدفع';
                $this->moyasarService->handlePaymentFailure($payment, $errorMessage);

                // Check if it's an Inertia request
                if (request()->header('X-Inertia')) {
                    return back()->withErrors(['payment' => 'فشل في معالجة الدفع: ' . $errorMessage]);
                }

                return response()->json([
                    'error' => 'فشل في معالجة الدفع: ' . $errorMessage
                ], 400);
            } else {
                // Payment is pending or requires additional action
                // Check if it's an Inertia request
                if (request()->header('X-Inertia')) {
                    return back()->with('info', 'الدفع قيد المعالجة، يرجى الانتظار...');
                }

                return response()->json([
                    'pending' => true,
                    'payment_id' => $moyasarPayment['id'],
                    'message' => 'الدفع قيد المعالجة'
                ]);
            }

        } catch (Exception $e) {
            \Log::error('Payment processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'subscription_id' => $subscription->id
            ]);

            if (isset($payment)) {
                $this->moyasarService->handlePaymentFailure($payment, $e->getMessage());
            }

            // Check if it's an Inertia request
            if (request()->header('X-Inertia')) {
                return back()->withErrors(['payment' => $e->getMessage()]);
            }

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle payment callback from Moyasar
     */
    public function callback(Request $request)
    {
        // Get payment ID from URL parameters (Moyasar sends it as 'id')
        $paymentId = $request->get('id');
        $status = $request->get('status');
        $amount = $request->get('amount');
        $message = $request->get('message');

        \Log::info('Payment callback received', [
            'payment_id' => $paymentId,
            'status' => $status,
            'amount' => $amount,
            'message' => $message,
            'all_params' => $request->all()
        ]);

        if (!$paymentId) {
            return $this->renderPaymentResult(false, 'معرف الدفع مفقود');
        }

        try {
            // The paymentId from Moyasar is actually the invoice ID
            // Find payment record by invoice ID
            $payment = Payment::where('payment_gateway_id', $paymentId)->first();

            if (!$payment) {
                \Log::error('Payment not found', [
                    'invoice_id' => $paymentId,
                    'status' => $status,
                    'amount' => $amount
                ]);
                return $this->renderPaymentResult(false, 'لم يتم العثور على معلومات الدفع');
            }

            \Log::info('Payment found', [
                'payment_id' => $payment->id,
                'invoice_id' => $paymentId,
                'status' => $status
            ]);

            // Check payment status from URL parameter
            if ($status === 'paid') {
                \Log::info('Processing successful payment', ['payment_id' => $payment->id]);
                $this->moyasarService->handlePaymentSuccess($payment);

                // Determine redirect URL based on payment type
                if ($payment->subscription_id) {
                    // Subscription payment - redirect to subscription management
                    return $this->renderPaymentResult(true, 'تم الدفع بنجاح! تم تفعيل اشتراكك.', route('client.subscription.manage'));
                } else {
                    // Template purchase - redirect to template page
                    $templatePurchase = \App\Models\TemplatePurchase::where('payment_id', $payment->id)->first();
                    if ($templatePurchase) {
                        return $this->renderPaymentResult(true, 'تم شراء القالب بنجاح! يمكنك الآن استخدامه.', route('client.templates.create', $templatePurchase->template_id));
                    }
                    return $this->renderPaymentResult(true, 'تم الدفع بنجاح!', route('client.templates'));
                }
            } else {
                \Log::info('Processing failed payment', ['payment_id' => $payment->id, 'status' => $status]);
                $this->moyasarService->handlePaymentFailure($payment, $message ?? 'فشل الدفع');
                return $this->renderPaymentResult(false, 'فشل في معالجة الدفع: ' . ($message ?? 'فشل الدفع'));
            }

        } catch (Exception $e) {
            \Log::error('Payment callback error', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            return $this->renderPaymentResult(false, 'حدث خطأ أثناء معالجة الدفع');
        }
    }

    /**
     * Render payment result page
     */
    private function renderPaymentResult($success, $message, $redirectUrl = null)
    {
        $defaultRedirectUrl = $success ? route('client.subscription.manage') : route('client.subscriptions.index');

        return Inertia::render('Client/PaymentResult', [
            'success' => $success,
            'message' => $message,
            'redirectUrl' => $redirectUrl ?? $defaultRedirectUrl
        ]);
    }

    /**
     * Show payment verification page
     */
    public function showVerification(Payment $payment)
    {
        return Inertia::render('Client/PaymentVerification', [
            'payment' => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'status' => $payment->status,
            ]
        ]);
    }

    /**
     * Show payment return page
     */
    public function showReturn($type)
    {
        return Inertia::render('Client/PaymentReturn', [
            'type' => $type
        ]);
    }

    /**
     * Check recent payments for current user
     */
    public function checkRecentPayments(Request $request)
    {
        try {
            $user = auth()->user();

            \Log::info('🔍 Checking recent payments', ['user_id' => $user->id]);

            // Get recent pending payments (last 10 minutes)
            $recentPayments = Payment::where('user_id', $user->id)
                ->where('status', Payment::STATUS_PENDING)
                ->where('created_at', '>=', now()->subMinutes(10))
                ->get();

            \Log::info('📋 Found recent payments', [
                'count' => $recentPayments->count(),
                'payments' => $recentPayments->map(fn($p) => [
                    'id' => $p->id,
                    'gateway_id' => $p->payment_gateway_id,
                    'status' => $p->status,
                    'created_at' => $p->created_at
                ])
            ]);

            $processed = 0;
            $redirectUrl = route('client.subscription.manage');

            foreach ($recentPayments as $payment) {
                try {
                    \Log::info('🔍 Checking payment', [
                        'payment_id' => $payment->id,
                        'gateway_id' => $payment->payment_gateway_id
                    ]);

                    // Skip payments with temporary IDs
                    if (str_starts_with($payment->payment_gateway_id, 'temp_')) {
                        \Log::info('⏭️ Skipping payment with temporary ID', [
                            'payment_id' => $payment->id,
                            'gateway_id' => $payment->payment_gateway_id
                        ]);
                        continue;
                    }

                    // Get invoice status from Moyasar
                    $invoiceService = app(\App\Services\Moyasar\InvoiceService::class);
                    $moyasarInvoice = $invoiceService->fetch($payment->payment_gateway_id);

                    \Log::info('📄 Invoice status fetched', [
                        'payment_id' => $payment->id,
                        'moyasar_status' => $moyasarInvoice['status']
                    ]);

                    if ($moyasarInvoice['status'] === 'paid') {
                        \Log::info('✅ Processing paid payment', ['payment_id' => $payment->id]);

                        $this->moyasarService->handlePaymentSuccess($payment);
                        $processed++;

                        // Determine redirect URL
                        if ($payment->subscription_id) {
                            $redirectUrl = route('client.subscription.manage');
                        } else {
                            $redirectUrl = route('client.templates');
                        }

                        \Log::info('🎯 Payment processed successfully', [
                            'payment_id' => $payment->id,
                            'redirect_url' => $redirectUrl
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('❌ Recent payment check failed', [
                        'payment_id' => $payment->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => $processed > 0,
                'processed' => $processed,
                'redirectUrl' => $redirectUrl
            ]);

        } catch (\Exception $e) {
            \Log::error('Check recent payments failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'فشل في التحقق من الدفع'
            ], 500);
        }
    }

    /**
     * Verify payment status manually (when callback doesn't work)
     */
    public function verifyPayment(Payment $payment)
    {
        try {
            \Log::info('Manual payment verification started', ['payment_id' => $payment->id]);

            // Get invoice status from Moyasar
            $invoiceService = app(\App\Services\Moyasar\InvoiceService::class);
            $moyasarInvoice = $invoiceService->fetch($payment->payment_gateway_id);

            \Log::info('Invoice status fetched', [
                'payment_id' => $payment->id,
                'invoice_id' => $payment->payment_gateway_id,
                'status' => $moyasarInvoice['status']
            ]);

            if ($moyasarInvoice['status'] === 'paid') {
                \Log::info('Processing successful payment via verification', ['payment_id' => $payment->id]);
                $this->moyasarService->handlePaymentSuccess($payment);

                // Determine redirect URL based on payment type
                if ($payment->subscription_id) {
                    return $this->renderPaymentResult(true, 'تم الدفع بنجاح! تم تفعيل اشتراكك.', route('client.subscription.manage'));
                } else {
                    $templatePurchase = \App\Models\TemplatePurchase::where('payment_id', $payment->id)->first();
                    if ($templatePurchase) {
                        return $this->renderPaymentResult(true, 'تم شراء القالب بنجاح! يمكنك الآن استخدامه.', route('client.templates.create', $templatePurchase->template_id));
                    }
                    return $this->renderPaymentResult(true, 'تم الدفع بنجاح!', route('client.templates'));
                }
            } else {
                return $this->renderPaymentResult(false, 'الدفع لم يكتمل بعد أو فشل');
            }

        } catch (Exception $e) {
            \Log::error('Payment verification error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            return $this->renderPaymentResult(false, 'حدث خطأ أثناء التحقق من الدفع');
        }
    }

    /**
     * Check payment status
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|string',
        ]);

        try {
            $moyasarPayment = $this->moyasarService->getPayment($request->payment_id);

            return response()->json([
                'status' => $moyasarPayment['status'],
                'message' => $this->getStatusMessage($moyasarPayment['status'])
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'فشل في التحقق من حالة الدفع'
            ], 500);
        }
    }

    /**
     * Get status message in Arabic
     */
    private function getStatusMessage($status)
    {
        return match($status) {
            'paid' => 'تم الدفع بنجاح',
            'failed' => 'فشل الدفع',
            'pending' => 'الدفع قيد المعالجة',
            'authorized' => 'تم تفويض الدفع',
            'captured' => 'تم استلام الدفع',
            default => 'حالة غير معروفة'
        };
    }
}
