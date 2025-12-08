<?php

namespace App\Notifications;

use App\Models\Subnet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubnetFlaggedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Subnet $subnet,
        public array $report,
        public string $recipientType = 'holder'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Subnet Flagged - {$this->subnet->cidr_notation}")
            ->greeting("Important: Subnet Status Update")
            ->error();

        if ($this->recipientType === 'holder') {
            $message->line("Your subnet {$this->subnet->cidr_notation} has been flagged due to reputation issues.");
        } elseif ($this->recipientType === 'lessee') {
            $message->line("A subnet you are leasing ({$this->subnet->cidr_notation}) has been flagged due to reputation issues.");
        } else {
            $message->line("Subnet {$this->subnet->cidr_notation} has been automatically flagged and suspended.");
        }

        $message->line("**Reputation Score:** {$this->report['overall_score']}/100")
            ->line("**Blocklist Count:** {$this->report['blocklist_count']}");

        if (!empty($this->report['blocklist_details'])) {
            $blocklists = collect($this->report['blocklist_details'])
                ->pluck('blocklist')
                ->take(5)
                ->implode(', ');
            $message->line("**Listed On:** {$blocklists}");
        }

        $message->line("**Recommendation:** {$this->report['recommendation']}");

        if ($this->recipientType === 'admin') {
            $message->action('Review Subnet', route('admin.subnets.show', $this->subnet));
        } else {
            $message->action('View Subnet', route('subnets.show', $this->subnet));
        }

        return $message->line('Please take action to delist this IP from the blocklists to restore its availability.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'subnet_flagged',
            'subnet_id' => $this->subnet->id,
            'subnet' => $this->subnet->cidr_notation,
            'score' => $this->report['overall_score'],
            'blocklist_count' => $this->report['blocklist_count'],
            'recipient_type' => $this->recipientType,
        ];
    }
}
