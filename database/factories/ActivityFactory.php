<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = now()->addWeeks(2)->setTime(14, 0);

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraphs(2, true),
            'location' => 'Gouwhuis Karree, Sint-Niklaas',
            'starts_at' => $start,
            'ends_at' => $start->copy()->addHours(3),
            'registration_deadline' => $start->copy()->subWeek(),
            'price_cents' => 0,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'registration_deadline' => now()->subDay(),
        ]);
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->subMonth()->addHours(3),
            'registration_deadline' => now()->subMonths(2),
        ]);
    }
}
