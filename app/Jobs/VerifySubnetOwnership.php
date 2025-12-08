<?php

namespace App\Jobs;

use App\Models\Subnet;
use App\Services\WhoisService;
use App\Events\OwnershipVerificationCompleted;
use App\Notifications\SubnetVerificationEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VerifySubnetOwnership implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;
    public int $timeout = 90;

    public function __construct(
        public Subnet $subnet
    ) {}

    public function handle(WhoisService $whoisService): void
    {
        Log::info("Verifying ownership for subnet {$this->subnet->cidr_notation}");

        try {
            $whoisData = $whoisService->query($this->subnet->ip_address, $this->subnet->rir);

            if (!$whoisData || !isset($whoisData['abuse_email'])) {
                // Auto-verify if no WHOIS data (will require admin review)
                $this->subnet->update([
                    'ownership_verified_at' => now(),
                    'status' => 'pending_review',
                ]);

                Log::warning("Subnet {$this->subnet->cidr_notation} auto-verified due to missing WHOIS data");

                event(new OwnershipVerificationCompleted($this->subnet, false, 'No WHOIS data available'));
                return;
            }

            $abuseEmail = $whoisData['abuse_email'];

            // Generate verification token
            if (!$this->subnet->verification_token) {
                $this->subnet->update(['verification_token' => Str::random(64)]);
            }

            // Send verification email
            Mail::send('emails.subnet-verification', [
                'subnet' => $this->subnet,
                'verificationUrl' => route('subnets.confirm-verification', [
                    'subnet' => $this->subnet->id,
                    'token' => $this->subnet->verification_token,
                ]),
                'company' => $this->subnet->company,
            ], function ($message) use ($abuseEmail) {
                $message->to($abuseEmail)
                    ->subject("Verify IP Ownership for {$this->subnet->cidr_notation} - Soltia IPS Marketplace");
            });

            Log::info("Verification email sent for subnet {$this->subnet->cidr_notation} to {$abuseEmail}");

        } catch (\Exception $e) {
            Log::error("Failed to verify ownership for subnet {$this->subnet->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function tags(): array
    {
        return ['ownership-verification', 'subnet:' . $this->subnet->id];
    }
}
