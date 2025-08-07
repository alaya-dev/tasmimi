<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

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
     * Show the form for creating a new client subscription.
     */
    public function create()
    {
        // Get all clients (users with role 'client')
        $clients = User::where('role', 'client')
            ->select('id', 'email', 'phone')
            ->orderBy('email')
            ->get();

        // Get all active subscription plans
        $subscriptions = Subscription::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/ClientSubscriptions/Create', [
            'clients' => $clients,
            'subscriptions' => $subscriptions
        ]);
    }

    /**
     * Store a newly created client subscription.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_id' => 'required|exists:subscriptions,id',
            'status' => 'required|in:active,pending,canceled,expired',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'auto_renew' => 'boolean'
        ]);

        // Verify the user is a client
        $user = User::findOrFail($request->user_id);
        if ($user->role !== 'client') {
            return redirect()->back()->withErrors(['user_id' => 'المستخدم المحدد ليس عميلاً']);
        }

        // Check if user already has an active subscription
        $existingActiveSubscription = UserSubscription::where('user_id', $request->user_id)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();

        if ($existingActiveSubscription) {
            return redirect()->back()->withErrors(['user_id' => 'العميل لديه اشتراك نشط بالفعل']);
        }

        // Create the subscription
        UserSubscription::create([
            'user_id' => $request->user_id,
            'subscription_id' => $request->subscription_id,
            'status' => $request->status,
            'starts_at' => Carbon::parse($request->starts_at),
            'ends_at' => Carbon::parse($request->ends_at),
            'auto_renew' => $request->boolean('auto_renew', false),
            'stripe_subscription_id' => null, // Admin created subscriptions don't have Stripe IDs
        ]);

        return redirect()->route('admin.client-subscriptions.index')
            ->with('success', 'تم إنشاء الاشتراك بنجاح');
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
