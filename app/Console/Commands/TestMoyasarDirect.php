<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Moyasar\PaymentService;
use App\Models\User;
use App\Models\Subscription;
use Exception;

class TestMoyasarDirect extends Command
{
    protected $signature = 'test:moyasar-direct';
    protected $description = 'Test direct Moyasar payment creation (without card details)';

    public function handle()
    {
        $this->info('🧪 Testing direct Moyasar payment creation...');
        
        try {
            // Get test data
            $user = User::first();
            $subscription = Subscription::first();
            
            if (!$user || !$subscription) {
                $this->error('❌ Please ensure you have at least one user and subscription in the database');
                return 1;
            }
            
            $invoiceService = app(\App\Services\Moyasar\InvoiceService::class);

            // Create invoice (hosted payment)
            $this->info('1️⃣ Creating hosted invoice...');

            $invoiceData = [
                'amount' => $subscription->price * 100, // Convert to halalas
                'currency' => 'SAR',
                'description' => 'اشتراك ' . $subscription->name,
                'callback_url' => config('moyasar.finish_payment_url'),
                'metadata' => [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'user_email' => $user->email,
                ],
            ];

            $result = $invoiceService->create($invoiceData);
            
            $this->info('✅ Hosted invoice created successfully!');
            $this->line('   Invoice ID: ' . ($result['id'] ?? 'unknown'));
            $this->line('   Status: ' . ($result['status'] ?? 'unknown'));
            $this->line('   Amount: ' . ($result['amount'] ?? 0) . ' halalas');
            $this->line('   Currency: ' . ($result['currency'] ?? 'SAR'));

            // Show invoice URL
            $invoiceId = $result['id'];
            $invoiceUrl = $result['url'] ?? "https://api.moyasar.com/v1/invoices/{$invoiceId}";

            $this->info('2️⃣ Invoice URL generated:');
            $this->line('   URL: ' . $invoiceUrl);
            
            $this->info('3️⃣ Test URLs:');
            $this->line('   Simple subscription page: ' . url("/client/payment/{$subscription->id}/simple"));
            $this->line('   Direct invoice URL: ' . $invoiceUrl);

            $this->info('4️⃣ How to test:');
            $this->line('   1. Visit the simple subscription page');
            $this->line('   2. Click "Pay with Moyasar" button');
            $this->line('   3. You will be redirected to Moyasar invoice page');
            $this->line('   4. Enter test card: 4111 1111 1111 1111');
            $this->line('   5. CVC: 123, Expiry: 12/26');
            $this->line('   6. Complete payment on Moyasar interface');
            $this->line('   7. Moyasar will redirect back to your callback');

            $this->info('🎯 Expected flow:');
            $this->line('   User clicks pay → Redirected to Moyasar → Pays → Redirected back');

            $this->info('🎉 Direct invoice test setup completed!');
            $this->info('💡 This approach eliminates the need for card form on your site');
            
            return 0;
            
        } catch (Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            return 1;
        }
    }
}
