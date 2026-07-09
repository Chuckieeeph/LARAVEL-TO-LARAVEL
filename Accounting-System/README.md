# Accounting System

Accounting System is the consumer application. It listens to the RabbitMQ queue published by the Enrollment System, validates the enrollment payload, creates or updates student records, computes tuition from fee schedules, generates assessments, stores an enrollment log, and writes the financial history in its own MySQL database.

## Roles

- Accounting Administrator
- Cashier
- Accounting Staff

## Main Features

- Login and registration
- Role-based middleware
- Student ledger management
- Fee schedule CRUD
- Assessment listing and detail pages
- Payment processing
- Transaction history
- RabbitMQ consumer command with ACK and NACK
- Bootstrap 5 Blade UI

## Queue Flow

1. Enrollment System publishes a student or enrollment event to the `school.events` exchange.
2. Accounting System runs `php artisan rabbitmq:consume-enrollments`.
3. The worker inspects `event_type` to decide whether the message is a student sync or an enrollment sync.
4. Student sync messages create or update the student record in Accounting.
5. Enrollment sync messages update the student, compute tuition, and create the assessment.
6. Accounting System writes ledger entries and enrollment log records in its own database.

## Project Structure

- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/StudentController.php`
- `app/Http/Controllers/FeeScheduleController.php`
- `app/Http/Controllers/AssessmentController.php`
- `app/Http/Controllers/PaymentController.php`
- `app/Http/Controllers/TransactionController.php`
- `app/Services/AccountingEnrollmentProcessor.php`
- `app/Services/RabbitMqConsumer.php`
- `app/Console/Commands/ConsumeEnrollmentQueue.php`
- `app/Http/Middleware/EnsureRole.php`
- `database/migrations/*`
- `database/seeders/DatabaseSeeder.php`
- `resources/views/*`

## Setup

1. Create a MySQL database named `accounting_system`.
2. Copy `.env.example` to `.env`.
3. Update database and RabbitMQ credentials in `.env`.
4. Run:

```bash
php artisan key:generate
php artisan migrate --seed
php artisan serve --port=8001
```

5. Start the RabbitMQ consumer in a second terminal:

```bash
php artisan rabbitmq:consume-enrollments
```

## Environment

```env
APP_NAME="Accounting System"
APP_URL=http://localhost:8001
DB_CONNECTION=mysql
DB_DATABASE=accounting_system
QUEUE_CONNECTION=rabbitmq
RABBITMQ_HOST=127.0.0.1
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/
RABBITMQ_EXCHANGE=school.events
RABBITMQ_QUEUE=accounting.events
```

## Sample Accounts

- `admin@accounting.test` / `password`
- `cashier@accounting.test` / `password`
- `staff@accounting.test` / `password`

## Verification

Run the test suite with:

```bash
php artisan test
```
