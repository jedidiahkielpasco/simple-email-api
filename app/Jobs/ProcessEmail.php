<?php

namespace App\Jobs;

use App\Models\Email;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 3;

    public function __construct(public Email $email,public string $subject,public string $body) {
    }

    public function handle(): void
    {
        if($this->email->willSucceed) {
            $this->email->markAsProcessing();

            //simulate email sending
            sleep(rand(10, 12));

            $this->email->markAsSent();
            EmailLog::create([
                'to_email' => $this->email->to_email,
                'from_email' => $this->email->from_email,
                'from_name' => $this->email->from_name,
                'subject' => $this->subject,
                'body' => $this->body,
                'message_id' => $this->email->message_id,
            ]);
            return;
        } else {
            $this->email->markAsFailed('Email failed manually');
            EmailLog::create([
                'to_email' => $this->email->to_email,
                'from_email' => $this->email->from_email,
                'from_name' => $this->email->from_name,
                'subject' => $this->subject,
                'body' => $this->body,
                'message_id' => null,
            ]);
            return;
        }
    }

    // do this if fail for other reason
    public function failed(Exception $exception): void
    {
        $this->email->markAsFailed($exception->getMessage());
    }
}
