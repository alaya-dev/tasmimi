<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MoyasarRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_payment_redirects_to_moyasar()
    {
        // Create test data
        $user = User::factory()->create();
        $subscription = Subscription::factory()->create([
            'name' => 'Test Subscription',
            'price' => 100
        ]);

        // Simulate payment request
        $response = $this->actingAs($user)->post(route('client.subscriptions.payment', $subscription->id), [
            'payment_method' => 'creditcard',
            'card_name' => 'Test User',
            'card_number' => '4111111111111111',
            'card_cvc' => '123',
            'card_month' => 12,
            'card_year' => 2026,
        ]);

        // Should redirect (302) to external Moyasar URL
        $this->assertEquals(302, $response->status());
        
        // Check that it's redirecting to Moyasar domain
        $location = $response->headers->get('Location');
        $this->assertStringContains('moyasar.com', $location);
    }

    public function test_template_purchase_redirects_to_moyasar()
    {
        // Create test data
        $user = User::factory()->create();
        $template = Template::factory()->create([
            'name' => 'Test Template',
            'price' => 50
        ]);

        // Simulate payment request
        $response = $this->actingAs($user)->post(route('client.templates.purchase.process', $template->id), [
            'card_name' => 'Test User',
            'card_number' => '4111111111111111',
            'card_cvc' => '123',
            'card_month' => 12,
            'card_year' => 2026,
        ]);

        // Should redirect (302) to external Moyasar URL
        $this->assertEquals(302, $response->status());
        
        // Check that it's redirecting to Moyasar domain
        $location = $response->headers->get('Location');
        $this->assertStringContains('moyasar.com', $location);
    }
}
