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
     * Show the payment form for a subscription
     */
    public function show(Subscription $subscription)
    {
        return Inertia::render('Client/MoyasarPayment', [
            'subscription' => $subscription,
            'moyasarKey' => config('services.moyasar.publishable_key'),
            'paymentMethods' => $this->moyasarService->getAvailablePaymentMethods(),
        ]);
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
                $this->moyasarService->handlePaymentSuccess($payment);

                // Check if it's an Inertia request
                if (request()->header('X-Inertia')) {
                    return redirect()->route('client.subscription.manage')
                        ->with('success', 'تم الدفع بنجاح! تم تفعيل اشتراكك.');
                }

                return response()->json([
                    'success' => true,
                    'message' => 'تم الدفع بنجاح',
                    'redirect' => route('client.subscription.manage')
                ]);
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
        $paymentId = $request->get('id');

        if (!$paymentId) {
            return redirect()->route('client.subscriptions.index')
                ->with('error', 'معرف الدفع مفقود');
        }

        try {
            $moyasarPayment = $this->moyasarService->getPayment($paymentId);
            $payment = Payment::where('stripe_payment_intent_id', $paymentId)->first();

            if (!$payment) {
                return redirect()->route('client.subscriptions.index')
                    ->with('error', 'لم يتم العثور على معلومات الدفع');
            }

            if ($moyasarPayment['status'] === 'paid') {
                $this->moyasarService->handlePaymentSuccess($payment);

                return redirect()->route('client.subscription.manage')
                    ->with('success', 'تم الدفع بنجاح! تم تفعيل اشتراكك.');
            } else {
                $errorMessage = $moyasarPayment['source']['message'] ?? 'فشل الدفع';
                $this->moyasarService->handlePaymentFailure($payment, $errorMessage);

                return redirect()->route('client.subscriptions.index')
                    ->with('error', 'فشل في معالجة الدفع: ' . $errorMessage);
            }

        } catch (Exception $e) {
            return redirect()->route('client.subscriptions.index')
                ->with('error', 'حدث خطأ أثناء معالجة الدفع');
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
