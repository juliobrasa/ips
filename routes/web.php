<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\SubnetController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\LoaController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

// Language switcher
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public Marketplace (viewable without login)
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/marketplace/{subnet}', [MarketplaceController::class, 'show'])->name('marketplace.show');

// Public subnet verification (link from email)
Route::get('/subnets/{subnet}/confirm/{token}', [SubnetController::class, 'confirmVerification'])->name('subnets.confirm-verification');

// Public LOA verification
Route::post('/verify-loa', [LoaController::class, 'verify'])->name('loa.verify');

// Public Help Center
Route::get('/help', [HelpController::class, 'index'])->name('help.index');
Route::get('/help/{slug}', [HelpController::class, 'show'])->name('help.show');

// Two-Factor Authentication Challenge (before full auth)
Route::get('/two-factor-challenge', [TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
Route::post('/two-factor-challenge', [TwoFactorController::class, 'verify'])->name('two-factor.verify');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Two-Factor Authentication Management
    Route::get('/two-factor', [TwoFactorController::class, 'index'])->name('two-factor.index');
    Route::get('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::delete('/two-factor', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    Route::post('/two-factor/regenerate-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.regenerate-codes');

    // KYC Documents
    Route::get('/profile/kyc-documents', [App\Http\Controllers\KycDocumentController::class, 'index'])->name('kyc.documents');
    Route::post('/profile/kyc-documents/identity', [App\Http\Controllers\KycDocumentController::class, 'uploadIdentityDocument'])->name('kyc.upload-identity');
    Route::post('/profile/kyc-documents/signed', [App\Http\Controllers\KycDocumentController::class, 'uploadSignedKyc'])->name('kyc.upload-signed');
    Route::get('/profile/kyc-documents/download-form', [App\Http\Controllers\KycDocumentController::class, 'downloadKycForm'])->name('kyc.download-form');
    Route::get('/profile/kyc-documents/view-form', [App\Http\Controllers\KycDocumentController::class, 'viewKycForm'])->name('kyc.view-form');
    Route::post('/profile/kyc-documents/submit', [App\Http\Controllers\KycDocumentController::class, 'submitForReview'])->name('kyc.submit-review');

    // Company / KYC
    Route::get('/company/create', [CompanyController::class, 'create'])->name('company.create');
    Route::post('/company', [CompanyController::class, 'store'])->name('company.store');
    Route::get('/company/edit', [CompanyController::class, 'edit'])->name('company.edit');
    Route::patch('/company', [CompanyController::class, 'update'])->name('company.update');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{subnet}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // Subnets (for Holders)
    Route::resource('subnets', SubnetController::class);
    Route::post('/subnets/{subnet}/verify', [SubnetController::class, 'verify'])->name('subnets.verify');
    Route::post('/subnets/{subnet}/check-reputation', [SubnetController::class, 'checkReputation'])->name('subnets.check-reputation');
    Route::get('/subnets/{subnet}/whois', [SubnetController::class, 'getWhoisData'])->name('subnets.whois');

    // Leases
    Route::get('/leases', [LeaseController::class, 'index'])->name('leases.index');
    Route::get('/leases/{lease}', [LeaseController::class, 'show'])->name('leases.show');
    Route::post('/leases/{lease}/assign-asn', [LeaseController::class, 'assignAsn'])->name('leases.assign-asn');
    Route::post('/leases/{lease}/renew', [LeaseController::class, 'renew'])->name('leases.renew');
    Route::post('/leases/{lease}/terminate', [LeaseController::class, 'terminate'])->name('leases.terminate');

    // LOA
    Route::get('/leases/{lease}/loa', [LoaController::class, 'generate'])->name('loa.generate');
    Route::get('/loa/{loa}/download', [LoaController::class, 'download'])->name('loa.download');
    Route::get('/loa/{loa}/view', [LoaController::class, 'view'])->name('loa.view');

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::post('/invoices/{invoice}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');

    // Payouts (for Holders)
    Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts.index');
    Route::get('/payouts/{payout}', [PayoutController::class, 'show'])->name('payouts.show');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [App\Http\Controllers\Admin\UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/suspend', [App\Http\Controllers\Admin\UserManagementController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/activate', [App\Http\Controllers\Admin\UserManagementController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/verify-email', [App\Http\Controllers\Admin\UserManagementController::class, 'verifyEmail'])->name('users.verify-email');
    Route::post('/users/{user}/impersonate', [App\Http\Controllers\Admin\UserManagementController::class, 'impersonate'])->name('users.impersonate');
    Route::post('/stop-impersonating', [App\Http\Controllers\Admin\UserManagementController::class, 'stopImpersonating'])->name('stop-impersonating');

    // KYC Management
    Route::get('/kyc', [App\Http\Controllers\Admin\KycController::class, 'index'])->name('kyc.index');
    Route::get('/kyc/{company}', [App\Http\Controllers\Admin\KycController::class, 'show'])->name('kyc.show');
    Route::get('/kyc/{company}/review', [App\Http\Controllers\Admin\KycController::class, 'review'])->name('kyc.review');
    Route::post('/kyc/{company}/approve', [App\Http\Controllers\Admin\KycController::class, 'approve'])->name('kyc.approve');
    Route::post('/kyc/{company}/reject', [App\Http\Controllers\Admin\KycController::class, 'reject'])->name('kyc.reject');
    Route::post('/kyc/{company}/request-info', [App\Http\Controllers\Admin\KycController::class, 'requestInfo'])->name('kyc.request-info');

    // Subnet Management
    Route::get('/subnets', [App\Http\Controllers\Admin\SubnetManagementController::class, 'index'])->name('subnets.index');
    Route::get('/subnets/{subnet}', [App\Http\Controllers\Admin\SubnetManagementController::class, 'show'])->name('subnets.show');
    Route::post('/subnets/{subnet}/verify', [App\Http\Controllers\Admin\SubnetManagementController::class, 'verify'])->name('subnets.verify');
    Route::post('/subnets/{subnet}/suspend', [App\Http\Controllers\Admin\SubnetManagementController::class, 'suspend'])->name('subnets.suspend');
    Route::post('/subnets/{subnet}/unsuspend', [App\Http\Controllers\Admin\SubnetManagementController::class, 'unsuspend'])->name('subnets.unsuspend');
    Route::post('/subnets/{subnet}/check-reputation', [App\Http\Controllers\Admin\SubnetManagementController::class, 'checkReputation'])->name('subnets.check-reputation');
    Route::post('/subnets/bulk-reputation', [App\Http\Controllers\Admin\SubnetManagementController::class, 'bulkReputationCheck'])->name('subnets.bulk-reputation');

    // Lease Management
    Route::get('/leases', [App\Http\Controllers\Admin\LeaseManagementController::class, 'index'])->name('leases.index');
    Route::get('/leases/{lease}', [App\Http\Controllers\Admin\LeaseManagementController::class, 'show'])->name('leases.show');
    Route::post('/leases/{lease}/terminate', [App\Http\Controllers\Admin\LeaseManagementController::class, 'terminate'])->name('leases.terminate');
    Route::post('/leases/{lease}/extend', [App\Http\Controllers\Admin\LeaseManagementController::class, 'extend'])->name('leases.extend');

    // Finance
    Route::get('/finance/invoices', [App\Http\Controllers\Admin\FinanceController::class, 'invoices'])->name('finance.invoices');
    Route::get('/finance/payouts', [App\Http\Controllers\Admin\FinanceController::class, 'payouts'])->name('finance.payouts');
    Route::post('/finance/payouts/{payout}/process', [App\Http\Controllers\Admin\FinanceController::class, 'processPayout'])->name('finance.payouts.process');
    Route::post('/finance/payouts/{payout}/complete', [App\Http\Controllers\Admin\FinanceController::class, 'completePayout'])->name('finance.payouts.complete');
    Route::get('/finance/revenue', [App\Http\Controllers\Admin\FinanceController::class, 'revenueReport'])->name('finance.revenue');

    // Document Templates
    Route::get('/documents', [App\Http\Controllers\Admin\DocumentTemplateController::class, 'index'])->name('documents.index');
    Route::get('/documents/{template}/preview', [App\Http\Controllers\Admin\DocumentTemplateController::class, 'preview'])->name('documents.preview');
    Route::get('/documents/{template}/download', [App\Http\Controllers\Admin\DocumentTemplateController::class, 'download'])->name('documents.download');

    // Security & IP Reputation
    Route::get('/security', [App\Http\Controllers\Admin\SecurityController::class, 'index'])->name('security.index');
    Route::get('/security/blocklist-check', [App\Http\Controllers\Admin\SecurityController::class, 'blocklistCheck'])->name('security.blocklist-check');
    Route::post('/security/check-ip', [App\Http\Controllers\Admin\SecurityController::class, 'checkIp'])->name('security.check-ip');
    Route::post('/security/check-subnet', [App\Http\Controllers\Admin\SecurityController::class, 'checkSubnet'])->name('security.check-subnet');
    Route::get('/security/abuse-reports', [App\Http\Controllers\Admin\SecurityController::class, 'abuseReports'])->name('security.abuse-reports');
    Route::get('/security/abuse-reports/{report}', [App\Http\Controllers\Admin\SecurityController::class, 'showAbuseReport'])->name('security.abuse-reports.show');
    Route::post('/security/abuse-reports/{report}/resolve', [App\Http\Controllers\Admin\SecurityController::class, 'resolveAbuseReport'])->name('security.abuse-reports.resolve');
    Route::post('/security/abuse-reports/{report}/dismiss', [App\Http\Controllers\Admin\SecurityController::class, 'dismissAbuseReport'])->name('security.abuse-reports.dismiss');

    // IP Health Management
    Route::get('/ip-health', [App\Http\Controllers\Admin\IpHealthController::class, 'index'])->name('ip-health.index');
    Route::get('/ip-health/dashboard', [App\Http\Controllers\Admin\IpHealthController::class, 'dashboard'])->name('ip-health.dashboard');
    Route::get('/ip-health/subnets-at-risk', [App\Http\Controllers\Admin\IpHealthController::class, 'subnetsAtRisk'])->name('ip-health.at-risk');
    Route::post('/ip-health/schedule-check', [App\Http\Controllers\Admin\IpHealthController::class, 'scheduleCheck'])->name('ip-health.schedule-check');
    Route::post('/ip-health/bulk-check', [App\Http\Controllers\Admin\IpHealthController::class, 'bulkCheck'])->name('ip-health.bulk-check');

    // Blacklist Delisting Management
    Route::get('/delisting', [App\Http\Controllers\Admin\DelistingController::class, 'index'])->name('delisting.index');
    Route::get('/delisting/pending', [App\Http\Controllers\Admin\DelistingController::class, 'pending'])->name('delisting.pending');
    Route::get('/delisting/{request}', [App\Http\Controllers\Admin\DelistingController::class, 'show'])->name('delisting.show');
    Route::post('/delisting/{request}/check-status', [App\Http\Controllers\Admin\DelistingController::class, 'checkStatus'])->name('delisting.check-status');
    Route::post('/delisting/{request}/mark-completed', [App\Http\Controllers\Admin\DelistingController::class, 'markCompleted'])->name('delisting.mark-completed');
    Route::post('/delisting/request', [App\Http\Controllers\Admin\DelistingController::class, 'createRequest'])->name('delisting.request');

    // Audit Logs
    Route::get('/audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{log}', [App\Http\Controllers\Admin\AuditLogController::class, 'show'])->name('audit-logs.show');
});

require __DIR__.'/auth.php';
