<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Template;
use App\Models\Category;
use App\Models\ClientTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientTemplateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $client;
    protected $template;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer une catégorie
        $this->category = Category::create([
            'name' => 'Test Category',
            'is_active' => true
        ]);

        // Créer un template
        $this->template = Template::create([
            'name' => 'Test Template',
            'category_id' => $this->category->id,
            'design_data' => json_encode([
                'elements' => [],
                'canvas' => ['width' => 800, 'height' => 600]
            ]),
            'editable_elements' => ['text', 'image'],
            'canvas_size' => '800x600',
            'is_active' => true,
            'created_by' => 1
        ]);

        // Créer un client
        $this->client = User::create([
            'email' => 'client@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_CLIENT,
            'email_verified_at' => now()
        ]);
    }

    /** @test */
    public function client_can_access_my_designs_page()
    {
        $response = $this->actingAs($this->client)
            ->get(route('client.my-designs'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Client/MyDesigns')
                ->has('designs')
        );
    }

    /** @test */
    public function client_can_create_design_from_template()
    {
        $response = $this->actingAs($this->client)
            ->get(route('client.templates.create', $this->template));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Client/DesignEditor')
                ->has('template')
                ->where('mode', 'create')
        );
    }

    /** @test */
    public function client_can_save_new_design()
    {
        $designData = [
            'template_id' => $this->template->id,
            'name' => 'Mon nouveau design',
            'design_data' => json_encode([
                'elements' => [
                    [
                        'id' => 1,
                        'type' => 'text',
                        'content' => 'Test text',
                        'x' => 100,
                        'y' => 100
                    ]
                ],
                'canvas' => ['width' => 800, 'height' => 600],
                'watermark' => [
                    'text' => 'سامقة للتصميم',
                    'enabled' => true,
                    'removable' => false
                ]
            ]),
            'canvas_size' => '800x600',
            'notes' => 'Test notes'
        ];

        $response = $this->actingAs($this->client)
            ->postJson(route('client.designs.store'), $designData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('client_templates', [
            'user_id' => $this->client->id,
            'template_id' => $this->template->id,
            'name' => 'Mon nouveau design'
        ]);
    }

    /** @test */
    public function client_can_edit_existing_design()
    {
        $clientTemplate = ClientTemplate::create([
            'user_id' => $this->client->id,
            'template_id' => $this->template->id,
            'name' => 'Test Design',
            'design_data' => json_encode([
                'elements' => [],
                'watermark' => [
                    'text' => 'سامقة للتصميم',
                    'enabled' => true,
                    'removable' => false
                ]
            ]),
            'canvas_size' => '800x600'
        ]);

        $response = $this->actingAs($this->client)
            ->get(route('client.designs.edit', $clientTemplate));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Client/DesignEditor')
                ->has('clientTemplate')
                ->where('mode', 'edit')
        );
    }

    /** @test */
    public function client_can_update_design()
    {
        $clientTemplate = ClientTemplate::create([
            'user_id' => $this->client->id,
            'template_id' => $this->template->id,
            'name' => 'Test Design',
            'design_data' => json_encode(['elements' => []]),
            'canvas_size' => '800x600'
        ]);

        $updateData = [
            'name' => 'Updated Design',
            'design_data' => json_encode([
                'elements' => [
                    [
                        'id' => 1,
                        'type' => 'text',
                        'content' => 'Updated text'
                    ]
                ],
                'watermark' => [
                    'text' => 'سامقة للتصميم',
                    'enabled' => true,
                    'removable' => false
                ]
            ]),
            'notes' => 'Updated notes'
        ];

        $response = $this->actingAs($this->client)
            ->putJson(route('client.designs.update', $clientTemplate), $updateData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $clientTemplate->refresh();
        $this->assertEquals('Updated Design', $clientTemplate->name);
        $this->assertEquals(2, $clientTemplate->version);
    }

    /** @test */
    public function client_can_delete_design()
    {
        $clientTemplate = ClientTemplate::create([
            'user_id' => $this->client->id,
            'template_id' => $this->template->id,
            'name' => 'Test Design',
            'design_data' => json_encode(['elements' => []]),
            'canvas_size' => '800x600'
        ]);

        $response = $this->actingAs($this->client)
            ->deleteJson(route('client.designs.destroy', $clientTemplate));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('client_templates', [
            'id' => $clientTemplate->id
        ]);
    }

    /** @test */
    public function client_can_duplicate_design()
    {
        $clientTemplate = ClientTemplate::create([
            'user_id' => $this->client->id,
            'template_id' => $this->template->id,
            'name' => 'Original Design',
            'design_data' => json_encode(['elements' => []]),
            'canvas_size' => '800x600'
        ]);

        $response = $this->actingAs($this->client)
            ->postJson(route('client.designs.duplicate', $clientTemplate));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('client_templates', [
            'user_id' => $this->client->id,
            'template_id' => $this->template->id,
            'name' => 'Original Design - نسخة'
        ]);
    }

    /** @test */
    public function client_can_export_design()
    {
        $clientTemplate = ClientTemplate::create([
            'user_id' => $this->client->id,
            'template_id' => $this->template->id,
            'name' => 'Test Design',
            'design_data' => json_encode([
                'elements' => [],
                'watermark' => [
                    'text' => 'سامقة للتصميم',
                    'enabled' => true,
                    'removable' => false
                ]
            ]),
            'canvas_size' => '800x600'
        ]);

        $exportData = [
            'format' => 'png',
            'quality' => 90
        ];

        $response = $this->actingAs($this->client)
            ->postJson(route('client.designs.export', $clientTemplate), $exportData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'export_data' => [
                'design_data',
                'format',
                'quality',
                'filename',
                'watermark'
            ]
        ]);
    }

    /** @test */
    public function watermark_is_always_applied_to_client_designs()
    {
        $clientTemplate = ClientTemplate::create([
            'user_id' => $this->client->id,
            'template_id' => $this->template->id,
            'name' => 'Test Design',
            'design_data' => json_encode(['elements' => []]),
            'canvas_size' => '800x600'
        ]);

        $designDataWithWatermark = $clientTemplate->design_data_with_watermark;

        $this->assertArrayHasKey('watermark', $designDataWithWatermark);
        $this->assertEquals('سامقة للتصميم', $designDataWithWatermark['watermark']['text']);
        $this->assertTrue($designDataWithWatermark['watermark']['enabled']);
        $this->assertFalse($designDataWithWatermark['watermark']['removable']);
        $this->assertEquals('0.6', $designDataWithWatermark['watermark']['style']['opacity']);
    }

    /** @test */
    public function client_cannot_access_other_clients_designs()
    {
        $otherClient = User::create([
            'email' => 'other@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_CLIENT,
            'email_verified_at' => now()
        ]);

        $otherClientTemplate = ClientTemplate::create([
            'user_id' => $otherClient->id,
            'template_id' => $this->template->id,
            'name' => 'Other Client Design',
            'design_data' => json_encode(['elements' => []]),
            'canvas_size' => '800x600'
        ]);

        $response = $this->actingAs($this->client)
            ->get(route('client.designs.edit', $otherClientTemplate));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_cannot_access_client_design_routes()
    {
        $admin = User::create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now()
        ]);

        $response = $this->actingAs($admin)
            ->get(route('client.my-designs'));

        $response->assertRedirect(route('admin.dashboard'));
    }
}
