<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\CustomClientMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ClientMessagingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_client_messaging_page()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        // Create some client users
        $clients = User::factory()->count(3)->create([
            'role' => 'client'
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.client-messaging.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Admin/ClientMessaging/Index')
                ->has('clients', 3)
        );
    }

    public function test_admin_can_send_message_to_client()
    {
        Notification::fake();

        // Create an admin user
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        // Create a client user
        $client = User::factory()->create([
            'role' => 'client',
            'email' => 'client@test.com'
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.client-messaging.send'), [
                'client_id' => $client->id,
                'subject' => 'Test Subject',
                'message' => 'Test message content'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert that the notification was sent
        Notification::assertSentTo(
            [$client],
            CustomClientMessage::class,
            function ($notification) {
                return $notification->subject === 'Test Subject';
            }
        );
    }

    public function test_client_cannot_access_client_messaging()
    {
        $client = User::factory()->create([
            'role' => 'client'
        ]);

        $response = $this->actingAs($client)
            ->get(route('admin.client-messaging.index'));

        $response->assertStatus(403);
    }
}
