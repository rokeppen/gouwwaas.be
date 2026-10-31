<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return [
            'title' => 'Herfstontmoeting',
            'description' => "Eerste alinea.\n\nTweede alinea.",
            'location' => 'Gouwhuis Karree',
            'starts_at' => now()->addMonth()->format('Y-m-d\TH:i'),
            'ends_at' => now()->addMonth()->addHours(4)->format('Y-m-d\TH:i'),
            'registration_deadline' => now()->addWeeks(3)->format('Y-m-d\TH:i'),
            'price' => '12,50',
            'fields' => [
                ['label' => 'Groep', 'type' => 'text', 'required' => '1'],
                ['label' => 'Tak', 'type' => 'select', 'required' => '1', 'options' => "Kapoenen\nWelpen\nJonggivers"],
                ['label' => 'Ik blijf eten', 'type' => 'checkbox', 'required' => '0'],
            ],
            ...$overrides,
        ];
    }

    public function test_upcoming_and_past_activities_are_listed(): void
    {
        $upcoming = Activity::factory()->create(['title' => 'Komende activiteit']);
        $past = Activity::factory()->past()->create(['title' => 'Voorbije activiteit']);

        $this->get('/activiteiten')
            ->assertOk()
            ->assertSeeInOrder([$upcoming->title, 'Voorbije activiteiten', $past->title]);
    }

    public function test_guests_cannot_create_activities(): void
    {
        $this->get('/activiteiten/nieuw')->assertRedirect('/login');
        $this->post('/activiteiten', $this->payload())->assertRedirect('/login');
        $this->assertDatabaseCount('activities', 0);
    }

    public function test_user_can_create_an_activity_with_form_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/activiteiten', $this->payload())
            ->assertRedirect('/activiteiten/herfstontmoeting');

        $activity = Activity::firstOrFail();
        $this->assertTrue($activity->user->is($user));
        $this->assertSame(1250, $activity->price_cents);
        $this->assertSame(['Groep', 'Tak', 'Ik blijf eten'], $activity->fields->pluck('label')->all());
        $this->assertSame(['Kapoenen', 'Welpen', 'Jonggivers'], $activity->fields[1]->options);

        $this->get('/activiteiten/herfstontmoeting')
            ->assertOk()
            ->assertSee('€ 12,50')
            ->assertSee('<p>Eerste alinea.</p>', false);
    }

    public function test_empty_price_means_free(): void
    {
        $this->actingAs(User::factory()->create())->post('/activiteiten', $this->payload(['price' => '']));

        $this->assertSame('Gratis', Activity::firstOrFail()->priceLabel());
    }

    public function test_deadline_must_be_before_the_start_and_dropdowns_need_options(): void
    {
        $this->actingAs(User::factory()->create())->post('/activiteiten', $this->payload([
            'registration_deadline' => now()->addMonths(2)->format('Y-m-d\TH:i'),
            'fields' => [['label' => 'Tak', 'type' => 'select', 'options' => "\n"]],
        ]))->assertSessionHasErrors('registration_deadline');

        $this->actingAs(User::factory()->create())->post('/activiteiten', $this->payload([
            'fields' => [['label' => 'Tak', 'type' => 'select', 'options' => "\n  \n"]],
        ]))->assertSessionHasErrors('fields.0.options');
    }

    public function test_slug_never_collides_with_the_create_page(): void
    {
        $this->actingAs(User::factory()->create())->post('/activiteiten', $this->payload(['title' => 'Nieuw']));

        $this->assertSame('nieuw-2', Activity::firstOrFail()->slug);
    }

    public function test_only_the_owner_or_an_admin_can_manage_an_activity(): void
    {
        $activity = Activity::factory()->create();
        $stranger = User::factory()->create();

        $this->actingAs($stranger)->get("/activiteiten/{$activity->slug}/bewerken")->assertForbidden();
        $this->actingAs($stranger)->put("/activiteiten/{$activity->slug}", $this->payload())->assertForbidden();
        $this->actingAs($stranger)->get("/activiteiten/{$activity->slug}/inschrijvingen")->assertForbidden();
        $this->actingAs($stranger)->delete("/activiteiten/{$activity->slug}")->assertForbidden();
        $this->actingAs($stranger)->get("/activiteiten/{$activity->slug}")->assertDontSee('Bewerken');

        $this->actingAs($activity->user)->get("/activiteiten/{$activity->slug}/bewerken")->assertOk();
        $this->actingAs(User::factory()->admin()->create())->get("/activiteiten/{$activity->slug}/inschrijvingen")->assertOk();
    }

    public function test_owner_can_update_fields_without_losing_their_ids(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/activiteiten', $this->payload());
        $activity = Activity::firstOrFail();
        $groep = $activity->fields[0];

        $this->actingAs($user)->put("/activiteiten/{$activity->slug}", $this->payload([
            'title' => 'Nieuwe titel',
            'fields' => [
                ['id' => $groep->id, 'label' => 'Scoutsgroep', 'type' => 'text', 'required' => '1'],
            ],
        ]))->assertRedirect("/activiteiten/{$activity->slug}");

        $activity->refresh();
        $this->assertSame('Nieuwe titel', $activity->title);
        $this->assertSame('herfstontmoeting', $activity->slug, 'slug stays the same so shared links keep working');
        $this->assertSame([$groep->id], $activity->fields->pluck('id')->all());
        $this->assertSame('Scoutsgroep', $activity->fields[0]->label);
    }

    public function test_owner_can_delete_an_activity(): void
    {
        $activity = Activity::factory()->create();

        $this->actingAs($activity->user)->delete("/activiteiten/{$activity->slug}")->assertRedirect('/activiteiten');
        $this->assertModelMissing($activity);
    }
}
