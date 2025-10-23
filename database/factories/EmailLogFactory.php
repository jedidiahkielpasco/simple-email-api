<?php

namespace Database\Factories;

use App\Models\EmailLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailLog>
 */
class EmailLogFactory extends Factory
{
    protected $model = EmailLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'to' => $this->faker->email(),
            'from' => $this->faker->email(),
            'subject' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'message_id' => $this->faker->optional(0.8)->uuid(),
        ];
    }
}
