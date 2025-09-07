# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 11 e-commerce API application (api.e-apple.co.kr) built with PHP 8.2+. The application provides APIs for an e-commerce platform with product management, order processing, user authentication, and payment integration.

## Development Setup

Before running any commands, ensure dependencies are installed:
```bash
composer install
```

### Essential Development Commands

```bash
# Start development server with all services (server, queue, logs, vite)
composer run dev

# Run individual services
php artisan serve              # Start the development server
php artisan queue:listen --tries=1  # Start queue worker
php artisan pail --timeout=0   # Watch logs in real-time

# Database commands
php artisan migrate             # Run migrations
php artisan migrate:rollback   # Rollback last migration
php artisan migrate:fresh      # Drop all tables and re-run migrations
php artisan db:seed             # Seed the database

# Code quality
./vendor/bin/pint               # Laravel code formatter (Pint)
php artisan test                # Run PHPUnit tests

# Cache and optimization
php artisan cache:clear         # Clear application cache
php artisan config:clear        # Clear config cache
php artisan route:clear         # Clear route cache
php artisan optimize            # Optimize for production
```

## Architecture Overview

### API Structure
The application follows a RESTful API architecture with JWT authentication. Main API endpoints are organized under:
- `/api/` - Public and authenticated user endpoints
- `/api/admin/` - Admin-only endpoints (requires AdminMiddleware)

### Core Domain Models
- **User/Auth**: JWT-based authentication with social login support (Kakao)
- **Products**: Product catalog with categories, options, packages, and sweetness levels
- **Orders**: Order processing with status tracking and payment integration (Iamport)
- **Cart**: Shopping cart with product options management
- **Reviews & Inquiries**: Product reviews and customer inquiries
- **Coupons & Points**: Discount and loyalty system
- **Delivery**: Address management and shipping tracking

### Key Enums (app/Enums/)
- `OrderStatus`: Order lifecycle states
- `ProductCategory`: Product categorization
- `DeliveryCompany`: Shipping providers
- `ExchangeReturnStatus`: Return/exchange workflow
- `UserLevel`: Customer tier system
- `IamportMethod`: Payment methods

### Payment Integration
Uses Iamport (Korean payment gateway) for processing payments. Configuration in `config/iamport.php`.

### Scheduled Commands (app/Console/Commands/)
- `DestroyIncompletePaymentOrder`: Clean up incomplete orders
- `UpdateOrderDelivery`: Update delivery status from carriers
- `UpdateUserLevel`: Update user tier levels
- `ExpirePoints`: Handle point expiration

### External Services
- **Solapi SDK**: SMS messaging service for notifications
- **Socialite with Kakao**: Social login integration
- **Spatie Media Library**: File and image management

### API Documentation
The project uses Knuckles/Scribe for API documentation generation:
```bash
php artisan scribe:generate
```

### Testing Approach
Uses PHPUnit for testing. Test files are located in `tests/` directory. Run specific tests with:
```bash
php artisan test --filter TestClassName
php artisan test tests/Feature/ExampleTest.php
```

### Database
Default configuration uses SQLite for development. Production typically uses MySQL. Database configuration is in `config/database.php`.

### Queue Processing
Uses database queue driver by default. Ensure queue worker is running for background jobs:
```bash
php artisan queue:work
```

## Code Conventions

- Follow PSR-12 coding standards
- Use Laravel's built-in helpers and facades
- Models use Eloquent ORM with relationships defined
- Form requests for validation (app/Http/Requests/)
- Resource classes for API responses (app/Http/Resources/)
- Enums for constants and status values
- Service classes for complex business logic