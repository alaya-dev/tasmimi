<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientSubscriptionController extends Controller
{
    public function index()
    {
        $clientSubscriptions = UserSubscription::with(['user', 'subscription'])
            ->whereHas('user', function ($query) {
                $query->where('role', 'client');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($userSubscription) {
                return [
                    'id' => $userSubscription->id,
                    'client_email' => $userSubscription->user->email,
                    'client_name' => $userSubscription->user->name ?? $userSubscription->user->email,
                    'subscription_name' => $userSubscription->subscription->name,
                    'subscription_type' => $userSubscription->subscription->type,
                    'duration_days' => $userSubscription->subscription->duration_days,
                    'status' => $userSubscription->status,
                    'starts_at' => $userSubscription->starts_at,
                    'ends_at' => $userSubscription->ends_at,
                    'created_at' => $userSubscription->created_at,
                ];
            });

        // Calculate statistics
        $stats = [
            'total' => $clientSubscriptions->count(),
            'active' => $clientSubscriptions->where('status', 'active')->count(),
            'pending' => $clientSubscriptions->where('status', 'pending')->count(),
            'canceled' => $clientSubscriptions->where('status', 'canceled')->count(),
            'expired' => $clientSubscriptions->where('status', 'expired')->count(),
        ];

        return Inertia::render('Admin/ClientSubscriptions', [
            'clientSubscriptions' => $clientSubscriptions,
            'stats' => $stats
        ]);
    }

    /**
     * Delete a client subscription
     */
    public function destroy(UserSubscription $subscription)
    {
        // Check if subscription can be deleted (expired, canceled, or pending)
        if (!in_array($subscription->status, ['expired', 'canceled', 'pending'])) {
            return redirect()->back()->with('error', 'لا يمكن حذف الاشتراكات النشطة');
        }

        // Ensure it's a client subscription
        if ($subscription->user->role !== 'client') {
            return redirect()->back()->with('error', 'غير مسموح بحذف هذا الاشتراك');
        }

        $subscription->delete();

        return redirect()->back()->with('success', 'تم حذف الاشتراك بنجاح');
    }
}
