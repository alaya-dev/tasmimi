<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::ordered()
                                   ->orderBy('created_at', 'desc')
                                   ->get();

        return Inertia::render('Subscriptions/Index', [
            'subscriptions' => $subscriptions,
            'types' => Subscription::getTypes(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:annual,monthly,weekly,custom',
            'duration_days' => 'nullable|integer|min:1|required_if:type,custom',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_popular' => 'boolean',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();

        // Set default color if not provided
        if (empty($data['color'])) {
            $data['color'] = '#8D39EB';
        }

        // Filter out empty features
        if (isset($data['features'])) {
            $data['features'] = array_filter($data['features'], function($feature) {
                return !empty(trim($feature));
            });
        }

        Subscription::create($data);

        return redirect()->back()->with('success', 'تم إنشاء الاشتراك بنجاح');
    }

    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:annual,monthly,weekly,custom',
            'duration_days' => 'nullable|integer|min:1|required_if:type,custom',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_popular' => 'boolean',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();

        // Set default color if not provided
        if (empty($data['color'])) {
            $data['color'] = '#8D39EB';
        }

        // Filter out empty features
        if (isset($data['features'])) {
            $data['features'] = array_filter($data['features'], function($feature) {
                return !empty(trim($feature));
            });
        }

        $subscription->update($data);

        return redirect()->back()->with('success', 'تم تحديث الاشتراك بنجاح');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->back()->with('success', 'تم حذف الاشتراك بنجاح');
    }
}
