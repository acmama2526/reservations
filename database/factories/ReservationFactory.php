<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'people' => fake()->numberBetween(1, 6),
            'reservation_date' => fake()->date(),
            'start_time' => '18:00',
            'end_time' => '20:00',
            'phone' => '09012345678',
            'status' => 'reserved',
            'description' => null,
        ];
    }
}
