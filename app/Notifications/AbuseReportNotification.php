<?php

namespace App\Notifications;

use App\Models\AbuseReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbuseReportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public AbuseReport $report,
        public string $recipientType = 'holder'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subnet = $this->report->subnet;
        $severityLabel = strtoupper($this->report->severity);

        $message = (new MailMessage)
            ->subject("[{$severityLabel}] Abuse Report - {$subnet->cidr_notation}")
            ->greeting("Abuse Report Alert");

        if ($this->recipientType === 'holder') {
            $message->line("An abuse report has been filed for your subnet {$subnet->cidr_notation}.");
        } elseif ($this->recipientType === 'lessee') {
            $message->line("An abuse report has been filed for a subnet you are leasing: {$subnet->cidr_notation}.");
        } else {
            $message->line("A new abuse report requires admin attention for subnet {$subnet->cidr_notation}.");
        }

        $message->line("**Type:** {$this->report->type}")
            ->line("**Severity:** {$this->report->severity}")
            ->line("**Source:** {$this->report->source}")
            ->line("**Description:** " . \Str::limit($this->report->description, 200));

        if ($this->recipientType === 'admin') {
            $message->action('Review Report', route('admin.security.abuse-reports.show', $this->report));
        } else {
            $message->action('View Details', route('subnets.show', $subnet));
        }

        return $message->line('Please take appropriate action to address this issue.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'abuse_report',
            'report_id' => $this->report->id,
            'subnet_id' => $this->report->subnet_id,
            'subnet' => $this->report->subnet->cidr_notation,
            'abuse_type' => $this->report->type,
            'severity' => $this->report->severity,
            'recipient_type' => $this->recipientType,
        ];
    }
}
