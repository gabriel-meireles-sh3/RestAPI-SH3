<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Support;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Service::class;

    public function definition()
    {

        $clientIds = Ticket::pluck('id')->toArray();
        $supportId = Support::pluck('id')->toArray();

        return [
            'requester_name' => $this->faker->name,
            'client_id' => $this->faker->randomElement($clientIds),
            'service_area' => $this->faker->word,
            'support_id' => $this->faker->randomElement($supportId),
        ];
    }
}
