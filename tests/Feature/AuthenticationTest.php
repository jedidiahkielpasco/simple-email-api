<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_requires_authentication_for_email_creation()
    {
        $emailData = [
            'to' => 'recipient@example.com',
            'from' => 'sender@example.com',
            'subject' => 'Test Subject',
            'body' => 'Test Body',
        ];

        $response = $this->postJson('/api/v1/emails', $emailData);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_allows_authenticated_users_to_create_emails()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $emailData = [
            'to' => 'recipient@example.com',
            'from' => 'sender@example.com',
            'subject' => 'Test Subject',
            'body' => 'Test Body',
        ];

        $response = $this->postJson('/api/v1/emails', $emailData);

        $response->assertStatus(201);
    }

    /** @test */
    public function it_handles_invalid_token()
    {
        $this->withHeaders([
            'Authorization' => 'Bearer invalid-token',
        ]);

        $emailData = [
            'to' => 'recipient@example.com',
            'from' => 'sender@example.com',
            'subject' => 'Test Subject',
            'body' => 'Test Body',
        ];

        $response = $this->postJson('/api/v1/emails', $emailData);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_handles_missing_token()
    {
        $emailData = [
            'to' => 'recipient@example.com',
            'from' => 'sender@example.com',
            'subject' => 'Test Subject',
            'body' => 'Test Body',
        ];

        $response = $this->postJson('/api/v1/emails', $emailData);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_allows_multiple_authenticated_users()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Passport::actingAs($user1);
        
        $emailData = [
            'to' => 'recipient@example.com',
            'from' => 'sender@example.com',
            'subject' => 'Test Subject',
            'body' => 'Test Body',
        ];

        $response = $this->postJson('/api/v1/emails', $emailData);
        $response->assertStatus(201);

        Passport::actingAs($user2);
        
        $response = $this->postJson('/api/v1/emails', $emailData);
        $response->assertStatus(201);
    }
}
