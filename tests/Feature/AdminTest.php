<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_access_admin_area(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/users/create')->assertForbidden();
        $this->get('/admin/audit')->assertForbidden();
        $this->post('/admin/users', [])->assertForbidden();
    }

    public function test_admin_can_create_member(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Anggota Baru',
            'email' => 'baru@krayna.id',
            'role' => 'member',
            'password' => 'password-panjang',
            'password_confirmation' => 'password-panjang',
        ])->assertRedirect('/admin/users');

        $user = User::where('email', 'baru@krayna.id')->firstOrFail();
        $this->assertSame('member', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertNotSame('password-panjang', $user->password);

        $log = \App\Models\AuditLog::where('action', 'user.created')->firstOrFail();
        $this->assertStringNotContainsString('password-panjang', json_encode($log->metadata));
    }

    public function test_admin_can_deactivate_and_reactivate_member(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();

        $this->actingAs($admin)->patch("/admin/users/{$member->id}/toggle")->assertRedirect();
        $this->assertFalse($member->fresh()->is_active);
        $this->assertDatabaseHas('audit_logs', ['action' => 'user.deactivated', 'subject_id' => $member->id]);

        $this->patch("/admin/users/{$member->id}/toggle");
        $this->assertTrue($member->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_or_demote_self(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->patch("/admin/users/{$admin->id}/toggle")->assertSessionHasErrors('user');
        $this->assertTrue($admin->fresh()->is_active);

        $this->patch("/admin/users/{$admin->id}", ['name' => $admin->name, 'email' => $admin->email, 'role' => 'member'])
            ->assertSessionHasErrors('role');
        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_admin_pages_render(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->post('/logout');
        $this->actingAs($admin);

        $this->get('/admin/users')->assertOk()->assertSee($admin->email);
        $this->get('/admin/users/create')->assertOk();
        $this->get("/admin/users/{$admin->id}/edit")->assertOk();
        $this->get('/admin/audit')->assertOk()->assertSee('auth.logout');
    }

    public function test_create_admin_command(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Nama', 'Admin Krayna')
            ->expectsQuestion('Email', 'admin@krayna.id')
            ->expectsQuestion('Password (min. 10 karakter)', 'rahasia-sekali')
            ->expectsQuestion('Ulangi password', 'rahasia-sekali')
            ->assertSuccessful();

        $this->assertTrue(User::where('email', 'admin@krayna.id')->firstOrFail()->isAdmin());
    }
}
