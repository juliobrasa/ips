# Soltia IPS Marketplace

A complete IPv4 address leasing marketplace platform built with Laravel 12. This platform enables IP address holders to list their subnets for lease and allows lessees to browse, lease, and manage IP address ranges.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red)
![PHP](https://img.shields.io/badge/PHP-8.3+-blue)
![License](https://img.shields.io/badge/License-Proprietary-green)

## Features

### For IP Holders
- **Subnet Management**: Add, edit, and manage IPv4 subnets
- **WHOIS Verification**: Automatic ownership verification via WHOIS lookup
- **IP Reputation Monitoring**: Integration with AbuseIPDB for reputation checks
- **Pricing Control**: Set monthly prices per IP address
- **LOA Generation**: Automatic Letter of Authorization generation for lessees
- **Payout Management**: Track earnings and request payouts

### For Lessees
- **Marketplace**: Browse available IP ranges with filtering options
- **Shopping Cart**: Add multiple subnets and checkout
- **Lease Management**: View active leases, assign ASN, renew or terminate
- **Invoice Management**: View and pay invoices
- **LOA Downloads**: Download authorization letters for upstream providers

### KYC Verification
- **Document Upload**: Identity documents (DNI/NIE/Passport for individuals, NIF/CIF for companies)
- **KYC Form**: Pre-filled downloadable PDF form
- **Signed KYC Upload**: Upload signed and stamped KYC documents
- **Admin Review**: Complete KYC review workflow with approve/reject/request info actions

### Admin Panel
- **Dashboard**: Real-time statistics and metrics
- **User Management**: Full CRUD, suspend/activate, email verification, impersonation
- **KYC Management**: Review and approve/reject KYC applications
- **Subnet Management**: Verify ownership, check reputation, suspend subnets
- **Lease Management**: Monitor all leases, extend or terminate
- **Financial Management**: Invoice tracking, payout processing, revenue reports

### Additional Features
- **Multi-language Support**: English and Spanish
- **Email Notifications**: Verification emails, lease notifications
- **PDF Generation**: LOA documents and KYC forms
- **Responsive Design**: Mobile-friendly Material Design interface

## Tech Stack

- **Framework**: Laravel 12.x
- **PHP**: 8.3+
- **Database**: MySQL 8.0+
- **Frontend**: Blade templates, Tailwind CSS, Alpine.js
- **PDF Generation**: DomPDF
- **Icons**: Material Icons
- **Web Server**: LiteSpeed / Apache / Nginx

## Requirements

- PHP 8.3 or higher
- MySQL 8.0 or higher
- Composer 2.x
- Node.js 18+ (for asset compilation)
- SSL Certificate (required for production)

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/juliobrasa/ips.git
cd ips
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node.js dependencies and build assets

```bash
npm install
npm run build
```

### 4. Environment configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your configuration:

```env
APP_NAME="Soltia IPS Marketplace"
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@your-domain.com

# AbuseIPDB API Key (for IP reputation checks)
ABUSEIPDB_API_KEY=your-api-key
```

### 5. Run migrations

```bash
php artisan migrate
```

### 6. Create storage link

```bash
php artisan storage:link
```

### 7. Set permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 8. Create admin user

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('your-password'),
    'role' => 'admin',
    'status' => 'active',
    'email_verified_at' => now(),
]);
```

## Configuration

### Web Server (LiteSpeed/Apache)

Ensure your web server points to the `/public` directory.

For Apache, enable `mod_rewrite` and use the included `.htaccess` file.

For LiteSpeed, the `.htaccess` file works automatically.

### SSL Certificate

SSL is required for production. You can use Let's Encrypt:

```bash
certbot --webroot -w /path/to/ips/public -d your-domain.com
```

### Cron Jobs

Add the Laravel scheduler to your crontab:

```bash
* * * * * cd /path/to/ips && php artisan schedule:run >> /dev/null 2>&1
```

## Directory Structure

```
ips/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/           # Admin panel controllers
│   │   │   ├── Auth/            # Authentication controllers
│   │   │   └── ...              # User-facing controllers
│   │   └── Middleware/
│   ├── Models/                  # Eloquent models
│   ├── Services/                # Business logic services
│   │   ├── IpReputationService.php
│   │   └── WhoisService.php
│   └── View/Components/         # Blade components
├── database/
│   └── migrations/              # Database migrations
├── lang/
│   ├── en.json                  # English translations
│   └── es.json                  # Spanish translations
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── admin/               # Admin panel views
│       ├── auth/                # Authentication views
│       ├── components/          # Reusable components
│       ├── layouts/             # Layout templates
│       ├── pdf/                 # PDF templates
│       └── ...                  # Feature views
├── routes/
│   ├── web.php                  # Web routes
│   └── auth.php                 # Authentication routes
└── storage/
    └── app/public/
        └── kyc-documents/       # Uploaded KYC documents
```

## Database Schema

### Main Tables

| Table | Description |
|-------|-------------|
| `users` | User accounts with role and status |
| `companies` | Company profiles and KYC information |
| `subnets` | IPv4 subnets available for lease |
| `leases` | Active and historical leases |
| `invoices` | Billing invoices |
| `payments` | Payment records |
| `payouts` | Holder payout requests |
| `loas` | Letter of Authorization records |
| `cart_items` | Shopping cart items |
| `abuse_reports` | IP abuse reports |

## API Integrations

### WHOIS Lookup
The platform performs WHOIS lookups to verify subnet ownership. It parses WHOIS data to extract:
- Organization name
- Admin/Tech contacts
- Abuse contact email
- Network range information

### AbuseIPDB
Integration with AbuseIPDB API for IP reputation checking:
- Confidence score
- Abuse reports count
- Last reported date
- ISP and usage type information

Configure your API key in `.env`:
```env
ABUSEIPDB_API_KEY=your-api-key
```

## User Roles

| Role | Description |
|------|-------------|
| `user` | Standard user (can be holder, lessee, or both) |
| `admin` | Full administrative access |

### Company Types

| Type | Description |
|------|-------------|
| `holder` | Can list subnets for lease |
| `lessee` | Can lease subnets from marketplace |
| `both` | Can both list and lease subnets |

## KYC Status Flow

```
pending → in_review → approved
                   → rejected → pending (can resubmit)
```

## Lease Status Flow

```
pending → active → expired
               → terminated
               → renewed → active
```

## Security Features

- CSRF protection on all forms
- XSS prevention with Blade escaping
- SQL injection prevention via Eloquent ORM
- Password hashing with bcrypt
- Email verification requirement
- KYC verification for transactions
- Admin-only access controls
- Rate limiting on authentication

## Screenshots

### Marketplace
Browse available IP ranges with filtering by size, region, and price.

### Admin Dashboard
Real-time statistics showing users, subnets, leases, and revenue.

### KYC Review
Complete workflow for reviewing and approving KYC applications.

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is proprietary software owned by Soltia.

## Support

For support, contact: support@soltia.io

---

## Changelog

### v1.0.0 (2025-12-06)
- Initial release
- User authentication and registration
- Company profiles with KYC verification
- Subnet management for IP holders
- Marketplace for browsing and leasing IPs
- Cart and checkout system
- Lease management with LOA generation
- Invoice and payout management
- Admin panel with full management capabilities
- Multi-language support (EN/ES)
- WHOIS integration
- AbuseIPDB integration
- PDF generation for LOA and KYC forms

---

**Built with Laravel and love by Soltia**
