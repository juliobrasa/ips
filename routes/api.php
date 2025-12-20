<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\SubnetController;
use App\Http\Controllers\Api\V1\LeaseController;
use App\Http\Controllers\Api\V1\AbuseReportController;
use App\Http\Controllers\Api\V1\ReputationController;
use App\Http\Controllers\Api\V1\MarketplaceController;
use App\Http\Controllers\Api\V1\BlacklistController;
use App\Http\Controllers\Api\V1\RipeApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// API Version 1
Route::prefix('v1')->group(function () {

    // Public routes
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    // Public marketplace
    Route::get('/marketplace', [MarketplaceController::class, 'index']);
    Route::get('/marketplace/{subnet}', [MarketplaceController::class, 'show']);

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/user', [AuthController::class, 'user']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);

        // Subnets (for holders)
        Route::apiResource('subnets', SubnetController::class);
        Route::post('/subnets/{subnet}/verify', [SubnetController::class, 'verify']);
        Route::post('/subnets/{subnet}/check-reputation', [SubnetController::class, 'checkReputation']);
        Route::get('/subnets/{subnet}/whois', [SubnetController::class, 'whois']);
        Route::get('/subnets/{subnet}/reputation-history', [SubnetController::class, 'reputationHistory']);

        // Leases
        Route::apiResource('leases', LeaseController::class)->only(['index', 'show']);
        Route::post('/leases/{lease}/assign-asn', [LeaseController::class, 'assignAsn']);
        Route::post('/leases/{lease}/renew', [LeaseController::class, 'renew']);
        Route::post('/leases/{lease}/terminate', [LeaseController::class, 'terminate']);
        Route::get('/leases/{lease}/loa', [LeaseController::class, 'getLoa']);

        // Abuse Reports
        Route::apiResource('abuse-reports', AbuseReportController::class);
        Route::post('/abuse-reports/{report}/acknowledge', [AbuseReportController::class, 'acknowledge']);

        // Reputation & Blacklists
        Route::get('/reputation/check/{ip}', [ReputationController::class, 'check']);
        Route::get('/reputation/subnet/{subnet}', [ReputationController::class, 'checkSubnet']);
        Route::get('/reputation/blocklists', [ReputationController::class, 'listBlocklists']);

        // Blacklist Management
        Route::get('/blacklists/status/{subnet}', [BlacklistController::class, 'status']);
        Route::post('/blacklists/request-delisting', [BlacklistController::class, 'requestDelisting']);
        Route::get('/blacklists/delisting-requests', [BlacklistController::class, 'listRequests']);
        Route::get('/blacklists/delisting-requests/{request}', [BlacklistController::class, 'showRequest']);

        // RIPE Integration
        Route::prefix('ripe')->group(function () {
            // RIPEstat Data API
            Route::get('/info', [RipeApiController::class, 'getInfo']);
            Route::get('/geolocation', [RipeApiController::class, 'getGeolocation']);
            Route::get('/abuse-contact', [RipeApiController::class, 'getAbuseContact']);
            Route::get('/whois', [RipeApiController::class, 'getWhois']);
            Route::get('/routing-status', [RipeApiController::class, 'getRoutingStatus']);
            Route::get('/bgp-state', [RipeApiController::class, 'getBgpState']);
            Route::get('/asn/{asn}/prefixes', [RipeApiController::class, 'getAnnouncedPrefixes']);
            Route::get('/asn/{asn}/overview', [RipeApiController::class, 'getAsOverview']);
            Route::get('/endpoints', [RipeApiController::class, 'listEndpoints']);

            // RIPE Database API
            Route::get('/database/object', [RipeApiController::class, 'getDatabaseObject']);
            Route::get('/database/search', [RipeApiController::class, 'searchDatabase']);

            // Subnet-specific RIPE operations
            Route::put('/subnets/{subnet}/inetnum', [RipeApiController::class, 'updateInetnum']);
            Route::post('/subnets/{subnet}/route', [RipeApiController::class, 'createRoute']);
            Route::get('/subnets/{subnet}/routes', [RipeApiController::class, 'getRoutes']);
        });

        // Admin routes
        Route::middleware('admin')->prefix('admin')->group(function () {
            Route::get('/stats', [SubnetController::class, 'adminStats']);
            Route::post('/subnets/{subnet}/suspend', [SubnetController::class, 'suspend']);
            Route::post('/subnets/{subnet}/unsuspend', [SubnetController::class, 'unsuspend']);
            Route::post('/abuse-reports/{report}/resolve', [AbuseReportController::class, 'resolve']);
            Route::post('/abuse-reports/{report}/dismiss', [AbuseReportController::class, 'dismiss']);
            Route::post('/reputation/bulk-check', [ReputationController::class, 'bulkCheck']);
        });
    });
});
