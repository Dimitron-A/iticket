# ITicket Deployment Guide

This guide covers deploying ITicket to various hosting platforms and setting up the project from Git.

## 🚀 Quick Setup from Git

### 1. Clone the Repository
```bash
git clone <your-repository-url>
cd iticket
```

### 2. Database Configuration
```bash
# Copy the database configuration template
cp config/database.example.php config/database.php

# Edit the database credentials
nano config/database.php  # or use your preferred editor
```

### 3. Database Setup
```bash
# Create database and import schema
mysql -u root -p -e "CREATE DATABASE iticket;"
mysql -u root -p iticket < database.sql
```

### 4. Initial Admin Setup
```bash
# Create temporary admin setup file
cp setup/create_admin_template.php create_admin.php

# Visit: http://your-domain/create_admin.php
# Delete the file after creating admin user
rm create_admin.php
```

## 🌐 Hosting Platform Deployment

### Shared Hosting (cPanel, etc.)

1. **Upload Files:**
   - Upload all files except `create_admin.php` and `reset_password.php`
   - Place in `public_html` or equivalent directory

2. **Database Setup:**
   - Create MySQL database through hosting panel
   - Import `database.sql` via phpMyAdmin
   - Update `config/database.php` with hosting credentials

3. **File Permissions:**
   ```bash
   chmod 644 *.php
   chmod 755 assets/ ajax/ config/ includes/
   ```

### VPS/Dedicated Server

1. **Install Requirements:**
   ```bash
   # Ubuntu/Debian
   sudo apt update
   sudo apt install apache2 php php-mysql mysql-server git
   
   # CentOS/RHEL
   sudo yum install httpd php php-mysql mariadb-server git
   ```

2. **Clone and Setup:**
   ```bash
   cd /var/www/html
   sudo git clone <repository-url> iticket
   cd iticket
   sudo cp config/database.example.php config/database.php
   sudo nano config/database.php
   ```

3. **Database Setup:**
   ```bash
   sudo mysql -e "CREATE DATABASE iticket;"
   sudo mysql iticket < database.sql
   ```

4. **Set Permissions:**
   ```bash
   sudo chown -R www-data:www-data /var/www/html/iticket
   sudo chmod -R 755 /var/www/html/iticket
   ```

### Docker Deployment

Create `docker-compose.yml`:
```yaml
version: '3.8'
services:
  web:
    image: php:8.1-apache
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_NAME=iticket
      - DB_USER=root
      - DB_PASS=rootpassword

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: iticket
    volumes:
      - ./database.sql:/docker-entrypoint-initdb.d/database.sql
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

Run with:
```bash
docker-compose up -d
```

## 🔧 Environment Configuration

### Production Settings

1. **Security Hardening:**
   ```php
   // In config/database.php - use environment variables
   $host = $_ENV['DB_HOST'] ?? 'localhost';
   $dbname = $_ENV['DB_NAME'] ?? 'iticket';
   $username = $_ENV['DB_USER'] ?? 'root';
   $password = $_ENV['DB_PASS'] ?? '';
   ```

2. **Error Handling:**
   ```php
   // Add to config/database.php for production
   ini_set('display_errors', 0);
   ini_set('log_errors', 1);
   ini_set('error_log', '/path/to/error.log');
   ```

3. **HTTPS Configuration:**
   - Ensure SSL certificate is installed
   - Force HTTPS redirects in `.htaccess`

### Environment Variables (.env)

Create `.env` file (not tracked in Git):
```env
DB_HOST=localhost
DB_NAME=iticket
DB_USER=your_username
DB_PASS=your_password
APP_ENV=production
APP_DEBUG=false
```

## 📊 Database Migration

### Backup Current Data
```bash
mysqldump -u username -p iticket > backup_$(date +%Y%m%d).sql
```

### Update Database Schema
```bash
# Apply any new migrations
mysql -u username -p iticket < migrations/update_v2.sql
```

## 🔒 Security Checklist

- [ ] Remove `create_admin.php` and `reset_password.php` from production
- [ ] Use environment variables for database credentials
- [ ] Enable HTTPS/SSL
- [ ] Set proper file permissions (644 for files, 755 for directories)
- [ ] Configure firewall rules
- [ ] Regular database backups
- [ ] Keep PHP and MySQL updated
- [ ] Monitor error logs

## 🚨 Troubleshooting

### Common Issues

1. **Database Connection Failed:**
   - Check credentials in `config/database.php`
   - Verify database server is running
   - Check firewall settings

2. **Permission Denied:**
   ```bash
   sudo chown -R www-data:www-data /path/to/iticket
   sudo chmod -R 755 /path/to/iticket
   ```

3. **PHP Errors:**
   - Check PHP version (requires 7.4+)
   - Verify required extensions: `php -m | grep -E "(pdo|mysql)"`

4. **Can't Login as Admin:**
   - Use password reset utility
   - Check user exists in database
   - Verify password hashing

### Log Files
- Apache: `/var/log/apache2/error.log`
- PHP: `/var/log/php_errors.log`
- MySQL: `/var/log/mysql/error.log`

## 📈 Performance Optimization

### Database Optimization
```sql
-- Add indexes for better performance
CREATE INDEX idx_tickets_status_priority ON tickets(status, priority);
CREATE INDEX idx_tickets_created_at ON tickets(created_at);
CREATE INDEX idx_users_role ON users(role);
```

### Caching
- Enable PHP OPcache
- Use database query caching
- Implement Redis for session storage (advanced)

### Web Server Optimization
```apache
# .htaccess for Apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
</IfModule>

<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/javascript
</IfModule>
```

## 📞 Support

For deployment issues:
1. Check this documentation
2. Review error logs
3. Create an issue on GitHub
4. Contact system administrator

---

**Note:** Always test deployments in a staging environment before production!