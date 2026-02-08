# Ciaoeventi 🎉

An event discovery and management platform where users can create, explore, and engage with events.

## Features

- **User Authentication**: Complete registration and login system with session management
- **Event Management**: Create, edit, view, and delete events
- **Event Discovery**: Browse and discover amazing events on the home page
- **User Profiles**: Personalized user profiles for managing your events
- **Like System**: Users can like/unlike events via AJAX API
- **Event Statistics**: Live stats tracking for events, users, and locations
- **Image Uploads**: Support for event images and media
- **Responsive Design**: Modern UI with custom CSS styling
- **Rating System**: Dynamic event ratings based on user engagement

## Technologies Used

- **Backend**: PHP with MySQL
- **Database**: MySQL (mysqli)
- **Frontend**: HTML5, CSS3, JavaScript
- **AJAX**: Dynamic like/unlike functionality
- **Session Management**: Secure user sessions

## Project Structure

```
test2/
├── index.php              # Home page with event discovery
├── login.php              # User login
├── register.php           # User registration
├── logout.php             # Session logout
├── profile.php            # User profile management
├── events.php             # Events listing page
├── create_event.php       # Create new event
├── edit_event.php         # Edit existing event
├── delete_event.php       # Delete event
├── 404.php                # Error page
├── api/
│   └── like.php          # AJAX endpoint for likes
├── config/
│   └── database.php      # Database configuration
├── includes/
│   ├── header.php        # Common header
│   └── footer.php        # Common footer
├── css/
│   └── style.css         # Main stylesheet
├── js/
│   └── script.js         # JavaScript functions
└── uploads/              # Event images directory
```

## Database Schema

The main database file `ciao_eventi.sql` contains the following tables:

- **users**: User accounts and authentication
- **events**: Event information (title, description, date, location, images, etc.)

## Setup Instructions

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

### Installation

1. **Clone or download this repository**
   ```bash
   cd test2
   ```

2. **Import the database**
   - Create a new MySQL database
   - Import `ciao_eventi.sql` into your database

3. **Configure database connection**
   - Open `config/database.php`
   - Update the database credentials:
     ```php
     define('DB_HOST', 'your_host');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'your_database_name');
     ```

4. **Set proper permissions**
   - Ensure `uploads/` directory is writable:
     ```bash
     chmod 755 uploads/
     ```

5. **Start your web server**
   - Access the application at `http://localhost/test2/`

## Usage

### For Users

1. **Register**: Create a new account via the registration page
2. **Login**: Access your account with your credentials
3. **Browse Events**: Explore events on the home page
4. **Create Event**: Share your own events with the community
5. **Engage**: Like events you're interested in
6. **Manage**: Edit or delete your own events from your profile

### For Developers

- All PHP files follow a consistent structure with includes
- Database connections use prepared statements for security
- AJAX endpoints are in the `api/` directory
- Custom styling is in `css/style.css`
- Client-side logic is in `js/script.js`

## Features Details

### Event Statistics
The home page displays real-time statistics:
- Total number of events
- Total registered users
- Number of unique locations
- Dynamic ratings based on likes

### Like System
- Real-time AJAX-based like/unlike functionality
- No page reload required
- Instant feedback to users

### Security Features
- Session-based authentication
- SQL injection protection with prepared statements
- XSS protection with proper output escaping
- Secure password handling

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open source and available for educational purposes.

## Contact

For questions or support, please open an issue in the repository.

---

**Made with ❤️ for event enthusiasts**
