<?php

namespace Tests\Feature;

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_link_redirects_with_302_and_records_click(): void
    {
        $link = ShortLink::factory()->create(['alias' => 'modul-sbd', 'destination_url' => 'https://example.com/modul?x=1']);

        $this->get('/modul-sbd', [
            'Referer' => 'https://www.google.com/search?q=secret-token',
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120.0 Safari/537.36',
        ])
            ->assertStatus(302)
            ->assertRedirect('https://example.com/modul?x=1')
            ->assertHeader('X-Robots-Tag', 'noindex');

        $this->assertDatabaseHas('click_events', [
            'short_link_id' => $link->id,
            'referer_host' => 'www.google.com',
            'user_agent_family' => 'Chrome',
        ]);
        // URL referer lengkap (beserta token) tidak boleh tersimpan.
        $this->assertDatabaseMissing('click_events', ['referer_host' => 'https://www.google.com/search?q=secret-token']);
    }

    public function test_unknown_alias_returns_404(): void
    {
        $this->get('/does-not-exist')->assertNotFound();
        $this->assertDatabaseCount('click_events', 0);
    }

    public function test_inactive_link_does_not_redirect(): void
    {
        ShortLink::factory()->inactive()->create(['alias' => 'off']);

        $this->get('/off')->assertNotFound();
        $this->assertDatabaseCount('click_events', 0);
    }

    public function test_expired_link_does_not_redirect(): void
    {
        ShortLink::factory()->expired()->create(['alias' => 'old']);

        $this->get('/old')->assertNotFound();
    }

    public function test_link_of_deactivated_user_does_not_redirect(): void
    {
        $user = User::factory()->inactive()->create();
        ShortLink::factory()->for($user)->create(['alias' => 'ghost']);

        $this->get('/ghost')->assertNotFound();
    }

    public function test_alias_lookup_is_case_insensitive(): void
    {
        ShortLink::factory()->create(['alias' => 'abc123', 'destination_url' => 'https://example.com']);

        $this->get('/ABC123')->assertRedirect('https://example.com');
    }

    public function test_app_routes_are_not_shadowed_by_alias_route(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/health')->assertOk()->assertJson(['status' => 'ok', 'database' => 'ok']);
    }
}
