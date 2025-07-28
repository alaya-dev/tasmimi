<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Moyasar\PaymentService;
use Exception;

class TestMoyasar extends Command
{
    protected $signature = 'test:moyasar';
    protected $description = 'Test Moyasar integration with test card';

    public function handle()
    {
        $this->info('Testing Moyasar integration...');
        
        try {
            $paymentService = app(PaymentService::class);
            
            // Test configuration
            $this->info('Checking configuration...');
            $publishableKey = config('moyasar.publishable_key');
            $secretKey = config('moyasar.secret_key');
            $finishUrl = config('moyasar.finish_payment_url');
            
            if (!$publishableKey) {
                $this->error('Publishable key is missing!');
                return 1;
            }
            
            if (!$secretKey) {
                $this->error('Secret key is missing!');
                return 1;
            }
            
            if (!$finishUrl) {
                $this->error('Finish payment URL is missing!');
                return 1;
            }
            
            $this->info("✓ Publishable key: " . substr($publishableKey, 0, 10) . "...");
            $this->info("✓ Secret key: " . substr($secretKey, 0, 10) . "...");
            $this->info("✓ Finish URL: " . $finishUrl);
            
            // Test payment creation with test card
            $this->info('Creating test payment...');
            
            $testData = [
                'amount' => 100, // 1 SAR in halalas
                'currency' => 'SAR',
                'description' => 'Test payment from Laravel command',
                'source' => [
                    'type' => 'creditcard',
                    'name' => 'Test User',
                    'number' => '4111111111111111', // Visa test card
                    'cvc' => '123',
                    'month' => '12',
                    'year' => '26'
                ],
                'callback_url' => $finishUrl,
                'metadata' => [
                    'test' => true,
                    'command' => 'test:moyasar'
                ]
            ];
            
            $result = $paymentService->create($testData);
            
            $this->info('✓ Payment created successfully!');
            $this->info("Payment ID: " . ($result['id'] ?? 'unknown'));
            $this->info("Status: " . ($result['status'] ?? 'unknown'));
            $this->info("Amount: " . ($result['amount'] ?? 0) . " halalas");
            $this->info("Currency: " . ($result['currency'] ?? 'SAR'));

            // Show transaction URL if available
            if (isset($result['source']['transaction_url'])) {
                $this->info("Transaction URL: " . $result['source']['transaction_url']);
                $this->info("💡 For 'initiated' status, user needs to visit this URL to complete payment");
            }
            
            // Test payment fetch
            if (isset($result['id'])) {
                $this->info('Fetching payment details...');
                $fetchedPayment = $paymentService->fetch($result['id']);
                $this->info("✓ Payment fetched successfully!");
                $this->info("Fetched status: " . ($fetchedPayment['status'] ?? 'unknown'));
            }
            
            $this->info('🎉 Moyasar integration test completed successfully!');
            return 0;
            
        } catch (Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            return 1;
        }
    }
}
