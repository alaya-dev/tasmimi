<?php

use Illuminate\Support\Facades\Route;
use App\Services\Moyasar\PaymentService;

// Route de test pour vérifier l'intégration Moyasar
Route::get('/test-moyasar', function () {
    try {
        $paymentService = app(PaymentService::class);
        
        // Test data with test card
        $testData = [
            'amount' => 100, // 1 SAR in halalas
            'currency' => 'SAR',
            'description' => 'Test payment',
            'source' => [
                'type' => 'creditcard',
                'name' => 'Test User',
                'number' => '4111111111111111',
                'cvc' => '123',
                'month' => '12',
                'year' => '26'
            ],
            'callback_url' => config('moyasar.finish_payment_url'),
            'metadata' => [
                'test' => true,
                'user_id' => 'test-user'
            ]
        ];
        
        $result = $paymentService->create($testData);
        
        return response()->json([
            'success' => true,
            'message' => 'Moyasar integration working!',
            'payment_id' => $result['id'] ?? 'unknown',
            'status' => $result['status'] ?? 'unknown',
            'amount' => $result['amount'] ?? 0,
            'currency' => $result['currency'] ?? 'SAR'
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'config_check' => [
                'publishable_key' => config('moyasar.publishable_key') ? 'Set' : 'Missing',
                'secret_key' => config('moyasar.secret_key') ? 'Set' : 'Missing',
                'finish_payment_url' => config('moyasar.finish_payment_url') ? 'Set' : 'Missing'
            ]
        ], 500);
    }
});
