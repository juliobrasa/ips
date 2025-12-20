<?php

namespace App\Services;

use App\Models\User;
use App\Models\NotificationPreference;
use App\Models\WebhookEndpoint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Notification types
     */
    public const TYPE_LEASE_EXPIRING = 'lease_expiring';
    public const TYPE_LEASE_EXPIRED = 'lease_expired';
    public const TYPE_LEASE_CREATED = 'lease_created';
    public const TYPE_LEASE_RENEWED = 'lease_renewed';
    public const TYPE_IP_BLACKLISTED = 'ip_blacklisted';
    public const TYPE_IP_DELISTED = 'ip_delisted';
    public const TYPE_PAYMENT_RECEIVED = 'payment_received';
    public const TYPE_PAYMENT_FAILED = 'payment_failed';
    public const TYPE_INVOICE_CREATED = 'invoice_created';
    public const TYPE_INVOICE_OVERDUE = 'invoice_overdue';
    public const TYPE_PAYOUT_PROCESSED = 'payout_processed';
    public const TYPE_SUBNET_VERIFIED = 'subnet_verified';
    public const TYPE_SUBNET_SUSPENDED = 'subnet_suspended';
    public const TYPE_KYC_APPROVED = 'kyc_approved';
    public const TYPE_KYC_REJECTED = 'kyc_rejected';
    public const TYPE_ABUSE_REPORT = 'abuse_report';
    public const TYPE_SECURITY_ALERT = 'security_alert';
    public const TYPE_TICKET_UPDATE = 'ticket_update';

    /**
     * Channels
     */
    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_DATABASE = 'database';
    public const CHANNEL_WEBHOOK = 'webhook';
    public const CHANNEL_SMS = 'sms';
    public const CHANNEL_TELEGRAM = 'telegram';
    public const CHANNEL_SLACK = 'slack';

    /**
     * Send notification to user
     */
    public function notify(User $user, string $type, array $data = [], ?string $channel = null): void
    {
        $preferences = $this->getUserPreferences($user, $type);

        if ($channel) {
            $this->sendToChannel($user, $channel, $type, $data);
            return;
        }

        foreach ($preferences as $ch => $enabled) {
            if ($enabled) {
                $this->sendToChannel($user, $ch, $type, $data);
            }
        }
    }

    /**
     * Send to specific channel
     */
    protected function sendToChannel(User $user, string $channel, string $type, array $data): void
    {
        try {
            match ($channel) {
                self::CHANNEL_EMAIL => $this->sendEmail($user, $type, $data),
                self::CHANNEL_DATABASE => $this->sendDatabase($user, $type, $data),
                self::CHANNEL_WEBHOOK => $this->sendWebhook($user, $type, $data),
                self::CHANNEL_TELEGRAM => $this->sendTelegram($user, $type, $data),
                self::CHANNEL_SLACK => $this->sendSlack($user, $type, $data),
                self::CHANNEL_SMS => $this->sendSms($user, $type, $data),
                default => null,
            };
        } catch (\Exception $e) {
            Log::error("Notification failed", [
                'channel' => $channel,
                'type' => $type,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send email notification
     */
    protected function sendEmail(User $user, string $type, array $data): void
    {
        $notificationClass = $this->getNotificationClass($type);

        if ($notificationClass && class_exists($notificationClass)) {
            $user->notify(new $notificationClass($data));
        }
    }

    /**
     * Send database notification
     */
    protected function sendDatabase(User $user, string $type, array $data): void
    {
        $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => $type,
            'data' => array_merge($data, [
                'title' => $this->getNotificationTitle($type),
                'message' => $this->getNotificationMessage($type, $data),
            ]),
        ]);
    }

    /**
     * Send webhook notification
     */
    protected function sendWebhook(User $user, string $type, array $data): void
    {
        $endpoints = WebhookEndpoint::where('user_id', $user->id)
            ->where('is_active', true)
            ->where(function ($q) use ($type) {
                $q->whereNull('events')
                    ->orWhereJsonContains('events', $type);
            })
            ->get();

        foreach ($endpoints as $endpoint) {
            $this->dispatchWebhook($endpoint, $type, $data);
        }
    }

    /**
     * Dispatch webhook to endpoint
     */
    protected function dispatchWebhook(WebhookEndpoint $endpoint, string $type, array $data): void
    {
        $payload = [
            'event' => $type,
            'timestamp' => now()->toIso8601String(),
            'data' => $data,
        ];

        $signature = hash_hmac('sha256', json_encode($payload), $endpoint->secret);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-Webhook-Signature' => $signature,
                    'X-Webhook-Event' => $type,
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint->url, $payload);

            $endpoint->update([
                'last_triggered_at' => now(),
                'last_response_code' => $response->status(),
                'failure_count' => $response->successful() ? 0 : $endpoint->failure_count + 1,
            ]);
        } catch (\Exception $e) {
            $endpoint->increment('failure_count');
            Log::error("Webhook failed", [
                'endpoint_id' => $endpoint->id,
                'url' => $endpoint->url,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send Telegram notification
     */
    protected function sendTelegram(User $user, string $type, array $data): void
    {
        $chatId = $user->telegram_chat_id;
        $token = config('services.telegram.bot_token');

        if (!$chatId || !$token) {
            return;
        }

        $message = $this->formatTelegramMessage($type, $data);

        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);
    }

    /**
     * Send Slack notification
     */
    protected function sendSlack(User $user, string $type, array $data): void
    {
        $webhookUrl = $user->slack_webhook_url;

        if (!$webhookUrl) {
            return;
        }

        Http::post($webhookUrl, [
            'text' => $this->getNotificationTitle($type),
            'attachments' => [
                [
                    'color' => $this->getNotificationColor($type),
                    'text' => $this->getNotificationMessage($type, $data),
                    'ts' => now()->timestamp,
                ],
            ],
        ]);
    }

    /**
     * Send SMS notification
     */
    protected function sendSms(User $user, string $type, array $data): void
    {
        // Implement with Twilio or other SMS provider
        $phone = $user->phone;
        if (!$phone) {
            return;
        }

        // SMS implementation here
    }

    /**
     * Get user notification preferences
     */
    protected function getUserPreferences(User $user, string $type): array
    {
        $preferences = NotificationPreference::where('user_id', $user->id)
            ->where('notification_type', $type)
            ->first();

        if (!$preferences) {
            return $this->getDefaultPreferences($type);
        }

        return [
            self::CHANNEL_EMAIL => $preferences->email_enabled,
            self::CHANNEL_DATABASE => $preferences->database_enabled,
            self::CHANNEL_WEBHOOK => $preferences->webhook_enabled,
            self::CHANNEL_TELEGRAM => $preferences->telegram_enabled,
            self::CHANNEL_SLACK => $preferences->slack_enabled,
            self::CHANNEL_SMS => $preferences->sms_enabled,
        ];
    }

    /**
     * Get default preferences for notification type
     */
    protected function getDefaultPreferences(string $type): array
    {
        $critical = [
            self::TYPE_SECURITY_ALERT,
            self::TYPE_IP_BLACKLISTED,
            self::TYPE_PAYMENT_FAILED,
            self::TYPE_INVOICE_OVERDUE,
        ];

        return [
            self::CHANNEL_EMAIL => true,
            self::CHANNEL_DATABASE => true,
            self::CHANNEL_WEBHOOK => in_array($type, $critical),
            self::CHANNEL_TELEGRAM => false,
            self::CHANNEL_SLACK => false,
            self::CHANNEL_SMS => in_array($type, $critical),
        ];
    }

    /**
     * Get notification class
     */
    protected function getNotificationClass(string $type): ?string
    {
        $classes = [
            self::TYPE_LEASE_EXPIRING => \App\Notifications\LeaseExpiringNotification::class,
            self::TYPE_LEASE_CREATED => \App\Notifications\LeaseCreatedNotification::class,
            self::TYPE_IP_BLACKLISTED => \App\Notifications\IpBlacklistedNotification::class,
            self::TYPE_PAYMENT_RECEIVED => \App\Notifications\PaymentReceivedNotification::class,
            self::TYPE_INVOICE_CREATED => \App\Notifications\InvoiceCreatedNotification::class,
            self::TYPE_ABUSE_REPORT => \App\Notifications\AbuseReportNotification::class,
        ];

        return $classes[$type] ?? null;
    }

    /**
     * Get notification title
     */
    protected function getNotificationTitle(string $type): string
    {
        return match ($type) {
            self::TYPE_LEASE_EXPIRING => __('Lease Expiring Soon'),
            self::TYPE_LEASE_EXPIRED => __('Lease Expired'),
            self::TYPE_LEASE_CREATED => __('New Lease Created'),
            self::TYPE_LEASE_RENEWED => __('Lease Renewed'),
            self::TYPE_IP_BLACKLISTED => __('IP Address Blacklisted'),
            self::TYPE_IP_DELISTED => __('IP Address Delisted'),
            self::TYPE_PAYMENT_RECEIVED => __('Payment Received'),
            self::TYPE_PAYMENT_FAILED => __('Payment Failed'),
            self::TYPE_INVOICE_CREATED => __('New Invoice'),
            self::TYPE_INVOICE_OVERDUE => __('Invoice Overdue'),
            self::TYPE_PAYOUT_PROCESSED => __('Payout Processed'),
            self::TYPE_SUBNET_VERIFIED => __('Subnet Verified'),
            self::TYPE_SUBNET_SUSPENDED => __('Subnet Suspended'),
            self::TYPE_KYC_APPROVED => __('KYC Approved'),
            self::TYPE_KYC_REJECTED => __('KYC Rejected'),
            self::TYPE_ABUSE_REPORT => __('New Abuse Report'),
            self::TYPE_SECURITY_ALERT => __('Security Alert'),
            self::TYPE_TICKET_UPDATE => __('Ticket Updated'),
            default => __('Notification'),
        };
    }

    /**
     * Get notification message
     */
    protected function getNotificationMessage(string $type, array $data): string
    {
        return $data['message'] ?? $this->getNotificationTitle($type);
    }

    /**
     * Get notification color for Slack
     */
    protected function getNotificationColor(string $type): string
    {
        return match ($type) {
            self::TYPE_IP_BLACKLISTED,
            self::TYPE_PAYMENT_FAILED,
            self::TYPE_INVOICE_OVERDUE,
            self::TYPE_SUBNET_SUSPENDED,
            self::TYPE_KYC_REJECTED,
            self::TYPE_SECURITY_ALERT => 'danger',

            self::TYPE_LEASE_EXPIRING,
            self::TYPE_ABUSE_REPORT => 'warning',

            self::TYPE_LEASE_CREATED,
            self::TYPE_LEASE_RENEWED,
            self::TYPE_IP_DELISTED,
            self::TYPE_PAYMENT_RECEIVED,
            self::TYPE_PAYOUT_PROCESSED,
            self::TYPE_SUBNET_VERIFIED,
            self::TYPE_KYC_APPROVED => 'good',

            default => '#3AA3E3',
        };
    }

    /**
     * Format Telegram message
     */
    protected function formatTelegramMessage(string $type, array $data): string
    {
        $title = $this->getNotificationTitle($type);
        $message = $data['message'] ?? '';

        return "<b>{$title}</b>\n\n{$message}";
    }

    /**
     * Get all notification types
     */
    public static function getAllTypes(): array
    {
        return [
            self::TYPE_LEASE_EXPIRING,
            self::TYPE_LEASE_EXPIRED,
            self::TYPE_LEASE_CREATED,
            self::TYPE_LEASE_RENEWED,
            self::TYPE_IP_BLACKLISTED,
            self::TYPE_IP_DELISTED,
            self::TYPE_PAYMENT_RECEIVED,
            self::TYPE_PAYMENT_FAILED,
            self::TYPE_INVOICE_CREATED,
            self::TYPE_INVOICE_OVERDUE,
            self::TYPE_PAYOUT_PROCESSED,
            self::TYPE_SUBNET_VERIFIED,
            self::TYPE_SUBNET_SUSPENDED,
            self::TYPE_KYC_APPROVED,
            self::TYPE_KYC_REJECTED,
            self::TYPE_ABUSE_REPORT,
            self::TYPE_SECURITY_ALERT,
            self::TYPE_TICKET_UPDATE,
        ];
    }
}
