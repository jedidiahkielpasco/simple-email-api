<?php

namespace Tests\Feature;

use App\Models\Email;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\Passport;
use Tests\TestCase;

class EmailCreationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        Passport::actingAs($this->user);
    }

    /** @test */
    public function it_can_create_an_email_with_valid_data()
    {
        Queue::fake();

        $emailData = [
            'to' => 'recipient@example.com',
            'from' => 'sender@example.com',
            'subject' => 'Test Email Subject',
            'body' => 'This is a test email body content.',
            'willSucceed' => true,
        ];

        $response = $this->postJson('/api/v1/emails', $emailData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Email queued for sending',
                'data' => [
                    'status' => 'pending',
                ],
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'status',
                    'created_at',
                ],
            ]);

        $this->assertDatabaseHas('email_tracker', [
            'to' => 'recipient@example.com',
            'from' => 'sender@example.com',
            'status' => Email::STATUS_PENDING,
            'willSucceed' => true,
        ]);

        Queue::assertPushed(\App\Jobs\ProcessEmail::class);
    }

    /** @test */
    public function it_can_create_an_email_without_willSucceed_field()
    {
        Queue::fake();

        $emailData = [
            'to' => 'recipient@example.com',
            'from' => 'sender@example.com',
            'subject' => 'Test Email Subject',
            'body' => 'This is a test email body content.',
        ];

        $response = $this->postJson('/api/v1/emails', $emailData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('email_tracker', [
            'to' => 'recipient@example.com',
            'from' => 'sender@example.com',
            'status' => Email::STATUS_PENDING,
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/v1/emails', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['to', 'from', 'subject', 'body']);
    }

    /** @test */
    public function it_validates_email_format()
    {
        $emailData = [
            'to' => 'invalid-email',
            'from' => 'also-invalid',
            'subject' => 'Test Subject',
            'body' => 'Test body',
        ];

        $response = $this->postJson('/api/v1/emails', $emailData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['to', 'from']);
    }
}
