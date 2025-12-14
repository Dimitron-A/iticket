# ITicket - IT Support Ticket System

A comprehensive web-based IT support ticket management system built with PHP, MySQL, HTML, CSS, and JavaScript.

## Features

### User Features
- **User Authentication** - Secure login system with role-based access (admin-controlled registration)
- **Ticket Creation** - Submit support requests with categories and priority levels
- **Ticket Tracking** - View status updates and communication history
- **Real-time Updates** - Dashboard auto-refreshes to show latest ticket status
- **Comment System** - Add comments and communicate with IT staff

### Admin/Technician Features
- **Admin Dashboard** - Overview of all tickets and system statistics
- **Ticket Management** - Update ticket status, assign priorities
- **User Management** - Create, edit, and delete user accounts (admin only)
- **Role Management** - Assign user roles (user, technician, admin)
- **Advanced Filtering** - Filter tickets by status, priority, category
- **Export Functionality** - Export ticket data for reporting

### Technical Features
- **Responsive Design** - Works on desktop, tablet, and mobile devices
- **AJAX Updates** - Real-time status updates without page refresh
- **Security** - Password hashing, SQL injection prevention, session management
- **Clean UI** - Modern, intuitive interface with priority color coding

## Installation

### Prerequisites
- Web server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser

### Setup Steps

1. **Clone/Download** the project files to your web server directory

2. **Database Setup**
   
   **Option A: Using phpMyAdmin (Recommended for XAMPP)**
   - Start XAMPP and ensure Apache + MySQL are running
   - Go to `http://localhost/phpmyadmin`
   - Create new database named `iticket`
   - Select the database and click "Import"
   - Choose `database.sql` file and click "Go"
   
   **Option B: Using MySQL Command Line**
   ```sql
   -- Import the database.sql file to create the database structure
   mysql -u root -p < database.sql
   ```

3. **Configure Database Connection**
   - Edit `config/database.php`
   - For XAMPP default setup, use:
   ```php
   $host = 'localhost';
   $dbname = 'iticket';
   $username = 'root';
   $password = '';  // Usually empty for XAMPP
   ```

4. **Set Permissions**
   - Ensure web server has read/write access to the project directory
   - Set appropriate file permissions (644 for files, 755 for directories)

5. **Access the Application**
   - Navigate to your web server URL
   - Default login credentials:
     - Admin: `admin` / `admin123`
     - Technician: `technician` / `tech123`
     - User: `user1` / `user123`

## File Structure

```
iticket/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   └── js/
│       └── main.js            # JavaScript functionality
├── config/
│   └── database.php           # Database configuration
├── includes/
│   └── functions.php          # Core PHP functions
├── ajax/
│   └── update_status.php      # AJAX status updates
├── index.php                  # Main dashboard
├── login.php                  # User authentication
├── register.php               # User registration
├── create_ticket.php          # New ticket form
├── view_ticket.php            # Ticket details and comments
├── admin.php                  # Admin panel
├── logout.php                 # Session termination
├── database.sql               # Database schema
└── README.md                  # This file
```

## Usage

### For End Users
1. **Login** with credentials provided by your IT administrator
2. **Create Tickets** by clicking "New Ticket" and filling out the form
3. **Track Progress** on the dashboard to see ticket status updates
4. **Add Comments** to provide additional information or updates

### For IT Staff (Technicians/Admins)
1. **Login** with technician or admin credentials
2. **Create User Accounts** (admin only) - Use "Create New User" button in admin panel
3. **Manage Users** (admin only) - Edit roles, delete accounts in user management section
4. **View All Tickets** in the admin panel
5. **Update Status** by changing the dropdown in ticket view or admin panel
6. **Communicate** with users through the comment system
7. **Filter and Search** tickets to manage workload efficiently

## Categories Available
- Hardware (computers, peripherals)
- Software (applications, operating systems)
- Network (connectivity, internet issues)
- Email (email client problems)
- Printer (printing issues)
- Phone System (VoIP, telephony)
- Security (access, permissions)
- Other (miscellaneous issues)

## Priority Levels
- **Low** - Can wait, non-urgent issues
- **Medium** - Normal priority, standard response time
- **High** - Urgent, requires immediate attention

## Security Features
- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Session management for user authentication
- Role-based access control
- Input validation and sanitization

## Customization

### Adding New Categories
Edit the category options in:
- `create_ticket.php` (form dropdown)
- `database.sql` (ENUM values in tickets table)

### Modifying User Roles
Update the roles in:
- `database.sql` (users table ENUM)
- `includes/functions.php` (role checking functions)

### Styling Changes
Modify `assets/css/style.css` to customize:
- Color scheme
- Layout and spacing
- Responsive breakpoints
- Component styling

## Browser Support
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## License
This project is open source and available under the MIT License.

## Support
For technical support or questions about ITicket, please create a ticket using the system or contact your IT administrator.