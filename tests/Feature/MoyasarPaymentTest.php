<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\Moyasar\PaymentService;
use App\Services\Moyasar\InvoiceService;
use App\Services\MoyasarService;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MoyasarPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_service_can_be_resolved()
    {
        $paymentService = app(PaymentService::class);
        $this->assertInstanceOf(PaymentService::class, $paymentService);
    }

    public function test_invoice_service_can_be_resolved()
    {
        $invoiceService = app(InvoiceService::class);
        $this->assertInstanceOf(InvoiceService::class, $invoiceService);
    }

    public function test_moyasar_service_can_be_resolved()
    {
        $moyasarService = app(MoyasarService::class);
        $this->assertInstanceOf(MoyasarService::class, $moyasarService);
    }

    public function test_moyasar_configuration_is_loaded()
    {
        $this->assertNotNull(config('moyasar.publishable_key'));
        $this->assertNotNull(config('moyasar.secret_key'));
        $this->assertNotNull(config('moyasar.finish_payment_url'));
    }

    public function test_payment_callback_route_exists()
    {
        $response = $this->get('/client/payment/callback?id=test-payment-id');
        
        // Should not return 404 (route exists)
        $this->assertNotEquals(404, $response->status());
    }

    public function test_template_purchase_callback_route_exists()
    {
        $response = $this->get('/client/template-purchase/callback?id=test-payment-id');
        
        // Should not return 404 (route exists)
        $this->assertNotEquals(404, $response->status());
    }
}
