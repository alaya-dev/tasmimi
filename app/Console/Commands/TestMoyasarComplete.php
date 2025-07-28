<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Moyasar\PaymentService;
use Exception;

class TestMoyasarComplete extends Command
{
    protected $signature = 'test:moyasar-complete';
    protected $description = 'Test complete Moyasar payment flow with transaction URL';

    public function handle()
    {
        $this->info('🧪 Testing complete Moyasar payment flow...');
        
        try {
            $paymentService = app(PaymentService::class);
            
            // Create payment with test card
            $this->info('1️⃣ Creating payment with test card...');
            
            $testData = [
                'amount' => 100, // 1 SAR
                'currency' => 'SAR',
                'description' => 'Test complete payment flow',
                'source' => [
                    'type' => 'creditcard',
                    'name' => 'Test User',
                    'number' => '4111111111111111', // Visa test card
                    'cvc' => '123',
                    'month' => '12',
                    'year' => '26'
                ],
                'callback_url' => config('moyasar.finish_payment_url'),
                'metadata' => [
                    'test' => true,
                    'flow' => 'complete'
                ]
            ];
            
            $result = $paymentService->create($testData);
            
            $this->info('✅ Payment created successfully!');
            $this->line('   Payment ID: ' . ($result['id'] ?? 'unknown'));
            $this->line('   Status: ' . ($result['status'] ?? 'unknown'));
            $this->line('   Amount: ' . ($result['amount'] ?? 0) . ' halalas');
            
            // Check if we have transaction URL
            if (isset($result['source']['transaction_url'])) {
                $this->info('2️⃣ Transaction URL generated:');
                $this->line('   URL: ' . $result['source']['transaction_url']);
                
                $this->info('3️⃣ Next steps for complete testing:');
                $this->line('   1. Open the transaction URL in your browser');
                $this->line('   2. Use test card: 4111 1111 1111 1111');
                $this->line('   3. CVC: 123, Expiry: 12/26');
                $this->line('   4. Complete the payment process');
                $this->line('   5. Moyasar will redirect to your callback URL');
                $this->line('   6. Check your logs for the final status');
                
                $this->info('🎯 Expected flow:');
                $this->line('   initiated → [user completes payment] → paid');
                
                // Show callback URL
                $this->info('4️⃣ Callback configuration:');
                $this->line('   Callback URL: ' . config('moyasar.finish_payment_url'));
                $this->line('   Route: /client/payment/callback');
                
                $this->info('🔍 To monitor the callback:');
                $this->line('   tail -f storage/logs/laravel.log');
                
            } else {
                $this->warn('⚠️  No transaction URL found in response');
                $this->line('Response structure:');
                $this->line(json_encode($result, JSON_PRETTY_PRINT));
            }
            
            $this->info('🎉 Test setup completed successfully!');
            $this->info('💡 The "initiated" status is CORRECT - user needs to complete payment via Moyasar interface');
            
            return 0;
            
        } catch (Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            return 1;
        }
    }
}
