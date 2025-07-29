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
        $user = Auth::user();
        $paymentConfig = $this->moyasarService->createSubscriptionPaymentConfig($user, $subscription);

        return Inertia::render('Client/MoyasarPayment', [
            'subscription' => $subscription,
            'paymentConfig' => $paymentConfig,
        ]);
    }

    /**
     * Save payment ID after Moyasar form completion (called from frontend)
     */
    public function savePaymentId(Request $request, Subscription $subscription)
    {
        $request->validate([
            'payment_id' => 'required|string',
        ]);

        $user = Auth::user();

        try {
            $payment = $this->moyasarService->savePaymentIdForSubscription(
                $user, 
                $subscription, 
                $request->payment_id
            );

            return response()->json([
                'success' => true,
                'payment_id' => $payment->id,
                'message' => 'تم حفظ معرف الدفع بنجاح'
            ]);
        } catch (Exception $e) {
            \Log::error('Failed to save payment ID', [
                'error' => $e->getMessage(),
                'payment_id' => $request->payment_id,
                'user_id' => $user->id,
                'subscription_id' => $subscription->id
            ]);

            return response()->json([
                'error' => 'فشل في حفظ معرف الدفع'
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
