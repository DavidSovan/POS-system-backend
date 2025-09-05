# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

This is a Laravel-based Point of Sale (POS) system backend API that provides user management, authentication with role-based access control, and **comprehensive product & inventory management**. The system is designed for retail environments with three primary user roles:

- **Admin**: Full system access including user management, product/inventory management, sales, and system configuration
- **Manager**: Product/inventory management, sales, and reporting access
- **Cashier**: Sales-related operations only

## Architecture

### Authentication & Authorization
- **JWT Authentication**: Uses `tymon/jwt-auth` for stateless API authentication
- **Role-Based Access Control**: Implemented through custom Policies and role-based permissions
- **User Roles**: Admin, Manager, and Cashier with hierarchical permissions

### Key Models
- **User**: Main user model with JWT authentication, linked to roles
- **Role**: Defines user permissions and access levels
- **Product**: Core inventory item with SKU, pricing, stock levels, and category
- **Category**: Product categorization for better organization
- **Supplier**: Supplier information for inventory management
- **StockMovement**: Complete audit trail of all stock changes (in/out movements)
- User-Role relationship is established through foreign key constraint

### API Structure
- RESTful API design with consistent JSON response format
- All API routes are prefixed with `/api/`
- Authentication routes: login, logout, refresh, register (admin-only)
- User management with CRUD operations and authorization checks
- Future modules planned: sales, inventory, reports, system config

### Database Design
- **Users**: name, email, password, role_id, status (pending/active/suspended/deactivated)
- **Roles**: predefined roles with permissions system
- **Products**: id, name, sku, category_id, price, cost, stock, reorder_level, status
- **Categories**: id, name, description, status (with default categories: General, Food & Beverage, Electronics)
- **Suppliers**: id, name, contact_info, email, phone, address, status
- **StockMovements**: id, product_id, type (in/out), quantity, reason, user_id, stock_after, created_at
- Default admin user created during migration: `admin@pos-system.com` / `Admin@123`

## Development Commands

### Environment Setup
```bash
# Copy environment file and configure
cp .env.example .env

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret

# Run database migrations
php artisan migrate

# Start development environment (runs server, queue, logs, and frontend)
composer dev
```

### Individual Services
```bash
# Start Laravel development server
php artisan serve

# Start Vite development server
npm run dev

# Build frontend assets for production
npm run build

# Start queue worker
php artisan queue:listen

# Monitor logs in real-time
php artisan pail
```

### Testing
```bash
# Run all tests
composer test
# OR
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run tests with coverage
php artisan test --coverage

# Run single test file
php artisan test tests/Feature/ExampleTest.php

# Run tests with specific filter
php artisan test --filter testExample
```

### Code Quality
```bash
# Run Laravel Pint (code formatter)
vendor/bin/pint

# Clear application caches
php artisan optimize:clear
# OR
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Database Operations
```bash
# Fresh migration (drops all tables and recreates)
php artisan migrate:fresh

# Rollback migrations
php artisan migrate:rollback

# Check migration status
php artisan migrate:status

# Create new migration
php artisan make:migration create_example_table

# Create model with migration
php artisan make:model Example -m
```

### Development Utilities
```bash
# Enter Laravel tinker (REPL)
php artisan tinker

# List all routes
php artisan route:list

# Generate IDE helper files (if installed)
php artisan ide-helper:generate
```

## Architecture Patterns

### Request Validation
All API endpoints use Form Request classes for validation:
- `LoginRequest`: Email and password validation
- `RegisterRequest`: User creation with role validation
- `UpdateUserRequest`: User update validation with role-based restrictions
- `StoreProductRequest`: Product creation with category and SKU validation
- `UpdateProductRequest`: Product update validation with unique constraints
- `StockAdjustmentRequest`: Stock movement validation with quantity and reason checks

### Authorization Pattern
Uses Laravel Gates and Policies for fine-grained access control:
- `UserPolicy`: Handles all user-related authorization logic
- Role-based methods: `isAdmin()`, `isManager()`, `isCashier()`
- Permission checks: `hasPermission()` method on User model

### API Response Format
Consistent JSON response structure:
```json
{
  "status": "success|error",
  "data": { /* response data */ },
  "code": "ERR_CODE" // (for errors)
}
```

### Future Architecture Considerations
The system is designed to be extensible with planned modules:
- Sales management with transaction tracking
- Inventory management with stock control
- Reporting system with sales analytics
- System configuration management

## Technology Stack
- **Backend**: Laravel 12 with PHP 8.2+
- **Authentication**: JWT via tymon/jwt-auth
- **Database**: MySQL (configurable)
- **Frontend Build**: Vite with Tailwind CSS 4.0
- **Testing**: PHPUnit
- **Code Quality**: Laravel Pint

## Database Seeding
Default data is created during migration:
- Three roles: Admin, Manager, Cashier
- Default admin user for initial access
- Additional users must be created by admin through API

## API Endpoints
Current endpoints (all prefixed with `/api/`):

### Authentication
- `POST /auth/login` - User authentication
- `POST /auth/logout` - User logout
- `POST /auth/refresh` - Token refresh
- `GET /auth/me` - Get current user
- `POST /auth/register` - User registration (admin only)

### User Management
- `GET /users` - List users (admin only)
- `GET /users/{id}` - Get user details
- `PUT /users/{id}` - Update user
- `DELETE /users/{id}` - Delete user (admin only)

### Product Management (Manager/Admin)
- `GET /products` - List all products with filtering and search
- `GET /products/{id}` - Get product details with stock movements
- `POST /products` - Create new product
- `PATCH /products/{id}` - Update product information
- `DELETE /products/{id}` - Delete product (admin only)

### Inventory Management (Manager/Admin)
- `POST /inventory/add` - Add stock to product
- `POST /inventory/remove` - Remove stock from product
- `GET /inventory/movements` - Get all stock movements with filtering
- `GET /inventory/products/{id}/movements` - Get stock movements for specific product
- `GET /inventory/low-stock` - Get products that need reordering

Future endpoints are planned for sales and reporting modules.

## Key Files and Locations
- **Models**: `app/Models/` - User and Role models
- **Controllers**: `app/Http/Controllers/Api/` - API controllers
- **Requests**: `app/Http/Requests/` - Form validation classes
- **Policies**: `app/Policies/` - Authorization logic
- **Routes**: `routes/api.php` - API route definitions
- **Migrations**: `database/migrations/` - Database schema
- **Tests**: `tests/Feature/` and `tests/Unit/` - Test suites
- **Config**: Environment variables in `.env` file

## Development Notes
- JWT tokens have 60-minute expiry by default (configurable)
- User status must be 'active' for successful login
- Role and status changes require admin privileges (except on self)
- All API endpoints require authentication except login
- Database uses foreign key constraints for data integrity
