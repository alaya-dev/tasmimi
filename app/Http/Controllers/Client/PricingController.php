<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Inertia\Inertia;

class PricingController extends Controller
{
    public function index()
    {
        // Get all active subscriptions ordered by price
        $subscriptions = Subscription::where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();

        // Get current user's plan if authenticated
        $currentPlan = 'free'; // Default to free
        if (auth()->check()) {
            // You can implement logic to get user's current subscription
            // $currentPlan = auth()->user()->subscription_type ?? 'free';
        }

        return Inertia::render('Client/Pricing', [
            'subscriptions' => $subscriptions,
            'currentPlan' => $currentPlan,
        ]);
    }
}
