<?php

namespace Tests\Feature;

use App\Jobs\ProcessEmail;
use App\Models\Email;
use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\Passport;
use Tests\TestCase;

class EmailJobProcessingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Passport::actingAs($this->user);
    }

    /** @test */
    public function it_processes_email_job_successfully()
    {
        Mail::fake();
        Queue::fake();

        $email = Email::factory()->create([
            'status' => Email::STATUS_PENDING,
            'willSucceed' => true,
        ]);

        $subject = 'Test Subject';
        $body = 'Test Body';

        $job = new ProcessEmail($email, $subject, $body);
        $job->handle();

        $email->refresh();
        $this->assertEquals(Email::STATUS_SENT, $email->status);
        $this->assertNotNull($email->sent_at);
        $this->assertNotNull($email->message_id);

        $this->assertDatabaseHas('email_logs', [
            'to' => $email->to,
            'from' => $email->from,
            'subject' => $subject,
            'body' => $body,
            'message_id' => $email->message_id,
        ]);
    }

    /** @test */
    public function it_handles_email_job_failure()
    {
        Mail::fake();
        Queue::fake();

        $email = Email::factory()->create([
            'status' => Email::STATUS_PENDING,
            'willSucceed' => false,
        ]);

        $subject = 'Test Subject';
        $body = 'Test Body';

        Mail::shouldReceive('raw')
            ->andThrow(new \Exception('SMTP connection failed'));

        $job = new ProcessEmail($email, $subject, $body);
        $job->handle();

        $email->refresh();
        $this->assertEquals(Email::STATUS_FAILED, $email->status);
        $this->assertNotNull($email->failed_at);
        $this->assertEquals('SMTP connection failed', $email->error_message);

        $this->assertDatabaseMissing('email_logs', [
            'to' => $email->to,
            'from' => $email->from,
            'subject' => $subject,
            'body' => $body,
        ]);
    }

    /** @test */
    public function it_handles_job_failure_callback()
    {
        $email = Email::factory()->create([
            'status' => Email::STATUS_PENDING,
        ]);

        $subject = 'Test Subject';
        $body = 'Test Body';

        $job = new ProcessEmail($email, $subject, $body);

        $exception = new \Exception('Job failed');
        $job->failed($exception);

        $email->refresh();
        $this->assertEquals(Email::STATUS_FAILED, $email->status);
        $this->assertNotNull($email->failed_at);
        $this->assertEquals('Job failed', $email->error_message);
    }

    /** @test */
    public function it_creates_email_log_with_correct_data()
    {
        Mail::fake();
        Queue::fake();

        $email = Email::factory()->create([
            'to' => 'recipient@example.com',
            'from' => 'sender@example.com',
            'status' => Email::STATUS_PENDING,
            'willSucceed' => true,
        ]);

        $subject = 'Important Email';
        $body = 'This is the email body content.';

        $job = new ProcessEmail($email, $subject, $body);
        $job->handle();

        $this->assertDatabaseHas('email_logs', [
            'to' => 'recipient@example.com',
            'from' => 'sender@example.com',
            'subject' => 'Important Email',
            'body' => 'This is the email body content.',
        ]);

        $emailLog = EmailLog::where('to', 'recipient@example.com')->first();
        $this->assertNotNull($emailLog);
        $this->assertEquals('sender@example.com', $emailLog->from);
        $this->assertEquals('Important Email', $emailLog->subject);
        $this->assertEquals('This is the email body content.', $emailLog->body);
    }
}
