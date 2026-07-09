# Enrollment System

Enrollment System is the producer application. It handles authentication, role-based access, student management, course management, subject management, and enrollment processing.

When a registrar creates or updates a student, or completes an enrollment, Enrollment System:

1. Saves the record in its own MySQL database.
2. Generates a student number automatically when one is not supplied.
3. Serializes the student or enrollment payload to JSON.
4. Publishes the payload to RabbitMQ exchange `school.events` with routing keys such as `student.registered` and `enrollment.submitted`.

The accounting application lives in [`../Accounting System`](../Accounting%20System) and consumes that queue separately.

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

1. Create a MySQL database named `enrollment_system`.
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
APP_NAME="Enrollment System"
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_DATABASE=enrollment_system
RABBITMQ_HOST=127.0.0.1
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/
RABBITMQ_EXCHANGE=school.events
RABBITMQ_QUEUE=enrollment.events
```

## Accounting System Location

The consumer application is in [`../Accounting System`](../Accounting%20System).

## Sample Accounts

- `admin@example.com` / `password`
- `registrar@example.com` / `password`
- `staff@example.com` / `password`

## Verification

Run the test suite with:

```bash
php artisan test
```
