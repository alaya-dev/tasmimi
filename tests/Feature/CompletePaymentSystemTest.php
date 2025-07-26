<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\UserSubscription;
use App\Services\MoyasarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;

class CompletePaymentSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $subscription;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'role' => 'client'
        ]);

        // Create test subscription
        $this->subscription = Subscription::create([
            'name' => 'Plan Test',
            'type' => 'monthly',
            'duration_days' => 30,
            'price' => 100.00,
            'description' => 'Plan de test',
            'features' => ['Feature 1', 'Feature 2'],
            'sort_order' => 1,
            'is_active' => true,
            'is_popular' => false,
            'color' => '#007bff'
        ]);
    }

    /** @test */
    public function test_subscription_creation()
    {
        $this->assertDatabaseHas('subscriptions', [
            'name' => 'Plan Test',
            'price' => 100.00,
            'is_active' => true
        ]);

        echo "✓ Test création d'abonnement: RÉUSSI\n";
    }

    /** @test */
    public function test_subscription_page_loads()
    {
        $response = $this->actingAs($this->user)
            ->get('/client/subscriptions');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Client/Subscriptions')
                ->has('subscriptions')
        );

        echo "✓ Test chargement page abonnements: RÉUSSI\n";
    }

    /** @test */
    public function test_payment_page_loads()
    {
        $response = $this->actingAs($this->user)
            ->get("/client/payment/{$this->subscription->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Client/MoyasarPayment')
                ->has('subscription')
        );

        echo "✓ Test chargement page paiement: RÉUSSI\n";
    }

    /** @test */
    public function test_payment_validation()
    {
        // Test validation des données de carte
        $response = $this->actingAs($this->user)
            ->post("/client/subscriptions/{$this->subscription->id}/payment", [
                'payment_method' => 'creditcard',
                // Données manquantes
            ]);

        $response->assertSessionHasErrors(['card_name', 'card_number', 'card_cvc']);

        echo "✓ Test validation paiement: RÉUSSI\n";
    }

    /** @test */
    public function test_payment_data_structure()
    {
        $paymentData = [
            'payment_method' => 'creditcard',
            'card_name' => 'Test User',
            'card_number' => '4242424242424242',
            'card_cvc' => '123',
            'card_month' => 12,
            'card_year' => 2025
        ];

        $response = $this->actingAs($this->user)
            ->post("/client/subscriptions/{$this->subscription->id}/payment", $paymentData);

        // Vérifier que la validation passe
        $response->assertSessionHasNoErrors();

        echo "✓ Test structure données paiement: RÉUSSI\n";
    }

    /** @test */
    public function test_payment_record_creation()
    {
        // Mock du service Moyasar pour éviter les appels API réels
        $this->mock(MoyasarService::class, function ($mock) {
            $mock->shouldReceive('createSubscriptionPayment')
                ->once()
                ->andReturn([
                    'id' => 'test_payment_id',
                    'status' => 'initiated',
                    'amount' => 10000
                ]);
            
            $mock->shouldReceive('handlePaymentSuccess')
                ->once();
        });

        $paymentData = [
            'payment_method' => 'creditcard',
            'card_name' => 'Test User',
            'card_number' => '4242424242424242',
            'card_cvc' => '123',
            'card_month' => 12,
            'card_year' => 2025
        ];

        $response = $this->actingAs($this->user)
            ->post("/client/subscriptions/{$this->subscription->id}/payment", $paymentData);

        // Vérifier qu'un enregistrement de paiement a été créé
        $this->assertDatabaseHas('payments', [
            'user_id' => $this->user->id,
            'subscription_id' => $this->subscription->id,
            'payment_method' => 'creditcard',
            'amount' => 100.00,
            'currency' => 'SAR'
        ]);

        echo "✓ Test création enregistrement paiement: RÉUSSI\n";
    }

    /** @test */
    public function test_duplicate_subscription_prevention()
    {
        // Créer un abonnement actif existant
        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_id' => $this->subscription->id,
            'status' => UserSubscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'auto_renew' => false
        ]);

        $paymentData = [
            'payment_method' => 'creditcard',
            'card_name' => 'Test User',
            'card_number' => '4242424242424242',
            'card_cvc' => '123',
            'card_month' => 12,
            'card_year' => 2025
        ];

        $response = $this->actingAs($this->user)
            ->post("/client/subscriptions/{$this->subscription->id}/payment", $paymentData);

        $response->assertSessionHasErrors(['payment']);

        echo "✓ Test prévention doublons abonnement: RÉUSSI\n";
    }

    /** @test */
    public function test_subscription_management_page()
    {
        $response = $this->actingAs($this->user)
            ->get('/client/subscription/manage');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Client/SubscriptionManagement')
        );

        echo "✓ Test page gestion abonnement: RÉUSSI\n";
    }

    /** @test */
    public function test_moyasar_service_configuration()
    {
        $service = app(MoyasarService::class);
        
        $this->assertNotNull($service);
        
        // Vérifier que la configuration est chargée
        $this->assertNotEmpty(config('services.moyasar.secret_key'));
        $this->assertNotEmpty(config('services.moyasar.publishable_key'));

        echo "✓ Test configuration service Moyasar: RÉUSSI\n";
    }

    /** @test */
    public function test_payment_constants()
    {
        // Vérifier que les constantes de statut existent
        $this->assertEquals('pending', Payment::STATUS_PENDING);
        $this->assertEquals('succeeded', Payment::STATUS_SUCCEEDED);
        $this->assertEquals('failed', Payment::STATUS_FAILED);
        $this->assertEquals('canceled', Payment::STATUS_CANCELED);

        echo "✓ Test constantes paiement: RÉUSSI\n";
    }

    /** @test */
    public function test_user_subscription_constants()
    {
        // Vérifier que les constantes d'abonnement existent
        $this->assertEquals('active', UserSubscription::STATUS_ACTIVE);
        $this->assertEquals('canceled', UserSubscription::STATUS_CANCELED);
        $this->assertEquals('expired', UserSubscription::STATUS_EXPIRED);

        echo "✓ Test constantes abonnement utilisateur: RÉUSSI\n";
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
