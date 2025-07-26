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
    public function manage()
    {
        $user = Auth::user();
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

        return Inertia::render('Client/SubscriptionManagement', [
            'activeSubscription' => $activeSubscription,
            'subscriptionHistory' => $subscriptionHistory,
            'paymentHistory' => $paymentHistory,
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
