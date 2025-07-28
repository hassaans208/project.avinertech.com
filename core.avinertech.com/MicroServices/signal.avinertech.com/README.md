<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Signal Handler System - Laravel Multi-Tenant Package Management

A comprehensive Laravel 11 application for handling centralized signal processing with multi-tenant package management.

## Architecture

This system implements the **Repository-Service Pattern** with:
- **Repository Layer**: Abstracts data access with interfaces
- **Service Layer**: Contains business logic and orchestrates repositories  
- **Controller Layer**: Handles HTTP requests and responses
- **Exception Layer**: Custom exceptions for specific error handling

## Features

- 🔐 **Encrypted Host ID Processing**: Decrypt and validate tenant hosts
- 📦 **Package Management**: Multi-tier packages with modules and pricing
- 👥 **Multi-Tenant Support**: Automatic tenant creation with free packages
- ⏰ **Token Expiration**: 1-hour token validity with timestamp validation
- 📊 **Signal Logging**: Complete audit trail of all signal processing
- 🔒 **Status Management**: Active/Inactive/Blocked tenant states
- ✅ **Comprehensive Testing**: Unit and feature tests included

## Installation & Setup

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=signal_handler
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Run Migrations & Seeders

```bash
php artisan migrate
php artisan db:seed
```

This creates:
- **Packages**: `free_package`, `basic_package`, `professional_package`, `enterprise_package`
- **Sample Tenants**: Including active and blocked examples

### 4. Start Development Server

```bash
php artisan serve
npm run dev
```

## API Usage

### Signal Processing Endpoint

**POST** `/{encryptedHostId}/signal`

#### Request Format

```json
{
    "hash": "{package_name}:{YYYY}:{MM-DD}:{HH}:{hostId}"
}
```

#### Hash Format Example
```
basic_package:2024:01-15:14:example.com
```

Where:
- `package_name`: Snake case package name
- `YYYY`: 4-digit year
- `MM-DD`: Month and day
- `HH`: Hour (24-hour format)
- `hostId`: Plain text host identifier

#### Success Response

```json
{
    "success": true,
    "data": {
        "tenant_id": 1,
        "tenant_host": "example.com",
        "tenant_name": "Example Tenant",
        "package_id": 2,
        "package_name": "basic_package",
        "package_cost": "29.99",
        "package_currency": "USD",
        "package_tax_rate": "0.0825",
        "package_modules": ["api_access", "analytics", "custom_domains"],
        "signal_timestamp": "2024-01-15T14:00:00.000000Z",
        "processed_at": "2024-01-15T14:05:30.000000Z",
        "expires_at": "2024-01-15T15:05:30.000000Z"
    },
    "signature": "sha256_hmac_signature"
}
```

#### Error Response

```json
{
    "error": "Invalid Client – contact sales@avinertech.com"
}
```

## Data Models

### Tenants
- `id`, `name`, `host`, `status`, `block_reason`, `timestamps`
- Status: `active`, `inactive`, `blocked`
- Relationship: Many-to-Many with Packages

### Packages  
- `id`, `name`, `cost`, `currency`, `tax_rate`, `modules`, `timestamps`
- Modules: JSON array of feature flags
- Relationship: Many-to-Many with Tenants

### Signal Logs
- Complete audit trail of all signal processing attempts
- Stores encrypted/decrypted hosts, timestamps, errors, responses

## Business Logic Flow

1. **Decrypt** `encryptedHostId` → host string
2. **Lookup** Tenant by host; if none → create tenant + assign free_package  
3. **Validate** tenant status (active, not blocked)
4. **Parse** hash format and validate timestamp (within 1 hour)
5. **Verify** hostId matches decrypted host
6. **Load** package details and return signed response
7. **Log** all processing events for audit

## Web Interface Routes

### Tenant Management
- `GET /tenants` - List all tenants
- `GET /tenants/create` - Create tenant form
- `POST /tenants` - Store new tenant
- `GET /tenants/{id}` - View tenant details
- `GET /tenants/{id}/edit` - Edit tenant form
- `PUT /tenants/{id}` - Update tenant
- `POST /tenants/{id}/block` - Block tenant
- `POST /tenants/{id}/unblock` - Unblock tenant

### Package Management
- `GET /packages` - List all packages
- `GET /packages/create` - Create package form
- `POST /packages` - Store new package
- `GET /packages/{id}` - View package details
- `GET /packages/{id}/edit` - Edit package form
- `PUT /packages/{id}` - Update package

## Testing

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suites
```bash
# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only  
php artisan test --testsuite=Feature

# Specific test class
php artisan test tests/Unit/SignalServiceTest.php
```

### Test Coverage
- **Unit Tests**: SignalService logic, repository patterns
- **Feature Tests**: API endpoints, database interactions
- **Edge Cases**: Invalid inputs, expired tokens, blocked tenants

## Available Packages

| Package | Cost | Modules |
|---------|------|---------|
| `free_package` | $0.00 | `api_access` |
| `basic_package` | $29.99 | `api_access`, `analytics`, `custom_domains` |
| `professional_package` | $99.99 | All basic + `ai_integration`, `payment_methods`, `priority_support` |
| `enterprise_package` | $299.99 | All professional + `white_label`, `advanced_security` |

## Available Modules

- `ai_integration` - AI-powered features
- `payment_methods` - Payment gateway integrations  
- `analytics` - Advanced analytics dashboard
- `custom_domains` - Custom domain support
- `api_access` - API access (included in all packages)
- `priority_support` - Priority customer support
- `white_label` - White-label customization
- `advanced_security` - Enhanced security features

## Error Handling

The system uses custom exceptions for specific error scenarios:

- `DecryptionException` - Invalid encrypted host ID
- `TokenExpiredException` - Expired timestamp (>1 hour)
- `InvalidTenantException` - Blocked or inactive tenant
- `InvalidHashFormatException` - Malformed hash format
- `SignalException` - Base exception for all signal errors

All errors return HTTP 400 with the standard error message for security.

## Security Features

- **Encrypted Host IDs**: Laravel's built-in encryption
- **HMAC Signatures**: Response payload signing
- **Token Expiration**: 1-hour maximum token age
- **Status Validation**: Blocked tenant protection
- **Audit Logging**: Complete signal processing history
- **Input Validation**: Comprehensive request validation

## Development Notes

- Built on **Laravel 11.31** with PHP 8.2+
- Uses **Repository Pattern** for clean architecture
- **Service Provider** bindings for dependency injection
- **Form Requests** for validation
- **Eloquent Relationships** for data modeling
- **Carbon** for date/time handling

## Deployment Checklist

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`  
3. Configure production database
4. Run `php artisan config:cache`
5. Run `php artisan route:cache`
6. Run `php artisan view:cache`
7. Set up SSL certificates
8. Configure web server (Nginx/Apache)
9. Set up monitoring and logging

---

**Contact**: For technical support, contact sales@avinertech.com
