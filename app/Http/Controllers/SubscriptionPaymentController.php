<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Http\Controllers\MoyasarPaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SubscriptionPaymentController extends Controller
{
    /**
     * Show subscription plans for purchase
     */
    public function index()
    {
        $subscriptions = Subscription::active()
            ->ordered()
            ->get();

        $user = Auth::user();
        $activeSubscription = $user->activeSubscription();

        return Inertia::render('Client/Subscriptions', [
            'subscriptions' => $subscriptions,
            'activeSubscription' => $activeSubscription,
            'currentPlan' => $activeSubscription ? $activeSubscription->subscription : null,
        ]);
    }

    /**
     * Show payment page for a specific subscription
     */
    public function show(Subscription $subscription)
    {
        if (!$subscription->is_active) {
            return redirect()->route('client.subscriptions.index')
                ->with('error', 'هذا الاشتراك غير متاح حالياً');
        }

        $user = Auth::user();
        $activeSubscription = $user->activeSubscription();

        return Inertia::render('Client/SubscriptionPayment', [
            'subscription' => $subscription,
            'activeSubscription' => $activeSubscription,
            'moyasarKey' => config('services.moyasar.publishable_key'),
        ]);
    }



    /**
     * Show user's subscription management page
     */
    public function manage(Request $request)
    {
        $user = Auth::user();

        // Check if user came from payment success
        if ($request->has('payment_success') && $request->has('payment_id')) {
            $paymentId = $request->get('payment_id');

            // Trigger payment verification in background
            try {
                $payment = \App\Models\Payment::find($paymentId);
                if ($payment && $payment->status === \App\Models\Payment::STATUS_PENDING) {
                    // Verify payment asynchronously
                    dispatch(function() use ($payment) {
                        $moyasarService = app(\App\Services\MoyasarService::class);
                        $invoiceService = app(\App\Services\Moyasar\InvoiceService::class);

                        try {
                            $moyasarInvoice = $invoiceService->fetch($payment->payment_gateway_id);
                            if ($moyasarInvoice['status'] === 'paid') {
                                $moyasarService->handlePaymentSuccess($payment);
                            }
                        } catch (\Exception $e) {
                            \Log::error('Background payment verification failed', [
                                'payment_id' => $payment->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    });
                }
            } catch (\Exception $e) {
                \Log::error('Payment verification trigger failed', ['error' => $e->getMessage()]);
            }
        }
        $activeSubscription = $user->activeSubscription();
        $subscriptionHistory = $user->userSubscriptions()
            ->with('subscription')
            ->orderBy('created_at', 'desc')
            ->get();

        $paymentHistory = $user->payments()
            ->with('subscription')
            ->where('status', Payment::STATUS_SUCCEEDED)
            ->orderBy('created_at', 'desc')
            ->get();

        // Debug logging
        \Log::info('🔍 Subscription Management Data', [
            'user_id' => $user->id,
            'activeSubscription' => $activeSubscription ? [
                'id' => $activeSubscription->id,
                'status' => $activeSubscription->status,
                'subscription_id' => $activeSubscription->subscription_id,
                'subscription_name' => $activeSubscription->subscription?->name
            ] : null,
            'subscriptionHistory_count' => $subscriptionHistory->count(),
            'paymentHistory_count' => $paymentHistory->count()
        ]);

        // Simplify data to avoid potential serialization issues
        $activeSubscriptionData = $activeSubscription ? [
            'id' => $activeSubscription->id,
            'status' => $activeSubscription->status,
            'starts_at' => $activeSubscription->starts_at?->toISOString(),
            'ends_at' => $activeSubscription->ends_at?->toISOString(),
            'subscription' => $activeSubscription->subscription ? [
                'id' => $activeSubscription->subscription->id,
                'name' => $activeSubscription->subscription->name,
                'price' => $activeSubscription->subscription->price,
                'duration_days' => $activeSubscription->subscription->duration_days,
            ] : null
        ] : null;

        $subscriptionHistoryData = $subscriptionHistory->map(function($sub) {
            return [
                'id' => $sub->id,
                'status' => $sub->status,
                'starts_at' => $sub->starts_at?->toISOString(),
                'ends_at' => $sub->ends_at?->toISOString(),
                'created_at' => $sub->created_at?->toISOString(),
                'subscription' => $sub->subscription ? [
                    'id' => $sub->subscription->id,
                    'name' => $sub->subscription->name,
                    'price' => $sub->subscription->price,
                ] : null
            ];
        });

        $paymentHistoryData = $paymentHistory->map(function($payment) {
            return [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'status' => $payment->status,
                'payment_method' => $payment->payment_method,
                'paid_at' => $payment->paid_at?->toISOString(),
                'created_at' => $payment->created_at?->toISOString(),
                'subscription' => $payment->subscription ? [
                    'id' => $payment->subscription->id,
                    'name' => $payment->subscription->name,
                ] : null
            ];
        });

        return Inertia::render('Client/SubscriptionManagement', [
            'activeSubscription' => $activeSubscriptionData,
            'subscriptionHistory' => $subscriptionHistoryData,
            'paymentHistory' => $paymentHistoryData,
        ]);
    }

    /**
     * Cancel user's active subscription
     */
    public function cancel(Request $request)
    {
        $user = Auth::user();
        $activeSubscription = $user->activeSubscription();

        if (!$activeSubscription) {
            return response()->json([
                'error' => 'لا يوجد اشتراك نشط للإلغاء'
            ], 400);
        }

        $activeSubscription->cancel();

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الاشتراك بنجاح'
        ]);
    }

    /**
     * Upgrade/downgrade subscription
     */
    public function changePlan(Request $request, Subscription $newSubscription)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        $user = Auth::user();
        $activeSubscription = $user->activeSubscription();

        if (!$activeSubscription) {
            return response()->json([
                'error' => 'لا يوجد اشتراك نشط لتغييره'
            ], 400);
        }

        if ($activeSubscription->subscription_id === $newSubscription->id) {
            return response()->json([
                'error' => 'أنت مشترك بالفعل في هذه الخطة'
            ], 400);
        }

        // Calculate prorated amount
        $remainingDays = $activeSubscription->daysRemaining();
        $currentPlan = $activeSubscription->subscription;

        $dailyRateOld = $currentPlan->price / ($currentPlan->duration_days ?: 30);
        $dailyRateNew = $newSubscription->price / ($newSubscription->duration_days ?: 30);

        $refundAmount = $dailyRateOld * $remainingDays;
        $newAmount = max(0, $newSubscription->price - $refundAmount);

        // If the new amount is 0 or negative, just switch plans
        if ($newAmount <= 0) {
            $activeSubscription->cancel();

            $startDate = now();
            $endDate = $newSubscription->calculateEndDate($startDate);

            UserSubscription::create([
                'user_id' => $user->id,
                'subscription_id' => $newSubscription->id,
                'status' => UserSubscription::STATUS_ACTIVE,
                'starts_at' => $startDate,
                'ends_at' => $endDate,
                'auto_renew' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تغيير الخطة بنجاح'
            ]);
        }

        // For now, redirect to payment page for the new subscription
        return response()->json([
            'redirect' => route('client.payment.show', $newSubscription->id)
        ]);
    }
}
