# VandoraX - Multi-Vendor Ecommerce Platform

A modern, scalable multi-vendor ecommerce platform built with Laravel, MySQL, and Tailwind CSS.

## Features

- ✅ Multi-vendor marketplace
- ✅ Secure vendor onboarding
- ✅ Product management with variants
- ✅ Multi-vendor shopping cart
- ✅ Secure payment processing
- ✅ Automatic order splitting by vendor
- ✅ Commission management & payouts
- ✅ Review & rating system
- ✅ Quality control (auto-block system)
- ✅ 4-day return & refund policy
- ✅ Comprehensive admin dashboard
- ✅ Vendor & customer dashboards

## Tech Stack

- **Backend:** Laravel 11
- **Database:** MySQL
- **Frontend:** Blade Templates + Tailwind CSS
- **Payment:** Stripe/JazzCash
- **Authentication:** Laravel Sanctum
- **Permissions:** Spatie Permission

## Quick Start

1. **Clone Repository**
```bash
   git clone <repository-url>
   cd VandoraX
```

2. **Install Dependencies**
```bash
   composer install
   npm install
```

3. **Setup Environment**
```bash
   cp .env.example .env
   php artisan key:generate
```

4. **Configure Database**
   - Create MySQL database: `vandorax_db`
   - Update `.env` with database credentials

5. **Run Migrations**
```bash
   php artisan migrate
```

6. **Start Development Servers**
```bash
   # Terminal 1
   php artisan serve
   
   # Terminal 2
   npm run dev
```

7. **Visit Application**
   - Open http://127.0.0.1:8000

## Documentation

- [Setup Instructions](SETUP.md)
- [Database Schema](DATABASE.md)
- [Development Guidelines](DEVELOPMENT.md)
- [API Documentation](API.md)

## Project Phases

Phase 1: ✅ Project Setup & Foundation  
Phase 2: Database Design & Architecture (coming next)  
Phase 3: Authentication & User Management  
... (17 more phases)

## Contributing

1. Create feature branch: `git checkout -b feature/feature-name`
2. Make changes and commit
3. Push to remote: `git push origin feature/feature-name`
4. Create pull request

## License

This project is the property of VandoraX.

## Support

For support, contact the development team.

---

**Built with ❤️ for modern ecommerce**