<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\SubnetRepositoryInterface;
use App\Repositories\Contracts\AbuseReportRepositoryInterface;
use App\Repositories\Contracts\LeaseRepositoryInterface;
use App\Repositories\Eloquent\SubnetRepository;
use App\Repositories\Eloquent\AbuseReportRepository;
use App\Repositories\Eloquent\LeaseRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [
        SubnetRepositoryInterface::class => SubnetRepository::class,
        AbuseReportRepositoryInterface::class => AbuseReportRepository::class,
        LeaseRepositoryInterface::class => LeaseRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        foreach ($this->bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
