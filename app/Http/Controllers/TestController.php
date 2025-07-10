<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestController extends Controller
{
    public function testSubscriptions()
    {
        // Créer quelques abonnements de test si la table est vide
        if (Subscription::count() === 0) {
            $subscriptions = [
                [
                    'name' => 'اشتراك أسبوعي',
                    'type' => 'weekly',
                    'price' => 29.99,
                    'description' => 'اشتراك أسبوعي للوصول إلى جميع القوالب والميزات',
                    'is_active' => true,
                ],
                [
                    'name' => 'اشتراك شهري',
                    'type' => 'monthly',
                    'price' => 99.99,
                    'description' => 'اشتراك شهري للوصول إلى جميع القوالب والميزات مع خصم 15%',
                    'is_active' => true,
                ],
                [
                    'name' => 'اشتراك سنوي',
                    'type' => 'annual',
                    'price' => 999.99,
                    'description' => 'اشتراك سنوي للوصول إلى جميع القوالب والميزات مع خصم 30%',
                    'is_active' => true,
                ],
            ];

            foreach ($subscriptions as $subscription) {
                Subscription::create($subscription);
            }
        }

        $subscriptions = Subscription::all();
        $types = Subscription::getTypes();

        return Inertia::render('Subscriptions/Index', [
            'subscriptions' => $subscriptions,
            'types' => $types,
        ]);
    }
}
