# Tasmimi - Smart Card Design Platform

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-10.x-red?style=flat-square&logo=laravel)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green?style=flat-square&logo=vue.js)
![Inertia.js](https://img.shields.io/badge/Inertia.js-0.6.x-purple?style=flat-square)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-blue?style=flat-square&logo=tailwindcss)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange?style=flat-square&logo=mysql)

*A comprehensive platform for designing and managing smart card templates with subscription-based access.*

[Features](#features) • [Installation](#installation) • [Configuration](#configuration) • [Deployment](#deployment) • [API Documentation](#api-documentation)

</div>

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Payment Integration](#payment-integration)
- [Deployment](#deployment)
- [Environment Variables](#environment-variables)
- [API Documentation](#api-documentation)
- [User Roles & Permissions](#user-roles--permissions)
- [Contributing](#contributing)
- [License](#license)
- [Support](#support)

---

## 🎯 Overview

**Tasmimi** is a modern, full-featured smart card design platform built with Laravel 10 and Vue.js 3. The platform allows users to create, customize, and manage smart card templates through an intuitive drag-and-drop interface powered by GrapesJS. It features a robust subscription system, payment integration with Moyasar, and comprehensive administrative tools.

### Key Highlights

- 🎨 **Visual Design Editor**: Drag-and-drop interface for card design
- 💳 **Payment Integration**: Secure payments via Moyasar gateway
- 📱 **Responsive Design**: Mobile-first approach with RTL support
- 🔐 **Role-based Access**: Multi-level user permissions
- 📊 **Analytics Dashboard**: Comprehensive reporting and insights
- 🌐 **Multi-language**: Arabic/English support
- ☁️ **Cloud Ready**: Optimized for production deployment

---

## ✨ Features

### 🎨 Design Tools
- **GrapesJS Editor**: Professional drag-and-drop design interface
- **Template Library**: Pre-built smart card templates
- **Asset Management**: Upload and manage design assets
- **Background Removal**: AI-powered background removal tools
- **Export Options**: Multiple export formats (PNG, JPG, PDF)

### 💼 Business Features
- **Subscription Management**: Flexible subscription plans (weekly, monthly, annual)
- **Payment Processing**: Secure payment handling with Moyasar
- **Template Marketplace**: Buy and sell premium templates
- **Client Portfolio**: Manage client designs and projects
- **Invoicing System**: Automated billing and invoicing

### 🛡️ Administration
- **User Management**: Complete user lifecycle management
- **Analytics Dashboard**: Revenue, usage, and performance metrics
- **Content Management**: Template and category administration
- **Customer Support**: Integrated messaging and support system
- **Security**: Role-based permissions and secure access

### 🔧 Technical Features
- **RESTful API**: Well-documented API endpoints
- **Real-time Updates**: Live design collaboration
- **File Management**: Cloud storage integration
- **Backup System**: Automated data backup
- **Performance Monitoring**: Application performance insights

---

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 10.x
- **Database**: MySQL 8.0+
- **Queue System**: Redis/Database queues
- **Storage**: Local/S3 compatible storage
- **Cache**: Redis/Memcached

### Frontend
- **Framework**: Vue.js 3.x
- **Build Tool**: Vite
- **Styling**: TailwindCSS 3.x
- **State Management**: Pinia
- **HTTP Client**: Axios

### Design & UI
- **Design Editor**: GrapesJS
- **Component Library**: TipTap Editor
- **Icons**: Heroicons
- **Responsive**: Mobile-first design
- **RTL Support**: Arabic language support

### DevOps & Tools
- **Server**: Nginx/Apache
- **Process Manager**: Supervisor
- **Deployment**: Git hooks/CI/CD
- **Monitoring**: Laravel Telescope
- **Testing**: PHPUnit, Jest

---

## 📋 System Requirements

### Minimum Requirements
- **PHP**: 8.1 or higher
- **Composer**: 2.0+
- **Node.js**: 18.0+
- **NPM/Yarn**: Latest stable
- **MySQL**: 8.0+
- **Redis**: 6.0+ (recommended)

### Recommended Server Specs
- **CPU**: 2+ cores
- **RAM**: 4GB minimum, 8GB recommended
- **Storage**: 20GB+ SSD
- **Bandwidth**: 100Mbps+

### PHP Extensions
```bash
# Required PHP extensions
php-curl, php-dom, php-fileinfo, php-filter, php-hash
php-intl, php-json, php-mbstring, php-openssl
php-pcre, php-pdo, php-session, php-tokenizer, php-xml
```

---

## 🚀 Installation

### 1. Clone Repository
```bash
git clone https://github.com/alaya-dev/tasmimi.git
cd tasmimi
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
npm install
```

### 3. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create storage symlink
php artisan storage:link
```

### 4. Database Setup
```bash
# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### 5. Build Assets
```bash
# Development build
npm run dev

# Production build
npm run build
```

### 6. Set Permissions
```bash
# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## ⚙️ Configuration

### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tasmimi
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Moyasar Payment Configuration
```env
MOYASAR_PUBLISHABLE_KEY=pk_test_your_key
MOYASAR_SECRET_KEY=sk_test_your_key
MOYASAR_WEBHOOK_SECRET=whsec_your_secret
```

### Queue Configuration
```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Mail Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@tasmimi.com"
MAIL_FROM_NAME="Tasmimi Platform"
```

---

## 🗃️ Database Setup

### Create Database
```sql
CREATE DATABASE tasmimi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Auto-expire Subscriptions (Recommended)
```sql
-- Create MySQL event to automatically expire subscriptions
CREATE EVENT IF NOT EXISTS update_expired_subscriptions_event
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
COMMENT 'Automatically update expired subscriptions'
DO
UPDATE user_subscriptions 
SET status = 'expired', updated_at = NOW()
WHERE status = 'active' AND ends_at < NOW();
```

### Enable MySQL Events
```sql
-- Ensure events are enabled
SET GLOBAL event_scheduler = ON;

-- Verify events are running
SHOW VARIABLES LIKE 'event_scheduler';
```

---

## 💳 Payment Integration

### Moyasar Setup

1. **Create Moyasar Account**
   - Visit [Moyasar Dashboard](https://moyasar.com)
   - Complete KYC verification
   - Get API keys from dashboard

2. **Configure Webhooks**
   ```
   Webhook URL: https://yourdomain.com/moyasar/webhook
   Events: payment.paid, payment.failed
   ```

3. **Test Payment Flow**
   ```bash
   # Test in sandbox mode first
   php artisan tinker
   
   # Create test subscription
   $user = App\Models\User::factory()->create(['role' => 'client']);
   $subscription = App\Models\Subscription::first();
   ```

### Supported Payment Methods
- 💳 Credit/Debit Cards (Visa, Mastercard, Mada)
- 📱 STC Pay
- 🏦 Bank Transfer (for enterprise)

---

## 🚀 Deployment

### Production Deployment

#### 1. Server Setup (Ubuntu 20.04+)
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install nginx mysql-server php8.1-fpm php8.1-mysql \
    php8.1-curl php8.1-json php8.1-mbstring php8.1-xml \
    php8.1-zip redis-server supervisor -y

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y
```

#### 2. Nginx Configuration
```nginx
# /etc/nginx/sites-available/tasmimi
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/tasmimi/public;
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/css text/javascript application/javascript application/json;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### 3. SSL Setup (Let's Encrypt)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Get SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

#### 4. Queue Worker Setup
```ini
# /etc/supervisor/conf.d/tasmimi-worker.conf
[program:tasmimi-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/tasmimi/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/tasmimi/storage/logs/worker.log
stopwaitsecs=3600
```

#### 5. Cron Jobs
```bash
# Add to crontab (crontab -e)
* * * * * cd /var/www/tasmimi && php artisan schedule:run >> /dev/null 2>&1
```

### Docker Deployment (Alternative)

#### Dockerfile
```dockerfile
FROM php:8.1-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    && docker-php-ext-install pdo_mysql

# Copy application
COPY . /var/www/tasmimi
WORKDIR /var/www/tasmimi

# Install dependencies
RUN composer install --optimize-autoloader --no-dev
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
```

#### Docker Compose
```yaml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "80:80"
    environment:
      - APP_ENV=production
      - DB_HOST=db
    depends_on:
      - db
      - redis

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: tasmimi
    volumes:
      - mysql_data:/var/lib/mysql

  redis:
    image: redis:7-alpine
    
volumes:
  mysql_data:
```

---

## 🔐 Environment Variables

### Essential Variables
```env
# Application
APP_NAME="Tasmimi"
APP_ENV=production
APP_KEY=base64:your_generated_key
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tasmimi
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Payment Gateway (Moyasar)
MOYASAR_PUBLISHABLE_KEY=pk_live_your_key
MOYASAR_SECRET_KEY=sk_live_your_key
MOYASAR_WEBHOOK_SECRET=whsec_your_secret

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Tasmimi"

# File Storage
FILESYSTEM_DISK=local
# For S3: FILESYSTEM_DISK=s3
# AWS_ACCESS_KEY_ID=your_key
# AWS_SECRET_ACCESS_KEY=your_secret
# AWS_DEFAULT_REGION=us-east-1
# AWS_BUCKET=your_bucket

# Services
PEXELS_API_KEY=your_pexels_key
REMOVE_BG_API_KEY=your_removebg_key
```

---

## 📚 API Documentation

### Authentication
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

### Subscription Management
```http
# Get user subscriptions
GET /api/subscriptions
Authorization: Bearer {token}

# Create subscription payment
POST /api/subscriptions/{id}/payment
Authorization: Bearer {token}
Content-Type: application/json

{
    "payment_method": "creditcard"
}
```

### Template Operations
```http
# Get templates
GET /api/templates?category=business&page=1

# Create design
POST /api/client/designs
Authorization: Bearer {token}
Content-Type: application/json

{
    "template_id": 1,
    "design_data": {...},
    "name": "My Design"
}
```

### Admin API
```http
# Get dashboard stats
GET /api/admin/dashboard
Authorization: Bearer {admin_token}

# Manage users
GET /api/admin/users
POST /api/admin/users
PUT /api/admin/users/{id}
DELETE /api/admin/users/{id}
```

---

## 👥 User Roles & Permissions

### Role Hierarchy
```
Super Admin (super_admin)
├── Full system access
├── User management
├── Subscription management
├── Financial reports
└── System settings

Admin (admin)
├── Template management
├── Client management
├── Order processing
└── Basic analytics

Client (client)
├── Design creation
├── Template access
├── Subscription management
└── Profile management
```

### Permission Matrix
| Feature | Super Admin | Admin | Client |
|---------|-------------|-------|--------|
| User Management | ✅ | ✅ | ❌ |
| Template Creation | ✅ | ✅ | ❌ |
| Design Editor | ✅ | ✅ | ✅ |
| Subscription Purchase | ✅ | ✅ | ✅ |
| Financial Reports | ✅ | ❌ | ❌ |
| System Settings | ✅ | ❌ | ❌ |

---

## 🧪 Testing

### Run Tests
```bash
# Unit tests
php artisan test

# Feature tests
php artisan test --testsuite=Feature

# Browser tests
php artisan dusk

# JavaScript tests
npm test
```

### Test Coverage
```bash
# Generate coverage report
php artisan test --coverage

# Generate HTML coverage report
php artisan test --coverage-html coverage
```

---

## 🔧 Maintenance

### Regular Tasks
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Database maintenance
php artisan queue:restart
php artisan schedule:run

# Storage cleanup
php artisan storage:cleanup
```

### Monitoring
```bash
# Check system status
php artisan health:check

# Monitor queues
php artisan queue:monitor

# Check failed jobs
php artisan queue:failed
```

---

## 🐛 Troubleshooting

### Common Issues

#### 1. Payment Failures
```bash
# Check webhook configuration
tail -f storage/logs/laravel.log | grep moyasar

# Verify webhook URL
curl -X POST https://yourdomain.com/moyasar/webhook \
  -H "Content-Type: application/json" \
  -d '{"test": true}'
```

#### 2. Queue Issues
```bash
# Restart queue workers
sudo supervisorctl restart tasmimi-worker:*

# Clear failed jobs
php artisan queue:flush
```

#### 3. Permission Issues
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache
```

#### 4. Database Connection
```bash
# Test database connection
php artisan tinker
DB::connection()->getPdo();
```

---

## 🤝 Contributing

We welcome contributions! Please follow these guidelines:

### Development Setup
```bash
# Fork and clone
git clone https://github.com/yourusername/tasmimi.git
cd tasmimi

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Run development server
php artisan serve
npm run dev
```

### Code Standards
- Follow PSR-12 coding standards
- Write comprehensive tests
- Document all public methods
- Use meaningful commit messages

### Pull Request Process
1. Create feature branch
2. Write tests for new functionality
3. Ensure all tests pass
4. Update documentation
5. Submit pull request

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🆘 Support

### Documentation
- [API Documentation](https://docs.tasmimi.com)
- [User Guide](https://help.tasmimi.com)
- [Video Tutorials](https://tutorials.tasmimi.com)

### Community
- [Discord Server](https://discord.gg/tasmimi)
- [GitHub Discussions](https://github.com/alaya-dev/tasmimi/discussions)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/tasmimi)

### Professional Support
- Email: support@tasmimi.com
- Phone: +966 XX XXX XXXX
- Business Hours: Sunday-Thursday, 9 AM - 6 PM (AST)

### Bug Reports
Please report bugs through [GitHub Issues](https://github.com/alaya-dev/tasmimi/issues) with:
- Detailed description
- Steps to reproduce
- Expected vs actual behavior
- System information
- Screenshots (if applicable)

---

## 🏆 Acknowledgments

- [Laravel Framework](https://laravel.com) - The PHP framework for web artisans
- [Vue.js](https://vuejs.org) - The progressive JavaScript framework
- [GrapesJS](https://grapesjs.com) - Free and open source web builder framework
- [TailwindCSS](https://tailwindcss.com) - A utility-first CSS framework
- [Moyasar](https://moyasar.com) - Saudi payment gateway
- [Inertia.js](https://inertiajs.com) - The modern monolith

---

<div align="center">

**Made with ❤️ by the Tasmimi Team**

[Website](https://tasmimi.com) • [Documentation](https://docs.tasmimi.com) • [Support](mailto:support@tasmimi.com)

</div>