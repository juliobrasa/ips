<?php

namespace App\Providers;

use App\Events\AbuseReportCreated;
use App\Events\AbuseReportResolved;
use App\Events\BulkReputationCheckCompleted;
use App\Events\DelistingRequestProcessed;
use App\Events\LeaseCreated;
use App\Events\LeaseTerminated;
use App\Events\OwnershipVerificationCompleted;
use App\Events\ReputationCheckCompleted;
use App\Events\SubnetFlaggedForAbuse;
use App\Listeners\LogAuditTrail;
use App\Listeners\NotifySubnetFlagged;
use App\Listeners\SendAbuseReportNotification;
use App\Listeners\UpdateSubnetStatusOnLease;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Abuse Reports
        AbuseReportCreated::class => [
            SendAbuseReportNotification::class,
            LogAuditTrail::class,
        ],

        AbuseReportResolved::class => [
            LogAuditTrail::class,
        ],

        // Subnet Events
        SubnetFlaggedForAbuse::class => [
            NotifySubnetFlagged::class,
            LogAuditTrail::class,
        ],

        ReputationCheckCompleted::class => [
            LogAuditTrail::class,
        ],

        OwnershipVerificationCompleted::class => [
            LogAuditTrail::class,
        ],

        // Lease Events
        LeaseCreated::class => [
            [UpdateSubnetStatusOnLease::class, 'handleLeaseCreated'],
            LogAuditTrail::class,
        ],

        LeaseTerminated::class => [
            [UpdateSubnetStatusOnLease::class, 'handleLeaseTerminated'],
            LogAuditTrail::class,
        ],

        // Bulk Operations
        BulkReputationCheckCompleted::class => [
            LogAuditTrail::class,
        ],

        DelistingRequestProcessed::class => [
            LogAuditTrail::class,
        ],

        // Auth Events
        Login::class => [
            LogAuditTrail::class,
        ],

        Logout::class => [
            LogAuditTrail::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
