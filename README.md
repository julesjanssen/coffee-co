# Blauwdruk

**Blauwdruk** (Dutch for "Blueprint") is a comprehensive multi-tenant Laravel application framework designed to accelerate SaaS development. It provides a production-ready foundation with enterprise-grade features that would typically take months to build from scratch.

## What is Blauwdruk?

Blauwdruk is a sophisticated Laravel boilerplate that transforms a standard Laravel installation into a feature-rich, multi-tenant SaaS platform. It's designed for developers who need to quickly build scalable applications with advanced user management, tenant isolation, and administrative capabilities.

## Key Features

### 🏢 **Multi-Tenant Architecture**
- **Domain-based tenant isolation** with subdomain routing
- **Separate databases** per tenant with automatic switching
- **Tenant-aware storage** with prefixed file systems
- **Landlord/Tenant migration separation** for clean data organization

### 🔐 **Advanced Authentication & Security**
- **Passkey authentication** (WebAuthn/FIDO2) for passwordless login
- **Two-factor authentication** with QR code setup
- **Role-based permissions** using Spatie Permission
- **Session tracking** with device detection and geolocation
- **Login attempt monitoring** with detailed analytics

### 🎨 **Modern Frontend Stack**
- **Vue 3 + Inertia.js** for seamless SPA experience
- **TypeScript** throughout the frontend
- **Vite with Rolldown** for ultra-fast builds
- **Modern CSS** with custom component system
- **Server-side rendering** ready

### 🛠️ **Sophisticated Admin Interface**
- **Dynamic navigation** from YAML configuration
- **System monitoring** dashboard with health checks
- **Database management** with backup/restore
- **Advanced log viewer** with parsing and navigation
- **Changelog management** with automatic parsing
- **Styleguide** for consistent UI components

### 📁 **File Management**
- **TUS resumable uploads** for large files
- **S3 integration** with tenant-specific prefixing
- **Attachment system** with metadata tracking
- **Image processing** (WebP conversion, thumbnails)

### 🔧 **Developer Experience**
- **Strict typing** throughout (PHP 8.4+)
- **Comprehensive testing** with PestPHP
- **Code quality tools** (PHPStan, Pint, Rector)
- **Custom Artisan commands** for common tasks
- **JS/TS constant export** system

## What Makes It Different

Unlike a standard Laravel installation, Blauwdruk provides:

- **Multi-tenancy out of the box** - Complete tenant isolation
- **Enterprise-grade admin interface** - Beyond basic authentication
- **Advanced authentication** - Passkeys, 2FA, session management
- **Sophisticated logging** - Custom log parser with advanced features
- **System monitoring** - Built-in health checks and alerts
- **Production-ready tooling** - Backup, monitoring, log management

## Requirements

- PHP 8.4 or higher
- Node.js 18 or higher
- Composer
- MySQL/PostgreSQL
- SQLite (used for queues and cache by default)

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url> your-project-name
   cd your-project-name
   git remote set-url --push origin no_push
   git remote rename origin blauwdruk
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   yarn install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   cp .env.testing.example .env.testing
   ```

5. **Configure your environment**
   ```bash
   # Generate application key
   php artisan key:generate
   
   # Configure database settings in .env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=blauwdruk
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Run migrations**
   ```bash
   # Run both landlord and tenant migrations
   php artisan app:migrate
   ```

7. **Build frontend assets**
   ```bash
   # Development
   yarn admin:watch
   
   # Production
   yarn admin:build
   ```

8. **Set up your first tenant**
   ```bash
   php artisan make:tenant
   ```

## License

This project is proprietary software. Please contact the repository owner for licensing information.

## Support

For support and questions, please refer to the project documentation or contact the development team.
