<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskSession>
 */
class TaskSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->randomNumber(),
            'task_id' => $this->faker->uuid(),
            'student_id' => $this->faker->uuid(),
            'status' => $this->faker->randomElement(['completed', 'processing']),
        ];
    }
}
