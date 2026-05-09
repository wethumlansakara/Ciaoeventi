<div align="center">

# 🎉 Ciaoeventi

### *Discover Amazing Events. Create Unforgettable Moments.*

A modern event discovery and management platform where users create, explore, and engage with events happening around them.

[![Live Site](https://img.shields.io/badge/Live-ciaoeventi.com-FF4081?style=for-the-badge&logo=googlechrome&logoColor=white)](https://ciaoeventi.com/index.php)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-Educational-green?style=for-the-badge)](#-license)

[Live Demo](https://ciaoeventi.com/index.php) • [Report Bug](https://github.com/wethumlansakara/Ciaoeventi/issues) • [Request Feature](https://github.com/wethumlansakara/Ciaoeventi/issues)

</div>

---

## 📖 About The Project

**Ciaoeventi** is a full-stack web application built to bring event organizers and attendees together. Whether it's a music festival in Galle, a DJ night in Kandy, or a private party — Ciaoeventi makes it easy to host, share, and discover events with a community-driven like system and a clean, responsive interface.

> 🌍 **Live now at [ciaoeventi.com](https://ciaoeventi.com/index.php)**

---

## ✨ Key Features

| | |
|---|---|
| 🔐 **Secure Authentication** | Registration, login, and session-based security with bcrypt password hashing |
| 📅 **Full Event CRUD** | Create, view, edit, and soft-delete events with status tracking |
| 🔍 **Event Discovery** | Browse events by category — Festival, Party, Concert, Nightlife, Social, Music |
| ❤️ **AJAX Like System** | Real-time like/unlike with no page reload, backed by a unique-index constraint |
| 👤 **User Profiles** | Personalized dashboards for managing your own events |
| 🖼️ **Image Uploads** | Banner image support for every event |
| 🎟️ **Ticket Integration** | Direct external ticket-link support per event |
| 📊 **Live Statistics** | Real-time counts for events, users, locations, and dynamic ratings |
| 📱 **Responsive Design** | Mobile-friendly layout with custom CSS |
| 🛡️ **Secure by Default** | Prepared statements, XSS escaping, CASCADE foreign keys |

---

## 🛠️ Tech Stack

**Backend** — PHP 7.4+ · MySQL / MariaDB (mysqli with prepared statements) · Session-based auth
**Frontend** — HTML5 · CSS3 · Vanilla JavaScript · AJAX (Fetch API)
**Database** — InnoDB engine · utf8mb4 charset · Foreign-key constraints with cascade deletes
**Server** — Apache (with `.htaccess`) / Nginx compatible

---

## 📂 Project Structure

```
Ciaoeventi/
├── 📄 README.md                  # You are here
├── 🗄️  ciao_eventi.sql            # Full database schema + seed data
├── 🔒 .htaccess.txt              # Apache rewrite rules
└── 📁 test2/                     # Application root
    ├── 🏠 index.php              # Home page — event discovery + live stats
    ├── 🔑 login.php              # User login
    ├── 📝 register.php           # User registration
    ├── 🚪 logout.php             # Session destruction
    ├── 👤 profile.php            # User profile + own events
    ├── 📅 events.php             # All events listing
    ├── ➕ create_event.php       # Create new event (with banner upload)
    ├── ✏️  edit_event.php         # Edit existing event
    ├── 🗑️  delete_event.php       # Soft-delete event
    ├── ❌ 404.php                # Custom error page
    ├── 📁 api/
    │   └── like.php              # AJAX endpoint — POST to toggle likes
    ├── 📁 config/
    │   └── database.php          # DB connection (configure here)
    ├── 📁 includes/
    │   ├── header.php            # Shared header + nav
    │   └── footer.php            # Shared footer + scripts
    ├── 📁 css/
    │   └── style.css             # Stylesheet (~32 KB)
    ├── 📁 js/
    │   └── script.js             # Client-side logic (~20 KB)
    └── 📁 uploads/               # User-uploaded event banners
```

---

## 🗃️ Database Schema

Three relational tables with proper indexing and foreign-key constraints:

### 👥 `users`
| Column | Type | Notes |
|---|---|---|
| `id` | INT, PK, AUTO_INCREMENT | |
| `username` | VARCHAR(50), UNIQUE | |
| `email` | VARCHAR(100), UNIQUE | |
| `password` | VARCHAR(255) | bcrypt-hashed |
| `full_name`, `profile_pic`, `bio` | nullable | Optional profile fields |
| `created_at`, `updated_at` | TIMESTAMP | Auto-managed |

### 🎫 `events`
| Column | Type | Notes |
|---|---|---|
| `id` | INT, PK, AUTO_INCREMENT | |
| `title`, `description` | VARCHAR / TEXT | |
| `category` | ENUM | Festival, Party, Concert, Nightlife, Social, Music |
| `event_date`, `event_time` | DATE / TIME | |
| `location`, `venue` | VARCHAR | |
| `banner_image` | VARCHAR(255) | Filename in `/uploads/` |
| `ticket_link` | VARCHAR(255) | External ticket URL |
| `user_id` | INT, FK → `users.id` | CASCADE on delete |
| `likes_count` | INT | Denormalized counter |
| `status` | ENUM | active, edited, deleted |

### ❤️ `likes`
| Column | Type | Notes |
|---|---|---|
| `id` | INT, PK, AUTO_INCREMENT | |
| `user_id` | INT, FK → `users.id` | CASCADE on delete |
| `event_id` | INT, FK → `events.id` | CASCADE on delete |
| `(user_id, event_id)` | UNIQUE INDEX | Prevents double-likes |

---

## 🚀 Getting Started

### Prerequisites

- PHP **7.4** or higher
- MySQL **5.7+** or MariaDB **10.4+**
- Apache or Nginx web server
- A modern browser

### Installation

**1. Clone the repository**
```bash
git clone https://github.com/wethumlansakara/Ciaoeventi.git
cd Ciaoeventi
```

**2. Import the database**
```bash
mysql -u root -p -e "CREATE DATABASE ciao_eventi CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -u root -p ciao_eventi < ciao_eventi.sql
```

**3. Configure the database connection**

Open `test2/config/database.php` and update the credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'ciao_eventi');
```

**4. Set permissions for uploads**
```bash
chmod -R 755 test2/uploads/
```

**5. Launch**

Drop the project into your web server's document root (e.g. `htdocs/` for XAMPP) and visit:
```
http://localhost/Ciaoeventi/test2/
```

---

## 💡 Usage

### For Users

1. 📝 **Register** for a free account
2. 🔑 **Log in** to access full features
3. 🔍 **Browse** the home page to discover events
4. ➕ **Create** your own event with a banner image and ticket link
5. ❤️ **Like** events to engage with the community
6. 👤 **Manage** your events from your profile

### For Developers

- All PHP files share a consistent `header.php` / `footer.php` include pattern
- Database queries use **prepared statements** (`mysqli_stmt_*`) end-to-end
- The like API at `api/like.php` accepts POST requests and returns JSON
- Output is escaped with `htmlspecialchars()` to prevent XSS
- Upload validation should be reviewed before production deployment

---

## 🔒 Security Features

- ✅ **Bcrypt password hashing** (`password_hash` / `password_verify`)
- ✅ **Prepared statements** prevent SQL injection
- ✅ **XSS protection** via `htmlspecialchars()` on all output
- ✅ **Session-based authentication** with regeneration on login
- ✅ **Foreign-key constraints** with `ON DELETE CASCADE`
- ✅ **Unique constraints** on usernames, emails, and likes

---

## 🗺️ Roadmap

- [ ] User profile pictures & bio editing UI
- [ ] Event search & advanced filters (date range, location)
- [ ] Comments / reviews on events
- [ ] Email notifications for event updates
- [ ] Event sharing on social media
- [ ] Admin moderation dashboard
- [ ] PWA / mobile app version

---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

1. Fork the project
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

This project is open source and available for **educational purposes**.

---

## 📬 Contact

**Wethum Lansakara** — [@wethumlansakara](https://github.com/wethumlansakara)

**Project Link:** [github.com/wethumlansakara/Ciaoeventi](https://github.com/wethumlansakara/Ciaoeventi)
**Live Site:** [ciaoeventi.com](https://ciaoeventi.com/index.php)

---

<div align="center">

### ⭐ If you found this project useful, please consider giving it a star!

**Made with ❤️ for event enthusiasts**

</div>
