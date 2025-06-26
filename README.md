# 🍽️ Restaurant Management System

A comprehensive web-based restaurant management system built with PHP, MySQL, and Bootstrap. This system allows restaurants to manage their menus, orders, customers, and staff efficiently.

## ✨ Features

- **Dashboard**: Overview of restaurant statistics and recent activities
- **Restaurant Management**: Add, edit, and delete restaurant information
- **Menu Management**: Organize menu items by categories with pricing and availability
- **Order Management**: Track orders from placement to delivery
- **User Management**: Role-based access control (Admin, Manager, Staff)
- **Authentication**: Secure login/logout system with session management
- **Responsive Design**: Mobile-friendly interface using Bootstrap 5
- **Dashboard**: Analytics and reporting with charts
- **Responsive Design**: Mobile-first approach with Bootstrap 5
- **Search & Filter**: Advanced search across all modules
- **Image Management**: Support for restaurant and menu item images
- **Status Tracking**: Real-time order status updates

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Modern web browser

## Installation

### 1. Download/Clone the Project

```bash
git clone <repository-url>
cd restaurant-app
```

### 2. Database Setup

1. Create a new MySQL database:
```sql
CREATE DATABASE restaurant_app;
```

2. Import the database schema:
```bash
mysql -u username -p restaurant_app < sql/database_setup.sql
```

### 3. Configuration

1. Update database credentials in `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'restaurant_app';
private $username = 'your_username';
private $password = 'your_password';
```

2. Update site configuration in `config/config.php` if needed.

### 4. Web Server Setup

#### Apache
1. Place the project in your web root directory
2. Ensure mod_rewrite is enabled
3. Access via `http://localhost/restaurant-app`

#### Nginx
Add this location block to your server configuration:
```nginx
location /restaurant-app {
    try_files $uri $uri/ /restaurant-app/index.php?$query_string;
}
```

### 5. Default Login Credentials

- **Admin**: admin@restaurant.com / password
- **Manager**: manager@restaurant.com / password
- **Staff**: staff@restaurant.com / password

## File Structure

```
restaurant-app/
├── config/
│   ├── database.php      # Database connection
│   └── config.php        # Site configuration
├── classes/
│   ├── Restaurant.php    # Restaurant CRUD operations
│   ├── Menu.php         # Menu CRUD operations
│   ├── Order.php        # Order management
│   └── User.php         # User authentication
├── includes/
│   ├── header.php       # Common header
│   ├── footer.php       # Common footer
│   └── update_availability.php # AJAX handler
├── pages/
│   ├── restaurants/     # Restaurant management pages
│   ├── menu/           # Menu management pages
│   ├── orders/         # Order management pages
│   ├── login.php       # Login page
│   ├── register.php    # Registration page
│   ├── profile.php     # User profile
│   └── reports.php     # Analytics & reports
├── assets/
│   ├── css/
│   │   └── style.css   # Custom styles
│   ├── js/
│   │   └── main.js     # Custom JavaScript
│   └── images/
│       └── uploads/    # Uploaded images
├── sql/
│   └── database_setup.sql # Database schema
├── index.php           # Dashboard
└── README.md           # This file
```

## Usage

### 1. Dashboard
- View key metrics and statistics
- Quick access to recent orders
- Top-performing restaurants
- Quick action buttons

### 2. Restaurant Management
- Add new restaurants with details
- Edit existing restaurant information
- Delete restaurants (with confirmation)
- Search and filter restaurants

### 3. Menu Management
- Add menu items to restaurants
- Organize items by categories
- Set prices and availability
- Upload item images
- Toggle availability status

### 4. Order Management
- Create new customer orders
- Select restaurant and menu items
- Track order status progression
- View detailed order information
- Update order status

### 5. User Management
- Secure login/logout
- Role-based access control
- Profile management
- Password updates

### 6. Reports & Analytics
- Sales performance by restaurant
- Popular menu items
- Order status summaries
- Daily sales trends with charts
- Date range filtering

## Security Features

- CSRF token protection
- SQL injection prevention (prepared statements)
- XSS protection (input sanitization)
- Password hashing
- Session management
- Input validation

## Customization

### Adding New Features
1. Create new classes in the `classes/` directory
2. Add corresponding pages in the `pages/` directory
3. Update navigation in `includes/header.php`

### Styling
- Modify `assets/css/style.css` for custom styles
- Update Bootstrap variables for theme changes
- Add custom JavaScript in `assets/js/main.js`

### Database Changes
- Create migration scripts for schema updates
- Update corresponding PHP classes
- Test thoroughly before deploying

## Troubleshooting

### Common Issues

**Database Connection Error**
- Check database credentials in `config/database.php`
- Ensure MySQL service is running
- Verify database exists

**Permission Denied**
- Check file permissions (755 for directories, 644 for files)
- Ensure web server has read access
- Check upload directory permissions

**Session Issues**
- Verify session.save_path is writable
- Check PHP session configuration
- Clear browser cookies/cache

**JavaScript Errors**
- Check browser console for errors
- Ensure jQuery and Bootstrap JS are loaded
- Verify AJAX endpoints are accessible

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the MIT License.

## Support

For support and questions:
- Create an issue in the repository
- Check the documentation
- Review existing issues for solutions

## Future Enhancements

- Online ordering system
- Payment gateway integration
- Inventory management
- Customer reviews and ratings
- Multi-language support
- Mobile app API
- Email notifications
- SMS integration
- Advanced reporting
- Data export/import

---

Built with ❤️ using PHP, MySQL, Bootstrap, and modern web technologies.
