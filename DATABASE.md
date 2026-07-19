# VandoraX Database Documentation

## Overview
This document describes the database structure for VandoraX multi-vendor ecommerce platform.

## Database Details
- **Database Name:** vandorax_db
- **Character Set:** utf8mb4
- **Collation:** utf8mb4_unicode_ci

## Default Tables (Created by Migrations)

### users
Stores all users (admin, vendor, customer)
- id
- name
- email
- password
- email_verified_at
- remember_token
- created_at
- updated_at

### roles
User roles (admin, vendor, customer)
- id
- name
- guard_name
- created_at
- updated_at

### permissions
Role permissions
- id
- name
- guard_name
- created_at
- updated_at

### model_has_roles
Connects users to roles
- role_id
- model_id
- model_type

## Custom Tables (Created in Phase 2)

Will include:
- vendors
- products
- categories
- orders
- payments
- commissions
- reviews
- And more...

## Migration Files

Migration files are stored in `database/migrations/` directory.
Each file creates or modifies a table.

## Seeding Data

Seed files are stored in `database/seeders/` directory.
Use these to populate test data:

```bash
php artisan db:seed
```