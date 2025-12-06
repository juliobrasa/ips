# API Documentation

## Overview

The Soltia IPS Marketplace currently operates as a web application with server-rendered views. This document describes the internal routes and endpoints available.

## Authentication

All authenticated routes require a valid session. Authentication is handled via Laravel's built-in authentication system with email verification.

### Login
- **POST** `/login`
- **Parameters**: `email`, `password`, `remember`

### Register
- **POST** `/register`
- **Parameters**: `name`, `email`, `password`, `password_confirmation`

### Logout
- **POST** `/logout`

### Password Reset
- **POST** `/forgot-password` - Request reset link
- **POST** `/reset-password` - Reset with token

## Public Routes

### Home
- **GET** `/` - Landing page

### Marketplace
- **GET** `/marketplace` - List all available subnets
- **GET** `/marketplace/{subnet}` - View subnet details

### Help Center
- **GET** `/help` - Help center index
- **GET** `/help/{slug}` - Help article

### LOA Verification
- **POST** `/verify-loa` - Verify LOA authenticity

### Subnet Verification (Email Link)
- **GET** `/subnets/{subnet}/confirm/{token}` - Confirm subnet ownership

## Authenticated Routes

All routes below require authentication and email verification.

### Dashboard
- **GET** `/dashboard` - User dashboard

### Profile
- **GET** `/profile` - Edit profile
- **PATCH** `/profile` - Update profile
- **DELETE** `/profile` - Delete account

### KYC Documents
- **GET** `/profile/kyc-documents` - KYC documents page
- **POST** `/profile/kyc-documents/identity` - Upload identity document
- **POST** `/profile/kyc-documents/signed` - Upload signed KYC
- **GET** `/profile/kyc-documents/download-form` - Download KYC PDF
- **GET** `/profile/kyc-documents/view-form` - Preview KYC form
- **POST** `/profile/kyc-documents/submit` - Submit for review

### Company
- **GET** `/company/create` - Create company form
- **POST** `/company` - Store company
- **GET** `/company/edit` - Edit company
- **PATCH** `/company` - Update company

### Cart
- **GET** `/cart` - View cart
- **POST** `/cart/add/{subnet}` - Add to cart
- **PATCH** `/cart/{cartItem}` - Update cart item
- **DELETE** `/cart/{cartItem}` - Remove from cart
- **POST** `/cart/checkout` - Process checkout

### Subnets (Holders)
- **GET** `/subnets` - List my subnets
- **GET** `/subnets/create` - Create subnet form
- **POST** `/subnets` - Store subnet
- **GET** `/subnets/{subnet}` - View subnet
- **GET** `/subnets/{subnet}/edit` - Edit subnet form
- **PATCH** `/subnets/{subnet}` - Update subnet
- **DELETE** `/subnets/{subnet}` - Delete subnet
- **POST** `/subnets/{subnet}/verify` - Request verification
- **POST** `/subnets/{subnet}/check-reputation` - Check IP reputation
- **GET** `/subnets/{subnet}/whois` - Get WHOIS data

### Leases
- **GET** `/leases` - List my leases
- **GET** `/leases/{lease}` - View lease details
- **POST** `/leases/{lease}/assign-asn` - Assign ASN to lease
- **POST** `/leases/{lease}/renew` - Renew lease
- **POST** `/leases/{lease}/terminate` - Terminate lease

### LOA (Letter of Authorization)
- **GET** `/leases/{lease}/loa` - Generate LOA
- **GET** `/loa/{loa}/download` - Download LOA PDF
- **GET** `/loa/{loa}/view` - View LOA

### Invoices
- **GET** `/invoices` - List invoices
- **GET** `/invoices/{invoice}` - View invoice
- **GET** `/invoices/{invoice}/download` - Download PDF
- **POST** `/invoices/{invoice}/pay` - Pay invoice

### Payouts (Holders)
- **GET** `/payouts` - List payouts
- **GET** `/payouts/{payout}` - View payout details

## Admin Routes

All admin routes require authentication and admin role. Prefix: `/admin`

### Dashboard
- **GET** `/admin` - Admin dashboard

### User Management
- **GET** `/admin/users` - List users
- **GET** `/admin/users/create` - Create user form
- **POST** `/admin/users` - Store user
- **GET** `/admin/users/{user}` - View user
- **GET** `/admin/users/{user}/edit` - Edit user form
- **PUT** `/admin/users/{user}` - Update user
- **DELETE** `/admin/users/{user}` - Delete user
- **POST** `/admin/users/{user}/suspend` - Suspend user
- **POST** `/admin/users/{user}/activate` - Activate user
- **POST** `/admin/users/{user}/verify-email` - Verify email
- **POST** `/admin/users/{user}/impersonate` - Impersonate user
- **POST** `/admin/stop-impersonating` - Stop impersonation

### KYC Management
- **GET** `/admin/kyc` - List KYC applications
- **GET** `/admin/kyc/{company}` - View KYC details
- **GET** `/admin/kyc/{company}/review` - Review KYC
- **POST** `/admin/kyc/{company}/approve` - Approve KYC
- **POST** `/admin/kyc/{company}/reject` - Reject KYC
- **POST** `/admin/kyc/{company}/request-info` - Request more info

### Subnet Management
- **GET** `/admin/subnets` - List all subnets
- **GET** `/admin/subnets/{subnet}` - View subnet
- **POST** `/admin/subnets/{subnet}/verify` - Verify subnet
- **POST** `/admin/subnets/{subnet}/suspend` - Suspend subnet
- **POST** `/admin/subnets/{subnet}/unsuspend` - Unsuspend subnet
- **POST** `/admin/subnets/{subnet}/check-reputation` - Check reputation
- **POST** `/admin/subnets/bulk-reputation` - Bulk reputation check

### Lease Management
- **GET** `/admin/leases` - List all leases
- **GET** `/admin/leases/{lease}` - View lease
- **POST** `/admin/leases/{lease}/terminate` - Terminate lease
- **POST** `/admin/leases/{lease}/extend` - Extend lease

### Finance
- **GET** `/admin/finance/invoices` - All invoices
- **GET** `/admin/finance/payouts` - All payouts
- **POST** `/admin/finance/payouts/{payout}/process` - Process payout
- **POST** `/admin/finance/payouts/{payout}/complete` - Complete payout
- **GET** `/admin/finance/revenue` - Revenue report

## Response Formats

All web routes return HTML views. For AJAX requests, JSON responses are returned where applicable.

### Success Response
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": { ... }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field": ["Error message"]
    }
}
```

## Rate Limiting

- Authentication routes: 5 attempts per minute
- API routes: 60 requests per minute (when implemented)

## CSRF Protection

All POST, PUT, PATCH, DELETE requests require a valid CSRF token. Include the token in forms:

```html
@csrf
```

Or in AJAX headers:
```javascript
headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
}
```
