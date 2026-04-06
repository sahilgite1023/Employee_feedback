# FeedbackPro – Employee Feedback System

A **modern, full-stack employee feedback platform** built with PHP, MySQL, JavaScript (Fetch API), and Chart.js.

---

## ✨ Features

| Feature | Details |
|---|---|
| 🔐 Authentication | Login & registration, bcrypt passwords, PHP sessions, role-based access |
| 👥 Dual Roles | Admin dashboard + Employee dashboard |
| ⚡ AJAX Feedback | Submit feedback without page reload (Fetch API) |
| 📊 Chart.js Analytics | Bar, Doughnut & Line charts with real DB data |
| 🎨 Premium UI | Dark sidebar, gradient stat cards, responsive Flexbox/Grid layout |
| 🔒 Security | Parameterised queries (PDO), session regeneration, `.htaccess` hardening |

---

## 📁 Folder Structure

```
Employee_feedback/
├── index.php                  # Root redirect
├── .htaccess                  # Apache config + security
│
├── config/
│   └── db.php                 # PDO database connection
│
├── auth/
│   ├── login.php              # Login page
│   ├── register.php           # Employee registration
│   └── logout.php             # Session destroy + redirect
│
├── admin/
│   ├── dashboard.php          # Stats cards + Chart.js charts
│   ├── feedbacks.php          # Filterable feedback list (AJAX)
│   └── reports.php            # Full-page analytics charts
│
├── employee/
│   ├── dashboard.php          # Employee overview + recent feedback
│   ├── submit_feedback.php    # AJAX feedback form with star rating
│   └── my_feedbacks.php       # Employee's own feedback history
│
├── api/
│   ├── submit_feedback.php    # POST – insert feedback row
│   ├── get_feedbacks.php      # GET  – filtered feedback list (admin)
│   └── get_chart_data.php     # GET  – chart data (admin)
│
├── includes/
│   ├── auth_check.php         # Session helper + role enforcement
│   ├── sidebar_admin.php      # Admin navigation partial
│   └── sidebar_employee.php   # Employee navigation partial
│
├── assets/
│   ├── css/style.css          # Global stylesheet
│   ├── js/main.js             # Sidebar toggle, password reveal
│   └── js/charts.js           # Chart.js initialisation
│
└── database/
    └── schema.sql             # DB schema + default admin seed
```

---

## 🛢️ Database Schema

### `users`
| Column | Type | Notes |
|---|---|---|
| id | INT PK | Auto-increment |
| name | VARCHAR(100) | Full name |
| email | VARCHAR(150) | Unique |
| password | VARCHAR(255) | bcrypt hash |
| role | ENUM | `admin` or `employee` |
| department | VARCHAR(100) | Optional |
| created_at | DATETIME | Auto |

### `feedback`
| Column | Type | Notes |
|---|---|---|
| id | INT PK | Auto-increment |
| user_id | INT FK | References `users.id` |
| category | ENUM | work_environment, management, career_growth, team_collaboration, work_life_balance, other |
| rating | TINYINT | 1 – 5 |
| message | TEXT | Feedback text |
| is_anonymous | TINYINT(1) | 0 = named, 1 = anonymous |
| created_at | DATETIME | Auto |

---

## 🚀 Setup Instructions

### Prerequisites
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10+
- Apache with `mod_rewrite` enabled (or Nginx equivalent)

### 1. Clone / place the project

```bash
# In your web root (e.g. /var/www/html or XAMPP htdocs)
git clone https://github.com/sahilgite1023/Employee_feedback.git
```

### 2. Import the database

```bash
mysql -u root -p < database/schema.sql
```

Or paste the contents of `database/schema.sql` into phpMyAdmin.

### 3. Configure the database connection

Edit `config/db.php` and update:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'employee_feedback_db');
define('DB_USER', 'root');      // your MySQL username
define('DB_PASS', '');          // your MySQL password
```

### 4. Set web root

Point your virtual host document root to the project folder.  
If using XAMPP, place the folder in `htdocs/` and access via `http://localhost/Employee_feedback/`.

### 5. Login

| Role | Email | Password |
|---|---|---|
| Admin | admin@company.com | Admin@123 |
| Employee | *(register a new account)* | *(your choice)* |

---

## 🖥️ UI Highlights

- **Dark sidebar** with user avatar and role badge
- **Gradient stat cards** for total feedback, avg rating, employee count
- **Chart.js charts**: Rating distribution (Bar), Category breakdown (Doughnut), Monthly trend (Line)
- **AJAX feedback form** with interactive star rating and anonymous toggle
- **Responsive** – works on mobile, tablet, and desktop

---

## 🔒 Security Notes

- Passwords hashed with `bcrypt` (cost 12)
- All DB queries use **PDO prepared statements** (SQL injection proof)
- Session ID regenerated on login (session fixation protection)
- `.htaccess` blocks directory listing, SQL files, and config directory
- Input validated and sanitised server-side before every DB write
