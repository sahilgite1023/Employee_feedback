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
├── Dockerfile                 # PHP 8.2 + Apache image definition
├── docker-compose.yml         # Multi-container orchestration (web + db + phpmyadmin)
├── .env.example               # Template for environment variables
├── .dockerignore              # Files excluded from the Docker build context
│
├── config/
│   └── db.php                 # PDO connection – reads credentials from env vars
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
    └── schema.sql             # DB schema + default admin seed (auto-imported by Docker)
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

## 🐳 Docker Setup (Recommended)

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (or Docker Engine + Compose plugin)

### 1. Create your `.env` file

```bash
cp .env.example .env
# Edit .env with your preferred passwords (do NOT commit this file)
```

### 2. Build and start all containers

```bash
docker-compose up --build
```

Docker will:
- Build the PHP + Apache image
- Pull MySQL 8.0 and phpMyAdmin images
- Auto-import `database/schema.sql` (creates tables + seeds default admin)
- Start all three services

### 3. Access the application

| Service | URL |
|---|---|
| Web app | <http://localhost:8080> |
| phpMyAdmin | <http://localhost:8081> |
| MySQL | `localhost:3306` (use DB_USER / DB_PASS from `.env`) |

### 4. Default login

| Role | Email | Password |
|---|---|---|
| Admin | admin@company.com | Admin@123 |
| Employee | *(register a new account)* | *(your choice)* |

### 5. Stop containers

```bash
docker-compose down          # stop and remove containers (data volume kept)
docker-compose down -v       # also delete the MySQL data volume (full reset)
```

### 6. View logs / debug

```bash
docker-compose logs -f web   # PHP / Apache logs (live)
docker-compose logs -f db    # MySQL logs
docker-compose ps            # show running containers and port bindings
docker exec -it employee_feedback_web bash   # shell into the web container
```

### Hot reload

The `docker-compose.yml` bind-mounts the project directory into the container, so any change you save locally is reflected immediately without rebuilding the image.

---

## 🚀 Manual Setup (without Docker)

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

Set environment variables before starting Apache, or create a `.env` file and load it. For a quick local test you can also export them in your shell:

```bash
export DB_HOST=localhost
export DB_NAME=employee_feedback_db
export DB_USER=root
export DB_PASS=your_password
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
