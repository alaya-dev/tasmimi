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
                    'auto_renew' => $userSubscription->auto_renew,
                ];
            });

        return Inertia::render('Admin/ClientSubscriptions', [
            'clientSubscriptions' => $clientSubscriptions
        ]);
    }
}
