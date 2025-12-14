# Contributing to ITicket

Thank you for your interest in contributing to ITicket! This document provides guidelines for contributing to the project.

## Getting Started

### Prerequisites
- Web server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7 or MariaDB
- Git

### Setup for Development

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd iticket
   ```

2. **Database Setup:**
   ```bash
   # Copy the database configuration template
   cp config/database.example.php config/database.php
   
   # Edit config/database.php with your database credentials
   # Import the database schema
   mysql -u root -p < database.sql
   ```

3. **Initial Admin Setup:**
   ```bash
   # Create the admin setup file (temporary)
   # Visit http://localhost/iticket/create_admin.php
   # Delete the file after creating admin user
   ```

## Development Guidelines

### Code Style
- Use PSR-4 autoloading standards
- Follow PSR-12 coding style
- Use meaningful variable and function names
- Add comments for complex logic
- Keep functions small and focused

### Security Best Practices
- Always use prepared statements for database queries
- Validate and sanitize all user inputs
- Use `htmlspecialchars()` for output escaping
- Implement proper session management
- Never commit sensitive configuration files

### Database Guidelines
- Use foreign key constraints
- Add appropriate indexes for performance
- Use ENUM types for fixed value sets
- Include created_at and updated_at timestamps

### Frontend Guidelines
- Write semantic HTML
- Use CSS Grid/Flexbox for layouts
- Ensure responsive design (mobile-first)
- Test across different browsers
- Optimize for accessibility

## Contributing Process

### 1. Fork and Branch
```bash
# Fork the repository on GitHub
# Clone your fork
git clone <your-fork-url>
cd iticket

# Create a feature branch
git checkout -b feature/your-feature-name
```

### 2. Make Changes
- Write clean, documented code
- Test your changes thoroughly
- Ensure no security vulnerabilities
- Update documentation if needed

### 3. Test Your Changes
- Test all functionality manually
- Verify database operations work correctly
- Check responsive design on different screen sizes
- Test with different user roles (admin, technician, user)

### 4. Commit and Push
```bash
# Stage your changes
git add .

# Commit with descriptive message
git commit -m "Add: Brief description of your changes"

# Push to your fork
git push origin feature/your-feature-name
```

### 5. Create Pull Request
- Open a pull request from your fork to the main repository
- Provide clear description of changes
- Include screenshots for UI changes
- Reference any related issues

## Types of Contributions

### Bug Fixes
- Report bugs with detailed reproduction steps
- Include browser/environment information
- Provide screenshots if applicable

### Feature Requests
- Describe the feature and its benefits
- Explain the use case
- Consider backward compatibility

### Documentation
- Improve README or setup instructions
- Add code comments
- Create user guides or tutorials

### Security Improvements
- Report security issues privately first
- Suggest security enhancements
- Improve input validation

## Code Review Process

All contributions go through code review:
1. Automated checks (if configured)
2. Manual code review by maintainers
3. Testing in development environment
4. Approval and merge

## Questions or Help?

- Create an issue for bugs or feature requests
- Use discussions for general questions
- Check existing issues before creating new ones

## License

By contributing to ITicket, you agree that your contributions will be licensed under the same license as the project.

Thank you for contributing to ITicket! 🎫