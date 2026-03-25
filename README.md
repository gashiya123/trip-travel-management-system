# Trip and Travel Management (7trip)

PHP + MySQL web app for managing travel packages and bookings with three roles: **Admin**, **Organizer**, and **Customer**.

## Features

- **Authentication**: register + login (`admin`, `organizer`, `customer`)
- **Admin**: manage vehicles, routes, locations, categories, countries; view bookings/packages/organizers
- **Organizer**: add/manage packages; view bookings/ratings/notifications
- **Customer**: browse packages, book packages, view bookings, rate packages, profile management

## Tech stack

- **Backend**: PHP (mysqli)
- **Database**: MySQL 
- **Frontend**: HTML/CSS + a little JS

## Project structure

- **Public pages**: `index.php`, `login.php`, `register.php`, `about.php`, `faq.php`, `contact.php`
- **Role areas**:
  - `admin/`
  - `organizer/`
  - `customer/`
- **Shared code**: `includes/config.php`, `includes/functions.php`
- **Uploads**: `uploads/` (user/package images and background images)
- **Static assets**: `styles/`, `scripts/`, `img/`, `images/`

## Requirements

- PHP 7.4+ (8.x recommended) with `mysqli` enabled
- MySQL / MariaDB
- Local server such as **XAMPP/WAMP/Laragon** (Windows) or Apache/Nginx

## Setup (local)

1. **Put the project in your web root**
   - Example (XAMPP): `C:\xampp\htdocs\7trip-travel-management\`

2. **Create the database**
   - DB name expected by default: `trip_and_travel_management`
   - Configure connection in `includes/config.php`:
     - `$servername` (default `localhost`)
     - `$dbusername` (default `root`)
     - `$dbpassword` (default empty)
     - `$dbname` (default `trip_and_travel_management`)

3. **Import tables (schema + seed data)**
   - Import `database/trip_and_travel_management.sql` into MySQL/MariaDB (phpMyAdmin or CLI).

   **phpMyAdmin**
   - Create database (or let the script create it), then Import the file:
     - `database/trip_and_travel_management.sql`

   **CLI (MySQL)**

```bash
mysql -u root -p < database/trip_and_travel_management.sql
```

   This creates all required tables used by the app, and inserts a small set of seed records.

4. **Run the app**
   - Open the home page: `http://localhost/<your-folder>/index.php`
   - Login page: `http://localhost/<your-folder>/login.php`

## Default pages / entry points

- **Home**: `index.php`
- **Login**: `login.php`
- **Register**: `register.php`
- **Dashboards**:
  - Admin: `admin/admin_dashboard.php`
  - Organizer: `organizer/organizer_dashboard.php`
  - Customer: `customer/customer_dashboard.php`

## Notes / known behaviors

- **Passwords are currently compared in plain text** in `login.php` and stored as plain text in `register.php`. If you want, I can update the project to use `password_hash()` / `password_verify()` and a migration approach.
- `register.php` currently **echoes debugging values** (username/email/role, etc.) on submit. Remove those echoes for production.
- If file uploads fail, ensure the `uploads/` folder exists and your PHP process has permission to write to it.

## Seed login accounts (from the SQL file)

- **Admin**: username `admin`, password `admin123`
- **Organizer**: username `organizer`, password `organizer123`
- **Customer**: username `customer`, password `customer123`

## Troubleshooting

- **DB connection error**: verify credentials in `includes/config.php` and that MySQL is running.
- **Blank/500 error**: check Apache/PHP error logs and ensure PHP extensions (`mysqli`) are enabled.
- **Images not showing**: confirm files exist under `uploads/` and paths are correct.

## License

Add a license file if you plan to distribute this project.

