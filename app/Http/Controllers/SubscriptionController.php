<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::orderBy('type')->orderBy('created_at', 'desc')->get();
        
        return Inertia::render('Subscriptions/Index', [
            'subscriptions' => $subscriptions,
            'types' => Subscription::getTypes(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:annual,monthly,weekly',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Subscription::create($request->all());

        return redirect()->back()->with('success', 'تم إنشاء الاشتراك بنجاح');
    }

    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:annual,monthly,weekly',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $subscription->update($request->all());

        return redirect()->back()->with('success', 'تم تحديث الاشتراك بنجاح');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->back()->with('success', 'تم حذف الاشتراك بنجاح');
    }
}
