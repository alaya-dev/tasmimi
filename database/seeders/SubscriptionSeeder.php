<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
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
}
