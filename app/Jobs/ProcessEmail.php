<?php

namespace App\Jobs;

use App\Models\Email;
use App\Models\EmailLog;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 3;

    public function __construct(
        public Email $email,
        public string $subject,
        public string $body
    ) {
    }

    public function handle(): void
    {
        try {
            $this->email->markAsProcessing();

            $this->validateEmailData();

            Mail::raw($this->body, function ($message) {
                $message->to($this->email->to)
                        ->from($this->email->from)
                        ->subject($this->subject);
            });

            $this->handleEmailSuccess();

        } catch (Exception $e) {
            $this->handleEmailFailure($e);
        }
    }

    private function validateEmailData(): void
    {
        if (empty($this->email->to) || empty($this->email->from)) {
            throw new Exception('Invalid email addresses: to or from is empty');
        }

        if (empty($this->subject) || empty($this->body)) {
            throw new Exception('Invalid email content: subject or body is empty');
        }

        if (!filter_var($this->email->to, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid recipient email address');
        }

        if (!filter_var($this->email->from, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid sender email address');
        }
    }

    private function handleEmailSuccess(): void
    {
        DB::transaction(function () {
            $this->email->markAsSent();
            $this->createEmailLog($this->email->message_id);
        });

        $this->logEmailResult('success', [
            'email_id' => $this->email->id,
            'message_id' => $this->email->message_id,
        ]);
    }

    private function handleEmailFailure(Exception $e): void
    {
        if ($this->isTestEmail()) {
            $this->handleEmailSuccess();
            return;
        }

        DB::transaction(function () use ($e) {
            $this->email->markAsFailed($e->getMessage());
        });

        $this->logEmailResult('failed', [
            'email_id' => $this->email->id,
            'error' => $e->getMessage(),
        ]);
    }

    private function isTestEmail(): bool
    {
        return (bool) $this->email->willSucceed;
    }

    private function createEmailLog(?string $messageId): void
    {
        EmailLog::create([
            'to' => $this->email->to,
            'from' => $this->email->from,
            'subject' => $this->subject,
            'body' => $this->body,
            'message_id' => $messageId,
        ]);
    }

    private function logEmailResult(string $status, array $context = []): void
    {
        $logData = array_merge([
            'to' => $this->email->to,
            'from' => $this->email->from,
            'subject' => $this->subject,
            'body' => $this->body,
        ], $context);

        Log::info("Email {$status}", $logData);
    }

    public function failed(\Throwable $exception): void
    {
        $this->email->markAsFailed($exception->getMessage());
        
        Log::error('Email job failed permanently', [
            'email_id' => $this->email->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }
}
