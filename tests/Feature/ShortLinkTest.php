<?php

namespace Tests\Feature;

use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ShortLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_create_link_with_custom_alias(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/links', [
            'destination_url' => 'https://example.com/modul',
            'alias' => 'modul-sbd',
            'title' => 'Modul SBD',
        ])->assertRedirect();

        $this->assertDatabaseHas('short_links', ['alias' => 'modul-sbd', 'user_id' => $user->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'link.created']);
    }

    public function test_empty_alias_gets_random_alias(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/links', ['destination_url' => 'https://example.com'])->assertRedirect();

        $link = ShortLink::firstOrFail();
        $this->assertMatchesRegularExpression('/^[a-z0-9]{6}$/', $link->alias);
    }

    public function test_alias_is_normalized_to_lowercase_and_unique_case_insensitively(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/links', ['destination_url' => 'https://example.com', 'alias' => 'Modul-SBD']);
        $this->assertDatabaseHas('short_links', ['alias' => 'modul-sbd']);

        $this->post('/links', ['destination_url' => 'https://example.com', 'alias' => 'MODUL-sbd'])
            ->assertSessionHasErrors('alias');
    }

    public function test_duplicate_alias_is_rejected(): void
    {
        ShortLink::factory()->create(['alias' => 'taken']);

        $this->actingAs(User::factory()->create())
            ->post('/links', ['destination_url' => 'https://example.com', 'alias' => 'taken'])
            ->assertSessionHasErrors('alias');
    }

    #[DataProvider('reservedAliases')]
    public function test_reserved_alias_is_rejected(string $alias): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/links', ['destination_url' => 'https://example.com', 'alias' => $alias])
            ->assertSessionHasErrors('alias');
    }

    public static function reservedAliases(): array
    {
        return [['login'], ['LOGOUT'], ['links'], ['admin'], ['api'], ['health']];
    }

    #[DataProvider('invalidAliases')]
    public function test_invalid_alias_format_is_rejected(string $alias): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/links', ['destination_url' => 'https://example.com', 'alias' => $alias])
            ->assertSessionHasErrors('alias');
    }

    public static function invalidAliases(): array
    {
        return [['a b'], ['-leading'], ['../etc'], ['x'], ['emoji😀'], [str_repeat('a', 65)]];
    }

    #[DataProvider('dangerousUrls')]
    public function test_dangerous_or_invalid_destination_is_rejected(string $url): void
    {
        config(['app.url' => 'https://s.krayna.id']);

        $this->actingAs(User::factory()->create())
            ->post('/links', ['destination_url' => $url, 'alias' => 'test-url'])
            ->assertSessionHasErrors('destination_url');

        $this->assertDatabaseCount('short_links', 0);
    }

    public static function dangerousUrls(): array
    {
        return [
            ['javascript:alert(1)'],
            ['data:text/html;base64,PHNjcmlwdD4='],
            ['file:///etc/passwd'],
            ['ftp://example.com/file'],
            ['https://'],
            ['//example.com'],
            ['example.com'],
            ['not a url'],
            ['https://user:pass@example.com'],
            ['http://S.KRAYNA.ID/loop'],
        ];
    }

    public function test_member_sees_only_own_links(): void
    {
        $me = User::factory()->create();
        ShortLink::factory()->for($me)->create(['alias' => 'mine-link']);
        ShortLink::factory()->create(['alias' => 'other-link']);

        $this->actingAs($me)->get('/links')
            ->assertOk()
            ->assertSee('mine-link')
            ->assertDontSee('other-link');
    }

    public function test_member_cannot_access_other_members_link(): void
    {
        $me = User::factory()->create();
        $other = ShortLink::factory()->create();

        $this->actingAs($me);
        $this->get("/links/{$other->id}")->assertForbidden();
        $this->get("/links/{$other->id}/edit")->assertForbidden();
        $this->get("/links/{$other->id}/analytics")->assertForbidden();
        $this->patch("/links/{$other->id}", ['destination_url' => 'https://evil.test'])->assertForbidden();
        $this->patch("/links/{$other->id}/toggle")->assertForbidden();
        $this->delete("/links/{$other->id}")->assertForbidden();

        $this->assertDatabaseHas('short_links', ['id' => $other->id, 'destination_url' => $other->destination_url, 'is_active' => true]);
    }

    public function test_admin_can_see_and_manage_all_links(): void
    {
        $admin = User::factory()->admin()->create();
        $link = ShortLink::factory()->create(['alias' => 'member-link']);

        $this->actingAs($admin);
        $this->get('/links')->assertSee('member-link');
        $this->get("/links/{$link->id}")->assertOk();
        $this->get("/links/{$link->id}/analytics")->assertOk();
        $this->patch("/links/{$link->id}", ['destination_url' => 'https://example.org', 'alias' => 'member-link'])->assertRedirect();
        $this->assertSame('https://example.org', $link->fresh()->destination_url);
        $this->delete("/links/{$link->id}")->assertRedirect('/links');
        $this->assertModelMissing($link);
    }

    public function test_owner_can_update_toggle_and_delete(): void
    {
        $me = User::factory()->create();
        $link = ShortLink::factory()->for($me)->create(['alias' => 'old-alias']);

        $this->actingAs($me);
        $this->patch("/links/{$link->id}", ['destination_url' => 'https://new.example.com/x', 'alias' => 'new-alias'])->assertRedirect();
        $this->assertSame('new-alias', $link->fresh()->alias);
        $this->assertDatabaseHas('audit_logs', ['action' => 'link.updated', 'subject_id' => $link->id]);

        $this->patch("/links/{$link->id}/toggle")->assertRedirect();
        $this->assertFalse($link->fresh()->is_active);

        $this->delete("/links/{$link->id}")->assertRedirect('/links');
        $this->assertDatabaseHas('audit_logs', ['action' => 'link.deleted', 'subject_id' => $link->id]);
    }

    public function test_updating_keeps_own_alias_without_unique_error(): void
    {
        $me = User::factory()->create();
        $link = ShortLink::factory()->for($me)->create(['alias' => 'same']);

        $this->actingAs($me)
            ->patch("/links/{$link->id}", ['destination_url' => 'https://example.com/z', 'alias' => 'same'])
            ->assertSessionHasNoErrors();
    }

    public function test_pages_render(): void
    {
        $me = User::factory()->create();
        $link = ShortLink::factory()->for($me)->create();
        $link->clickEvents()->create(['clicked_at' => now(), 'referer_host' => 'google.com', 'user_agent_family' => 'Chrome']);

        $this->actingAs($me);
        $this->get('/links')->assertOk()->assertSee('Total klik');
        $this->get('/links/create')->assertOk();
        $this->get("/links/{$link->id}")->assertOk()->assertSee($link->destinationHost());
        $this->get("/links/{$link->id}/edit")->assertOk();
        $this->get("/links/{$link->id}/analytics")->assertOk()->assertSee('google.com');
    }
}
