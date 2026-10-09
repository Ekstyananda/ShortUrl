<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'phone' => '0812-3456-7890',
            'organization' => 'SMK Contoh',
            'message' => 'Kami tertarik memakai short link untuk sekolah.',
        ], $overrides);
    }

    public function test_landing_shows_contact_form(): void
    {
        $this->get('/')->assertOk()->assertSee('Hubungi kami')->assertSee('Kirim pesan');
    }

    public function test_guest_can_send_message(): void
    {
        $this->post('/contact', $this->payload())
            ->assertRedirect(url('/').'#kontak')
            ->assertSessionHas('contact_status');

        $this->assertDatabaseHas('contact_messages', ['email' => 'budi@example.com', 'read_at' => null]);
    }

    public function test_invalid_message_is_rejected(): void
    {
        $this->post('/contact', $this->payload(['email' => 'bukan-email', 'message' => 'pendek']))
            ->assertSessionHasErrorsIn('contact', ['email', 'message']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_honeypot_silently_drops_spam(): void
    {
        $this->post('/contact', $this->payload(['website' => 'http://spam.test']))
            ->assertSessionHas('contact_status');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_form_is_rate_limited(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->post('/contact', $this->payload());
        }

        $this->post('/contact', $this->payload())->assertStatus(429);
        $this->assertDatabaseCount('contact_messages', 3);
    }

    public function test_admin_can_read_mark_unread_and_delete_message(): void
    {
        $admin = User::factory()->admin()->create();
        $message = ContactMessage::create($this->payload());

        $this->actingAs($admin)->get('/admin/messages')->assertOk()->assertSee('Budi');
        $this->get("/admin/messages/{$message->id}")->assertOk()->assertSee('SMK Contoh')->assertSee('wa.me/6281234567890');
        $this->assertNotNull($message->fresh()->read_at);

        $this->patch("/admin/messages/{$message->id}/unread")->assertRedirect('/admin/messages');
        $this->assertNull($message->fresh()->read_at);

        $this->delete("/admin/messages/{$message->id}")->assertRedirect('/admin/messages');
        $this->assertModelMissing($message);
        $this->assertDatabaseHas('audit_logs', ['action' => 'message.deleted']);
    }

    public function test_member_cannot_access_messages(): void
    {
        $message = ContactMessage::create($this->payload());

        $this->actingAs(User::factory()->create());
        $this->get('/admin/messages')->assertForbidden();
        $this->get("/admin/messages/{$message->id}")->assertForbidden();
    }
}
