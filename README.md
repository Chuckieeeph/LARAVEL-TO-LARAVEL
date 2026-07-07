# Laravel A - Enrollment Management System

Laravel A is the producer application. It handles authentication, role-based access, student management, course management, subject management, and enrollment processing.

When a registrar completes an enrollment, Laravel A:

1. Saves the enrollment in its own MySQL database.
2. Computes total units.
3. Serializes the full enrollment payload to JSON.
4. Publishes the payload to RabbitMQ queue `enrollment_queue`.

Laravel B consumes that queue separately and handles accounting.

## Roles

- Administrator
- Registrar
- Staff

## Main Features

- Login and registration
- Role-based middleware
- Student CRUD
- Course CRUD
- Subject CRUD
- Enrollment creation and record view
- RabbitMQ publisher service
- Bootstrap 5 Blade UI

## Project Structure

- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/StudentController.php`
- `app/Http/Controllers/CourseController.php`
- `app/Http/Controllers/SubjectController.php`
- `app/Http/Controllers/EnrollmentController.php`
- `app/Services/EnrollmentService.php`
- `app/Services/RabbitMqPublisher.php`
- `app/Http/Middleware/EnsureRole.php`
- `database/migrations/*`
- `database/seeders/DatabaseSeeder.php`
- `resources/views/*`

## Setup

1. Create a MySQL database named `laravel_a`.
2. Copy `.env.example` to `.env`.
3. Update database and RabbitMQ credentials in `.env`.
4. Run:

```bash
php artisan key:generate
php artisan migrate --seed
php artisan serve --port=8000
```

## Environment

```env
APP_NAME="Laravel A"
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_DATABASE=laravel_a
RABBITMQ_HOST=127.0.0.1
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/
RABBITMQ_QUEUE=enrollment_queue
```

## Laravel B Location

The consumer application is in [`laravel-b/`](laravel-b).

## Sample Accounts

- `admin@example.com` / `password`
- `registrar@example.com` / `password`
- `staff@example.com` / `password`

## Verification

Run the test suite with:

```bash
php artisan test
```

