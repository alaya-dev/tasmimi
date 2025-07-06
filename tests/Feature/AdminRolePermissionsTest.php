<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_only_view_clients(): void
    {
        // Créer un admin
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        
        // Créer des utilisateurs de différents rôles
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $anotherAdmin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);

        $response = $this
            ->actingAs($admin)
            ->get('/admin/users');

        $response->assertOk();
        
        // Vérifier que seuls les clients sont visibles dans la réponse
        $response->assertInertia(fn ($page) => 
            $page->has('users.data', 1) // Seulement 1 client
                 ->where('users.data.0.role', User::ROLE_CLIENT)
        );
    }

    public function test_admin_cannot_create_users(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this
            ->actingAs($admin)
            ->get('/admin/users/create');

        $response->assertStatus(403);
    }

    public function test_admin_cannot_edit_other_admins(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $anotherAdmin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this
            ->actingAs($admin)
            ->get("/admin/users/{$anotherAdmin->id}/edit");

        $response->assertStatus(403);
    }

    public function test_admin_can_edit_clients(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);

        $response = $this
            ->actingAs($admin)
            ->get("/admin/users/{$client->id}/edit");

        $response->assertOk();
    }

    public function test_admin_can_delete_clients(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);

        $response = $this
            ->actingAs($admin)
            ->delete("/admin/users/{$client->id}");

        $response->assertRedirect('/admin/users');
        $this->assertNull($client->fresh());
    }

    public function test_admin_cannot_delete_other_admins(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $anotherAdmin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this
            ->actingAs($admin)
            ->delete("/admin/users/{$anotherAdmin->id}");

        $response->assertStatus(403);
        $this->assertNotNull($anotherAdmin->fresh());
    }

    public function test_super_admin_can_create_users(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this
            ->actingAs($superAdmin)
            ->get('/admin/users/create');

        $response->assertOk();
    }

    public function test_super_admin_can_create_admin_users(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this
            ->actingAs($superAdmin)
            ->post('/admin/users', [
                'name' => 'New Admin',
                'email' => 'newadmin@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => User::ROLE_ADMIN,
            ]);

        $response->assertRedirect('/admin/users');
        
        $this->assertDatabaseHas('users', [
            'email' => 'newadmin@example.com',
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_super_admin_can_delete_admins(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this
            ->actingAs($superAdmin)
            ->delete("/admin/users/{$admin->id}");

        $response->assertRedirect('/admin/users');
        $this->assertNull($admin->fresh());
    }

    public function test_super_admin_can_view_all_users(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        // Créer des utilisateurs de différents rôles
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);

        $response = $this
            ->actingAs($superAdmin)
            ->get('/admin/users');

        $response->assertOk();

        // Vérifier que tous les utilisateurs sont visibles (Super Admin + Admin + Client)
        $response->assertInertia(fn ($page) =>
            $page->has('users.data', 3) // Super Admin + Admin + Client
        );
    }

    public function test_super_admin_can_edit_admin_users(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this
            ->actingAs($superAdmin)
            ->get("/admin/users/{$admin->id}/edit");

        $response->assertOk();
    }

    public function test_super_admin_can_update_admin_users(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this
            ->actingAs($superAdmin)
            ->put("/admin/users/{$admin->id}", [
                'name' => 'Updated Admin Name',
                'email' => $admin->email,
                'role' => User::ROLE_ADMIN,
            ]);

        $response->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Updated Admin Name',
            'role' => User::ROLE_ADMIN,
        ]);
    }
}
