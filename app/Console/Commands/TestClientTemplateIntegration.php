<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Template;
use App\Models\Category;
use App\Models\ClientTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestClientTemplateIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:client-templates {--cleanup : Clean up test data after testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the complete client template integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Testing Client Template Integration...');
        $this->newLine();

        try {
            // Test 1: Database Structure
            $this->testDatabaseStructure();

            // Test 2: Models and Relationships
            $this->testModelsAndRelationships();

            // Test 3: Watermark System
            $this->testWatermarkSystem();

            // Test 4: Routes
            $this->testRoutes();

            // Test 5: Controllers
            $this->testControllers();

            // Cleanup if requested
            if ($this->option('cleanup')) {
                $this->cleanup();
            }

            $this->newLine();
            $this->info('✅ All tests passed! Client template integration is working correctly.');

        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }

    protected function testDatabaseStructure()
    {
        $this->info('📊 Testing database structure...');

        // Check if client_templates table exists
        if (!DB::getSchemaBuilder()->hasTable('client_templates')) {
            throw new \Exception('Table client_templates does not exist');
        }

        // Check required columns
        $requiredColumns = [
            'id', 'user_id', 'template_id', 'name', 'design_data',
            'editable_elements', 'canvas_size', 'thumbnail', 'notes',
            'version', 'last_edited_at', 'created_at', 'updated_at'
        ];

        foreach ($requiredColumns as $column) {
            if (!DB::getSchemaBuilder()->hasColumn('client_templates', $column)) {
                throw new \Exception("Column {$column} does not exist in client_templates table");
            }
        }

        $this->line('  ✓ client_templates table structure is correct');
    }

    protected function testModelsAndRelationships()
    {
        $this->info('🔗 Testing models and relationships...');

        // Create test data
        $category = Category::create([
            'name' => 'Test Category Integration',
            'is_active' => true
        ]);

        $template = Template::create([
            'name' => 'Test Template Integration',
            'category_id' => $category->id,
            'design_data' => json_encode([
                'elements' => [],
                'canvas' => ['width' => 800, 'height' => 600]
            ]),
            'editable_elements' => ['text', 'image'],
            'canvas_size' => '800x600',
            'is_active' => true,
            'created_by' => 1
        ]);

        $client = User::create([
            'email' => 'test-integration@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_CLIENT,
            'email_verified_at' => now()
        ]);

        $clientTemplate = ClientTemplate::create([
            'user_id' => $client->id,
            'template_id' => $template->id,
            'name' => 'Test Client Template',
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
                'canvas' => ['width' => 800, 'height' => 600]
            ]),
            'canvas_size' => '800x600',
            'notes' => 'Test notes'
        ]);

        // Test relationships
        if (!$clientTemplate->user) {
            throw new \Exception('ClientTemplate -> User relationship not working');
        }

        if (!$clientTemplate->template) {
            throw new \Exception('ClientTemplate -> Template relationship not working');
        }

        if (!$client->clientTemplates->contains($clientTemplate)) {
            throw new \Exception('User -> ClientTemplates relationship not working');
        }

        $this->line('  ✓ Models and relationships are working correctly');
    }

    protected function testWatermarkSystem()
    {
        $this->info('🛡️ Testing watermark system...');

        $client = User::where('email', 'test-integration@example.com')->first();
        $clientTemplate = $client->clientTemplates->first();

        // Test watermark application
        $designDataWithWatermark = $clientTemplate->design_data_with_watermark;

        if (!isset($designDataWithWatermark['watermark'])) {
            throw new \Exception('Watermark not applied to design data');
        }

        $watermark = $designDataWithWatermark['watermark'];

        // Verify watermark properties
        if ($watermark['text'] !== 'سامقة للتصميم') {
            throw new \Exception('Watermark text is incorrect');
        }

        if (!$watermark['enabled']) {
            throw new \Exception('Watermark is not enabled');
        }

        if ($watermark['removable'] !== false) {
            throw new \Exception('Watermark should not be removable');
        }

        if ($watermark['style']['opacity'] !== '0.6') {
            throw new \Exception('Watermark opacity is incorrect (should be 0.6 for clients)');
        }

        $this->line('  ✓ Watermark system is working correctly');
    }

    protected function testRoutes()
    {
        $this->info('🛣️ Testing routes...');

        $routes = [
            'client.my-designs',
            'client.templates.create',
            'client.designs.edit',
            'client.designs.store',
            'client.designs.update',
            'client.designs.destroy',
            'client.designs.export',
            'client.designs.duplicate'
        ];

        foreach ($routes as $routeName) {
            if (!route($routeName, ['template' => 1, 'clientTemplate' => 1], false)) {
                throw new \Exception("Route {$routeName} is not defined");
            }
        }

        $this->line('  ✓ All required routes are defined');
    }

    protected function testControllers()
    {
        $this->info('🎮 Testing controllers...');

        // Test if controller exists and has required methods
        $controllerClass = 'App\Http\Controllers\Client\ClientTemplateController';
        
        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller {$controllerClass} does not exist");
        }

        $requiredMethods = [
            'index', 'create', 'edit', 'store', 'update', 'destroy', 'export', 'duplicate'
        ];

        foreach ($requiredMethods as $method) {
            if (!method_exists($controllerClass, $method)) {
                throw new \Exception("Method {$method} does not exist in {$controllerClass}");
            }
        }

        $this->line('  ✓ Controller and methods exist');
    }

    protected function cleanup()
    {
        $this->info('🧹 Cleaning up test data...');

        // Delete test data
        ClientTemplate::where('name', 'Test Client Template')->delete();
        User::where('email', 'test-integration@example.com')->delete();
        Template::where('name', 'Test Template Integration')->delete();
        Category::where('name', 'Test Category Integration')->delete();

        $this->line('  ✓ Test data cleaned up');
    }
}
