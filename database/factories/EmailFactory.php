<?php

namespace Database\Factories;

use App\Models\Email;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Email>
 */
class EmailFactory extends Factory
{
    protected $model = Email::class;

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
            'status' => $this->faker->randomElement(Email::getStatuses()),
            'error_message' => null,
            'sent_at' => null,
            'failed_at' => null,
            'message_id' => null,
            'willSucceed' => $this->faker->boolean(),
        ];
    }

    /**
     * Indicate that the email is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Email::STATUS_PENDING,
        ]);
    }

    /**
     * Indicate that the email is processing.
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Email::STATUS_PROCESSING,
        ]);
    }

    /**
     * Indicate that the email is sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Email::STATUS_SENT,
            'sent_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'message_id' => $this->faker->uuid(),
        ]);
    }

    /**
     * Indicate that the email failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Email::STATUS_FAILED,
            'error_message' => $this->faker->sentence(),
            'failed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
