<?php

namespace Tests\Feature;

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_gets_svg_qr_code(): void
    {
        $me = User::factory()->create();
        $link = ShortLink::factory()->for($me)->create(['alias' => 'qr-test']);

        $response = $this->actingAs($me)->get("/links/{$link->id}/qr.svg");

        $response->assertOk()->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertStringContainsString('<svg', $response->getContent());
    }

    public function test_download_sets_attachment_filename(): void
    {
        $me = User::factory()->create();
        $link = ShortLink::factory()->for($me)->create(['alias' => 'qr-test']);

        $this->actingAs($me)->get("/links/{$link->id}/qr.svg?download=1")
            ->assertHeader('Content-Disposition', 'attachment; filename="qr-qr-test.svg"');
    }

    public function test_member_cannot_get_qr_of_other_members_link(): void
    {
        $other = ShortLink::factory()->create();

        $this->actingAs(User::factory()->create())->get("/links/{$other->id}/qr.svg")->assertForbidden();
    }

    public function test_guest_cannot_get_qr(): void
    {
        $link = ShortLink::factory()->create();

        $this->get("/links/{$link->id}/qr.svg")->assertRedirect('/login');
    }
}
