# Laravel B - Accounting Management System

Laravel B is the consumer application. It listens to the RabbitMQ queue published by Laravel A, validates the enrollment payload, creates or updates student records, computes tuition from fee schedules, generates assessments, and stores the financial history in its own MySQL database.

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

1. Laravel A publishes an enrollment payload to `enrollment_queue`.
2. Laravel B runs `php artisan rabbitmq:consume-enrollments`.
3. The worker validates the payload.
4. Laravel B creates or updates the student record.
5. Laravel B looks up the fee schedule and computes tuition and total assessment.
6. Laravel B writes the assessment and ledger entry to its own database.
7. Laravel B logs the processed payload, including the group member names.

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

1. Create a MySQL database named `laravel_b`.
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
APP_NAME="Laravel B"
APP_URL=http://localhost:8001
DB_CONNECTION=mysql
DB_DATABASE=laravel_b
QUEUE_CONNECTION=rabbitmq
RABBITMQ_HOST=127.0.0.1
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/
RABBITMQ_QUEUE=enrollment_queue
COMMUNICATION_RABBITMQ_QUEUE=enrollment_queue
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

