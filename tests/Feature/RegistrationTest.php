<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function activityWithFields(array $state = []): Activity
    {
        $activity = Activity::factory()->create($state);
        $activity->syncFields([
            ['label' => 'Groep', 'type' => 'text', 'required' => true],
            ['label' => 'Tak', 'type' => 'select', 'required' => true, 'options' => "Kapoenen\nWelpen"],
            ['label' => 'Akkoord met de afspraken', 'type' => 'checkbox', 'required' => true],
            ['label' => 'Aantal personen', 'type' => 'number', 'required' => false],
        ]);

        return $activity->load('fields');
    }

    private function answers(Activity $activity, array $values): array
    {
        return collect($activity->fields)->mapWithKeys(fn ($field, $i) => [$field->id => $values[$i] ?? null])->all();
    }

    public function test_anyone_can_register_and_answers_are_stored(): void
    {
        $activity = $this->activityWithFields();

        $this->post("/activiteiten/{$activity->slug}/inschrijven", [
            'name' => 'Jan Peeters',
            'email' => 'jan@example.com',
            'answers' => $this->answers($activity, ['Sint-Jozef', 'Welpen', '1', '3']),
        ])->assertRedirect("/activiteiten/{$activity->slug}")->assertSessionHas('status');

        $registration = $activity->registrations()->firstOrFail();
        $this->assertSame('Jan Peeters', $registration->name);
        $this->assertSame('Welpen', $registration->answerFor($activity->fields[1]->id));
        $this->assertSame('Ja', $registration->answerFor($activity->fields[2]->id));
        $this->assertSame('3', $registration->answerFor($activity->fields[3]->id));
    }

    public function test_required_fields_and_choices_are_validated(): void
    {
        $activity = $this->activityWithFields();
        [$groep, $tak, $akkoord, $aantal] = $activity->fields->pluck('id')->all();

        $this->post("/activiteiten/{$activity->slug}/inschrijven", [
            'name' => '',
            'email' => 'geen-email',
            'answers' => [$groep => '', $tak => 'Pioniers', $aantal => 'veel'],
        ])->assertSessionHasErrors(['name', 'email', "answers.{$groep}", "answers.{$tak}", "answers.{$akkoord}", "answers.{$aantal}"]);

        $this->assertDatabaseCount('registrations', 0);
    }

    public function test_registration_is_closed_after_the_deadline(): void
    {
        $activity = Activity::factory()->closed()->create();

        $this->get("/activiteiten/{$activity->slug}")->assertSee('afgesloten');

        $this->post("/activiteiten/{$activity->slug}/inschrijven", ['name' => 'Jan', 'email' => 'jan@example.com'])
            ->assertSessionHasErrors('registration');

        $this->assertDatabaseCount('registrations', 0);
    }

    public function test_owner_sees_registrations_including_answers_to_removed_fields(): void
    {
        $activity = $this->activityWithFields();
        $this->post("/activiteiten/{$activity->slug}/inschrijven", [
            'name' => 'Jan Peeters',
            'email' => 'jan@example.com',
            'answers' => $this->answers($activity, ['Sint-Jozef', 'Welpen', '1']),
        ]);

        // The owner later drops the "Groep" question.
        $activity->syncFields($activity->fields->slice(1)->map(fn ($f) => [
            'id' => $f->id, 'label' => $f->label, 'type' => $f->type, 'required' => $f->required,
            'options' => implode("\n", $f->options ?? []),
        ])->values()->all());

        $this->actingAs($activity->user)->get("/activiteiten/{$activity->slug}/inschrijvingen")
            ->assertOk()
            ->assertSee('Jan Peeters')
            ->assertSee('Welpen')
            ->assertSee('Groep (verwijderd veld)')
            ->assertSee('Sint-Jozef');
    }

    public function test_csv_export_neutralises_formulas(): void
    {
        $activity = $this->activityWithFields();
        $this->post("/activiteiten/{$activity->slug}/inschrijven", [
            'name' => '=HYPERLINK("http://evil")',
            'email' => 'jan@example.com',
            'answers' => $this->answers($activity, ['Sint-Jozef', 'Welpen', '1']),
        ]);

        $csv = $this->actingAs($activity->user)->get("/activiteiten/{$activity->slug}/inschrijvingen.csv")
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('"Ingeschreven op";Naam;E-mailadres;Groep;Tak', $csv);
        $this->assertStringContainsString("'=HYPERLINK", $csv);
    }

    public function test_others_cannot_export_or_delete_registrations(): void
    {
        $activity = $this->activityWithFields();
        $this->post("/activiteiten/{$activity->slug}/inschrijven", [
            'name' => 'Jan', 'email' => 'jan@example.com',
            'answers' => $this->answers($activity, ['Sint-Jozef', 'Welpen', '1']),
        ]);
        $registration = $activity->registrations()->firstOrFail();
        $stranger = User::factory()->create();

        $this->actingAs($stranger)->get("/activiteiten/{$activity->slug}/inschrijvingen.csv")->assertForbidden();
        $this->actingAs($stranger)->delete("/activiteiten/{$activity->slug}/inschrijvingen/{$registration->id}")->assertForbidden();

        $this->actingAs($activity->user)->delete("/activiteiten/{$activity->slug}/inschrijvingen/{$registration->id}");
        $this->assertModelMissing($registration);
    }

    public function test_a_registration_of_another_activity_cannot_be_deleted_via_this_one(): void
    {
        $mine = Activity::factory()->create();
        $other = Activity::factory()->create();
        $registration = $other->registrations()->create(['name' => 'X', 'email' => 'x@example.com', 'answers' => []]);

        $this->actingAs($mine->user)->delete("/activiteiten/{$mine->slug}/inschrijvingen/{$registration->id}")->assertNotFound();
        $this->assertModelExists($registration);
    }
}
