# Deployment Guide

## Server Requirements

### Minimum Requirements
- **CPU**: 2 cores
- **RAM**: 2GB
- **Storage**: 20GB SSD
- **OS**: Ubuntu 20.04+ / CentOS 8+ / Debian 11+

### Recommended Requirements
- **CPU**: 4 cores
- **RAM**: 4GB
- **Storage**: 50GB SSD
- **OS**: Ubuntu 22.04 LTS

### Software Requirements
- PHP 8.3+
- MySQL 8.0+ or MariaDB 10.6+
- Composer 2.x
- Node.js 18+
- Web Server (LiteSpeed, Apache, or Nginx)
- SSL Certificate

## Installation Steps

### 1. System Preparation

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y curl git unzip software-properties-common

# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP and extensions
sudo apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-mbstring \
    php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath \
    php8.3-intl php8.3-readline

# Install MySQL
sudo apt install -y mysql-server

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. Database Setup

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

```sql
CREATE DATABASE ips_marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ips_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON ips_marketplace.* TO 'ips_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Application Deployment

```bash
# Create web directory
sudo mkdir -p /var/www/ips
sudo chown -R $USER:$USER /var/www/ips

# Clone repository
cd /var/www
git clone https://github.com/juliobrasa/ips.git

# Install dependencies
cd ips
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Environment setup
cp .env.example .env
php artisan key:generate

# Edit .env with your configuration
nano .env
```

### 4. Environment Configuration

Edit `.env` file:

```env
APP_NAME="Soltia IPS Marketplace"
APP_ENV=production
APP_KEY=base64:your_generated_key
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ips_marketplace
DB_USERNAME=ips_user
DB_PASSWORD=your_secure_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

ABUSEIPDB_API_KEY=your-api-key
```

### 5. Database Migration

```bash
php artisan migrate --force
php artisan storage:link
```

### 6. Permissions

```bash
sudo chown -R www-data:www-data /var/www/ips
sudo chmod -R 755 /var/www/ips
sudo chmod -R 775 /var/www/ips/storage
sudo chmod -R 775 /var/www/ips/bootstrap/cache
```

### 7. Web Server Configuration

#### Apache Configuration

```bash
sudo nano /etc/apache2/sites-available/ips.conf
```

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/ips/public

    <Directory /var/www/ips/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/ips-error.log
    CustomLog ${APACHE_LOG_DIR}/ips-access.log combined
</VirtualHost>
```

```bash
sudo a2ensite ips.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/ips
```

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/ips/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/ips /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### LiteSpeed / CyberPanel

For CyberPanel:
1. Create website via CyberPanel interface
2. Set document root to `/home/domain.com/ips/public`
3. Upload files to the directory
4. Configure vhost for rewrites if needed

### 8. SSL Certificate

Using Let's Encrypt:

```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache  # For Apache
sudo apt install certbot python3-certbot-nginx   # For Nginx

# Obtain certificate
sudo certbot --apache -d your-domain.com -d www.your-domain.com
# OR
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal (usually automatic)
sudo certbot renew --dry-run
```

### 9. Cron Jobs

```bash
crontab -e
```

Add:
```
* * * * * cd /var/www/ips && php artisan schedule:run >> /dev/null 2>&1
```

### 10. Create Admin User

```bash
cd /var/www/ips
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Administrator',
    'email' => 'admin@your-domain.com',
    'password' => Hash::make('secure_password_here'),
    'role' => 'admin',
    'status' => 'active',
    'email_verified_at' => now(),
]);

exit;
```

## Production Optimizations

### Cache Configuration

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Clear Cache (when updating)

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### OPcache Configuration

Edit `/etc/php/8.3/fpm/conf.d/10-opcache.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=64
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.fast_shutdown=1
```

## Updates

### Updating the Application

```bash
cd /var/www/ips

# Backup database
mysqldump -u ips_user -p ips_marketplace > backup_$(date +%Y%m%d).sql

# Pull changes
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www-data:www-data /var/www/ips
sudo chmod -R 775 storage bootstrap/cache
```

## Monitoring

### Log Files

- Laravel logs: `/var/www/ips/storage/logs/laravel.log`
- Apache logs: `/var/log/apache2/ips-*.log`
- Nginx logs: `/var/log/nginx/access.log`, `/var/log/nginx/error.log`
- PHP-FPM logs: `/var/log/php8.3-fpm.log`

### Health Checks

Monitor these endpoints:
- `https://your-domain.com/` - Homepage (should return 200)
- `https://your-domain.com/login` - Login page (should return 200)

## Troubleshooting

### Common Issues

**500 Error**
1. Check Laravel logs: `tail -f storage/logs/laravel.log`
2. Verify permissions on storage and bootstrap/cache
3. Check PHP extensions are installed
4. Verify .env configuration

**Permission Denied**
```bash
sudo chown -R www-data:www-data /var/www/ips
sudo chmod -R 755 /var/www/ips
sudo chmod -R 775 storage bootstrap/cache
```

**Database Connection Failed**
1. Verify MySQL is running: `sudo systemctl status mysql`
2. Check credentials in .env
3. Test connection: `mysql -u ips_user -p ips_marketplace`

**Asset Loading Issues**
1. Run `npm run build`
2. Verify `php artisan storage:link` was executed
3. Check web server configuration for public directory

## Security Checklist

- [ ] APP_DEBUG=false in production
- [ ] Strong database password
- [ ] SSL certificate installed
- [ ] File permissions correctly set
- [ ] Firewall configured (UFW/iptables)
- [ ] Regular backups scheduled
- [ ] Log rotation configured
- [ ] Keep software updated
