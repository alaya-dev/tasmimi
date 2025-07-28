<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Moyasar\PaymentService;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Payment;
use Exception;

class TestMoyasarCallback extends Command
{
    protected $signature = 'test:moyasar-callback';
    protected $description = 'Test Moyasar callback flow with real payment creation';

    public function handle()
    {
        $this->info('🧪 Testing Moyasar callback flow...');
        
        try {
            // Create test user and subscription
            $user = User::first();
            $subscription = Subscription::first();
            
            if (!$user || !$subscription) {
                $this->error('❌ Please ensure you have at least one user and subscription in the database');
                return 1;
            }
            
            $paymentService = app(PaymentService::class);
            
            // Create payment
            $this->info('1️⃣ Creating test payment...');
            
            $testData = [
                'amount' => $subscription->price * 100,
                'currency' => 'SAR',
                'description' => 'Test callback flow - ' . $subscription->name,
                'source' => [
                    'type' => 'creditcard',
                    'name' => 'Test User',
                    'number' => '4111111111111111',
                    'cvc' => '123',
                    'month' => '12',
                    'year' => '26'
                ],
                'callback_url' => url('/simulate-callback/PAYMENT_ID'), // Will be replaced
                'metadata' => [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'test' => true
                ]
            ];
            
            $moyasarPayment = $paymentService->create($testData);
            $paymentId = $moyasarPayment['id'];
            
            $this->info("✅ Payment created: {$paymentId}");
            $this->info("   Status: " . $moyasarPayment['status']);
            
            // Create local payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'payment_gateway' => 'moyasar',
                'payment_gateway_id' => $paymentId,
                'amount' => $subscription->price,
                'currency' => 'SAR',
                'status' => 'pending',
                'payment_method' => 'credit_card'
            ]);
            
            $this->info("✅ Local payment record created: {$payment->id}");
            
            // Test callback URL
            $callbackUrl = url("/client/payment/callback?id={$paymentId}");
            $this->info('2️⃣ Testing callback URL...');
            $this->line("   URL: {$callbackUrl}");
            
            // Simulate successful callback
            $this->info('3️⃣ Simulating successful callback...');
            
            // Update payment status to simulate Moyasar success
            $this->info('   Fetching payment from Moyasar...');
            $updatedPayment = $paymentService->fetch($paymentId);
            
            if ($updatedPayment['status'] === 'initiated') {
                $this->warn('   ⚠️  Payment is still "initiated" - this is normal for test cards');
                $this->info('   💡 In real scenario, user would complete payment on Moyasar interface');
                $this->info('   💡 Then Moyasar would call your callback with "paid" status');
            }
            
            // Show simulation URLs
            $this->info('4️⃣ Manual test URLs:');
            $this->line("   Simulate success: " . url("/simulate-callback/{$paymentId}"));
            $this->line("   Direct callback: {$callbackUrl}");
            
            $this->info('5️⃣ To complete the test:');
            $this->line('   1. Visit the simulation URL in your browser');
            $this->line('   2. Or visit the callback URL directly');
            $this->line('   3. Check the payment status in your database');
            
            $this->info('🎉 Test setup completed successfully!');
            
            return 0;
            
        } catch (Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            return 1;
        }
    }
}
