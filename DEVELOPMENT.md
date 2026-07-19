# VandoraX Development Guidelines

## Code Style

### PHP/Laravel
- Follow PSR-12 standard
- Use camelCase for variables and methods
- Use PascalCase for class names
- Use SCREAMING_SNAKE_CASE for constants

Example:
```php
class VendorController
{
    public function storeProduct($productId)
    {
        const MAX_IMAGES = 10;
        $vendorId = auth()->user()->vendor->id;
    }
}
```

### Database Naming
- Table names: snake_case, plural (users, products)
- Column names: snake_case (first_name, last_name)
- Primary key: always `id`
- Foreign keys: {table_singular}_id (vendor_id, product_id)

### Routes Naming
- Use RESTful conventions
- Use snake_case for route names
- Use clear, descriptive names

Example:
```php
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
```

## Git Workflow

### Branch Naming
- Feature: `feature/feature-name`
- Bugfix: `bugfix/bug-name`
- Hotfix: `hotfix/fix-name`

### Commit Messages
- Use imperative mood: "Add feature" not "Added feature"
- Be descriptive: "Add vendor category request system" not "Fix stuff"
- Reference issues: "Closes #123 - Add vendor category request"

Example:

### Pull Request Process
1. Create feature branch
2. Make changes
3. Commit with meaningful messages
4. Push to remote
5. Create pull request
6. Code review
7. Merge to develop
8. Delete branch

## File Organization

### Controllers
- One controller per resource
- Group related methods together
- Use type hints for parameters

### Models
- Define relationships
- Use mutators/accessors if needed
- Use scopes for common queries

### Views
- Use blade components
- Keep logic minimal (use controllers)
- Use consistent spacing

### Services
- Separate business logic from controllers
- Reusable functionality
- Example: PaymentService, CommissionService

## Testing

- Write tests for critical functionality
- Use meaningful test names
- Keep tests focused and small

Example:
```bash
php artisan make:test VendorRegistrationTest
```

## Performance

- Use eager loading (with())
- Add database indexes
- Cache frequently accessed data
- Optimize queries

## Security

- Always validate user input
- Use CSRF protection (@csrf in forms)
- Sanitize output
- Use prepared statements (Eloquent does this)
- Never commit sensitive data

## Documentation

- Comment complex logic
- Update README for major changes
- Document API endpoints
- Keep database schema updated