<?php

namespace Database\Factories;
use App\Models\Ticket;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Ticket::class;

    public function definition()
    {
        return [
            'name' => fake()->name(),
            'client' => fake()->name(),
            'occupation_area' => $this->faker->word,
        ];
    }
}
