# Database Documentation

## Overview

The Soltia IPS Marketplace uses MySQL 8.0+ as its database. This document describes all tables, their relationships, and field definitions.

## Entity Relationship Diagram

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│   Users     │───────│  Companies  │───────│   Subnets   │
└─────────────┘       └─────────────┘       └─────────────┘
      │                     │                     │
      │                     │                     │
      ▼                     ▼                     ▼
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│ Cart Items  │       │   Leases    │◄──────│   LOAs      │
└─────────────┘       └─────────────┘       └─────────────┘
                            │
                            ▼
                      ┌─────────────┐       ┌─────────────┐
                      │  Invoices   │───────│  Payments   │
                      └─────────────┘       └─────────────┘
                            │
                            ▼
                      ┌─────────────┐
                      │   Payouts   │
                      └─────────────┘
```

## Tables

### users

User accounts for authentication and authorization.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar(255) | User's full name |
| email | varchar(255) | Unique email address |
| email_verified_at | timestamp | Email verification date |
| password | varchar(255) | Hashed password |
| role | enum('user', 'admin') | User role (default: 'user') |
| status | enum('active', 'inactive', 'suspended') | Account status |
| phone | varchar(50) | Phone number (nullable) |
| remember_token | varchar(100) | Remember me token |
| created_at | timestamp | Creation date |
| updated_at | timestamp | Last update date |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE (email)

### companies

Company profiles linked to users, containing KYC information.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key to users |
| company_name | varchar(255) | Company or individual name |
| legal_name | varchar(255) | Legal registered name |
| tax_id | varchar(50) | Tax ID (NIF/CIF/VAT) |
| country | varchar(100) | Country |
| address | text | Full address |
| city | varchar(100) | City |
| postal_code | varchar(20) | Postal/ZIP code |
| company_type | enum('holder', 'lessee', 'both') | Type of account |
| entity_type | enum('individual', 'company') | Entity type |
| identity_document_type | varchar(50) | Document type (dni, nie, passport, nif, cif) |
| identity_document_number | varchar(50) | Document number |
| identity_document_file | varchar(255) | Path to uploaded document |
| identity_document_uploaded_at | timestamp | Upload date |
| kyc_signed_document | varchar(255) | Path to signed KYC |
| kyc_signed_uploaded_at | timestamp | Signed KYC upload date |
| legal_representative_name | varchar(255) | Legal rep name (for companies) |
| legal_representative_id | varchar(50) | Legal rep ID number |
| legal_representative_position | varchar(100) | Legal rep position |
| kyc_status | enum('pending', 'in_review', 'approved', 'rejected') | KYC status |
| kyc_documents | json | Legacy document paths |
| kyc_approved_at | timestamp | KYC approval date |
| kyc_notes | text | Admin notes |
| kyc_reviewed_at | timestamp | Review date |
| kyc_reviewed_by | bigint | Admin who reviewed |
| payout_method | varchar(50) | Payout method (bank_transfer, etc.) |
| payout_details | json | Payout information |
| created_at | timestamp | Creation date |
| updated_at | timestamp | Last update date |

**Indexes:**
- PRIMARY KEY (id)
- FOREIGN KEY (user_id) REFERENCES users(id)
- FOREIGN KEY (kyc_reviewed_by) REFERENCES users(id)

### subnets

IPv4 subnets available for lease.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| company_id | bigint | Foreign key to companies (owner) |
| network | varchar(18) | Network address (e.g., 192.168.1.0) |
| cidr | tinyint | CIDR notation (8-32) |
| ip_count | int | Number of IPs in range |
| rir | varchar(20) | Regional Internet Registry |
| country | varchar(100) | Country location |
| description | text | Subnet description |
| price_per_ip | decimal(10,2) | Monthly price per IP |
| minimum_lease_months | int | Minimum lease duration |
| status | enum('pending', 'verified', 'available', 'leased', 'suspended') | Subnet status |
| verification_token | varchar(64) | Email verification token |
| verification_email | varchar(255) | Email for verification |
| verified_at | timestamp | Verification date |
| whois_data | json | Cached WHOIS data |
| reputation_score | int | IP reputation score |
| reputation_checked_at | timestamp | Last reputation check |
| created_at | timestamp | Creation date |
| updated_at | timestamp | Last update date |

**Indexes:**
- PRIMARY KEY (id)
- FOREIGN KEY (company_id) REFERENCES companies(id)
- UNIQUE (network, cidr)

### leases

Active and historical IP leases.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| subnet_id | bigint | Foreign key to subnets |
| holder_company_id | bigint | IP holder company |
| lessee_company_id | bigint | Lessee company |
| start_date | date | Lease start date |
| end_date | date | Lease end date |
| monthly_price | decimal(10,2) | Monthly lease price |
| total_price | decimal(10,2) | Total lease price |
| status | enum('pending', 'active', 'expired', 'terminated', 'renewed') | Lease status |
| assigned_asn | varchar(20) | Assigned ASN (if any) |
| notes | text | Additional notes |
| created_at | timestamp | Creation date |
| updated_at | timestamp | Last update date |

**Indexes:**
- PRIMARY KEY (id)
- FOREIGN KEY (subnet_id) REFERENCES subnets(id)
- FOREIGN KEY (holder_company_id) REFERENCES companies(id)
- FOREIGN KEY (lessee_company_id) REFERENCES companies(id)

### loas

Letter of Authorization records.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| lease_id | bigint | Foreign key to leases |
| loa_number | varchar(50) | Unique LOA identifier |
| issued_at | timestamp | Issue date |
| valid_until | date | Expiration date |
| file_path | varchar(255) | Path to generated PDF |
| verification_code | varchar(32) | Code for verification |
| created_at | timestamp | Creation date |
| updated_at | timestamp | Last update date |

**Indexes:**
- PRIMARY KEY (id)
- FOREIGN KEY (lease_id) REFERENCES leases(id)
- UNIQUE (loa_number)
- INDEX (verification_code)

### invoices

Billing invoices for leases.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| company_id | bigint | Foreign key to companies |
| lease_id | bigint | Foreign key to leases |
| invoice_number | varchar(50) | Unique invoice number |
| amount | decimal(10,2) | Invoice amount |
| tax_amount | decimal(10,2) | Tax amount |
| total_amount | decimal(10,2) | Total with tax |
| status | enum('pending', 'paid', 'overdue', 'cancelled') | Invoice status |
| due_date | date | Payment due date |
| paid_at | timestamp | Payment date |
| billing_period_start | date | Billing period start |
| billing_period_end | date | Billing period end |
| notes | text | Additional notes |
| created_at | timestamp | Creation date |
| updated_at | timestamp | Last update date |

**Indexes:**
- PRIMARY KEY (id)
- FOREIGN KEY (company_id) REFERENCES companies(id)
- FOREIGN KEY (lease_id) REFERENCES leases(id)
- UNIQUE (invoice_number)

### payments

Payment records for invoices.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| invoice_id | bigint | Foreign key to invoices |
| amount | decimal(10,2) | Payment amount |
| payment_method | varchar(50) | Payment method |
| transaction_id | varchar(100) | External transaction ID |
| status | enum('pending', 'completed', 'failed', 'refunded') | Payment status |
| paid_at | timestamp | Payment date |
| notes | text | Additional notes |
| created_at | timestamp | Creation date |
| updated_at | timestamp | Last update date |

**Indexes:**
- PRIMARY KEY (id)
- FOREIGN KEY (invoice_id) REFERENCES invoices(id)

### payouts

Payout requests from holders.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| company_id | bigint | Foreign key to companies |
| amount | decimal(10,2) | Payout amount |
| fee | decimal(10,2) | Processing fee |
| net_amount | decimal(10,2) | Net amount after fees |
| status | enum('pending', 'processing', 'completed', 'failed') | Payout status |
| payout_method | varchar(50) | Payout method |
| payout_details | json | Payout destination details |
| processed_at | timestamp | Processing date |
| completed_at | timestamp | Completion date |
| reference | varchar(100) | External reference |
| notes | text | Additional notes |
| created_at | timestamp | Creation date |
| updated_at | timestamp | Last update date |

**Indexes:**
- PRIMARY KEY (id)
- FOREIGN KEY (company_id) REFERENCES companies(id)

### cart_items

Shopping cart items.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key to users |
| subnet_id | bigint | Foreign key to subnets |
| months | int | Lease duration in months |
| created_at | timestamp | Creation date |
| updated_at | timestamp | Last update date |

**Indexes:**
- PRIMARY KEY (id)
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY (subnet_id) REFERENCES subnets(id) ON DELETE CASCADE
- UNIQUE (user_id, subnet_id)

### abuse_reports

IP abuse reports.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| subnet_id | bigint | Foreign key to subnets |
| reporter_ip | varchar(45) | Reporter's IP address |
| report_type | varchar(50) | Type of abuse |
| description | text | Detailed description |
| evidence | json | Supporting evidence |
| status | enum('pending', 'investigating', 'resolved', 'dismissed') | Report status |
| resolved_at | timestamp | Resolution date |
| resolution_notes | text | Resolution details |
| created_at | timestamp | Creation date |
| updated_at | timestamp | Last update date |

**Indexes:**
- PRIMARY KEY (id)
- FOREIGN KEY (subnet_id) REFERENCES subnets(id)

### sessions

Laravel session storage.

| Column | Type | Description |
|--------|------|-------------|
| id | varchar(255) | Session ID |
| user_id | bigint | User ID (nullable) |
| ip_address | varchar(45) | Client IP |
| user_agent | text | Browser user agent |
| payload | longtext | Session data |
| last_activity | int | Last activity timestamp |

### cache

Laravel cache storage.

| Column | Type | Description |
|--------|------|-------------|
| key | varchar(255) | Cache key |
| value | mediumtext | Cached value |
| expiration | int | Expiration timestamp |

### jobs

Laravel queue jobs.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| queue | varchar(255) | Queue name |
| payload | longtext | Job payload |
| attempts | tinyint | Attempt count |
| reserved_at | int | Reserved timestamp |
| available_at | int | Available timestamp |
| created_at | int | Creation timestamp |

## Migrations

Run migrations in order:
```bash
php artisan migrate
```

Rollback:
```bash
php artisan migrate:rollback
```

Fresh migration (drops all tables):
```bash
php artisan migrate:fresh
```

## Seeding

Database seeders are available for testing:
```bash
php artisan db:seed
```

## Backup

Recommended backup strategy:
```bash
mysqldump -u username -p database_name > backup.sql
```

Or use Laravel's backup package for automated backups.
