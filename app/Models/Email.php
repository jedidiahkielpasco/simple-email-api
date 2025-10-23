<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $table = 'email_tracker';

    protected $fillable = [
        'to',
        'from',
        'status',
        'error_message',
        'sent_at',
        'failed_at',
        'message_id',
        'willSucceed',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_SENT,
            self::STATUS_FAILED,
        ];
    }

    /**
     * Mark email as processing
     */
    public function markAsProcessing(): void
    {
        $this->update(['status' => self::STATUS_PROCESSING]);
    }

    /**
     * Mark email as sent
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
            'message_id' => uniqid(),
        ]);
    }

    /**
     * Mark email as failed
     */
    public function markAsFailed(string $errorMessage = null): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
            'failed_at' => now(),
        ]);
    }
}
