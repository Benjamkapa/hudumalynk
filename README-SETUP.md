# HudumaLynk — Setup & Deployment Guide

This guide details how to deploy the HudumaLynk digital marketplace platform on a Linux VM (Ubuntu 22.04 LTS recommended) using Nginx, PHP 8.1-FPM, and MySQL.

## 1. Server Requirements
- **OS**: Ubuntu 22.04 LTS
- **Web Server**: Nginx
- **PHP**: 8.1 or 8.2 (with extensions: `pdo_mysql`, `mbstring`, `dom`, `curl`, `gd`, `zip`)
- **Database**: MySQL 8.0 or MariaDB 10.6+
- **Composer**: v2.x

## 2. Environment Setup

### Install Dependencies
```bash
sudo apt update
sudo apt install -y nginx mysql-server php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-gd php8.1-zip unzip curl
```

### Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## 3. Application Deployment

1. **Clone the specific repository to `/var/www/hudumalynk`**
```bash
sudo mkdir -p /var/www/hudumalynk
sudo chown -R $USER:$USER /var/www/hudumalynk
# clone your repo...
```

2. **Install Vendor Packages**
```bash
cd /var/www/hudumalynk
composer install --no-dev --optimize-autoloader
```

3. **Configure Environment**
```bash
cp .env.example .env
nano .env  # Update DB credentials, M-Pesa sandbox/live keys, and domain URLs
```

4. **Set Permissions**
```bash
# Allow Nginx to write to upload and runtime directories
sudo chgrp -R www-data frontend/web/uploads backend/web/uploads frontend/runtime backend/runtime console/runtime
sudo chmod -R 775 frontend/web/uploads backend/web/uploads frontend/runtime backend/runtime console/runtime
```

5. **Initialize Database**
Ensure MySQL is running, create a database `hudumalynk` matching your `.env`, then run:
```bash
php yii migrate --interactive=0
php yii setup/init
```
*This handles the full DB schema, inserts seed data, and creates the Admin user.*

## 4. Nginx Configuration

Create two Nginx server blocks — one for the Frontend (Customer) and one for the Backend (Admin/Provider).

### Create standard configuration
`sudo nano /etc/nginx/sites-available/hudumalynk.conf`

```nginx
# ── FRONTEND (Customer Portal) ────────────────────────
server {
    listen 80;
    server_name www.hudumalynk.co.ke hudumalynk.co.ke;
    root /var/www/hudumalynk/frontend/web;
    index index.php index.html;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    # Optional: Serve uploads natively without passing to index.php
    location ^~ /uploads/ {
        alias /var/www/hudumalynk/frontend/web/uploads/;
        access_log off;
        expires max;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

# ── BACKEND (Admin/Provider Portal) ────────────────────
server {
    listen 80;
    server_name admin.hudumalynk.co.ke providers.hudumalynk.co.ke;
    root /var/www/hudumalynk/backend/web;
    index index.php index.html;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    # Mount the frontend uploads folder into backend namespace to share images
    location ^~ /uploads/ {
        alias /var/www/hudumalynk/frontend/web/uploads/;
        access_log off;
        expires max;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Enable the site and restart Nginx
```bash
sudo ln -s /etc/nginx/sites-available/hudumalynk.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## 5. Cron Jobs Setup

Yii2 console commands must run automatically to expire subscriptions and send SMS reminders. Add these to the `www-data` or root crontab:

```bash
sudo crontab -u www-data -e
```

Add the following logic:
```cron
# Check explicitly for expired provider subscriptions each midnight
0 0 * * * cd /var/www/hudumalynk && /usr/bin/php yii subscription/check-expiry > /dev/null 2>&1

# Send 7-day subscription expiry reminders at 9:00 AM daily
0 9 * * * cd /var/www/hudumalynk && /usr/bin/php yii subscription/send-reminders > /dev/null 2>&1
```

## 6. Going Live (SSL & M-Pesa Callback)

1. Use Certbot to configure HTTPS: `sudo certbot --nginx -d hudumalynk.co.ke -d admin.hudumalynk.co.ke`
2. Once HTTPS is active, change `MPESA_ENV=live` in your `.env`. The M-Pesa Callback (`https://hudumalynk.co.ke/order/mpesa-callback`) is automatically resolved correctly. Note: M-Pesa **requires** an internet-routable TLS 1.2+ endpoint.

## 7. Success
* Customer Portal: `http://hudumalynk.co.ke`
* Admin Dashboard: `http://admin.hudumalynk.co.ke` (Credentials are configured in your `.env` file under **Admin Credentials**)

The deployment is now fully active!
