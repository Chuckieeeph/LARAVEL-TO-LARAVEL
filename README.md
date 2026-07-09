# Integrated Enrollment and Accounting System

This repository contains a two-part Laravel system for school operations:

- `Enrollment System` handles authentication, student records, courses, subjects, and enrollment submission.
- `Accounting System` consumes enrollment events, manages assessments, payments, and financial history.

The two applications communicate through RabbitMQ using the `school.events` exchange.
Enrollment System now publishes student, course, subject, and enrollment events, and Accounting System mirrors those records into its own database while also storing an activity log for every incoming event.

## Project Layout

- `student-api - M/Enrollment-system`
- `student-api - M/Accounting-System`

Each folder is its own Laravel application with its own:

- `.env` file
- MySQL database
- migrations and seeders
- frontend assets

## What To Copy To Another Device

To move the project completely, copy:

1. The full repository folder
2. Both Laravel app folders
3. Both MySQL databases
4. Any uploaded files inside `storage`
5. The `.env` configuration for each app, or recreate it from `.env.example`
7. RabbitMQ settings, if the accounting sync must keep working


## Requirements

Install these on the new device:

- PHP
- Composer
- Node.js and npm
- MySQL
- RabbitMQ

## Optional Docker Desktop Setup

This repository does not currently include a Dockerfile or `docker-compose.yml` for the school apps themselves.
However, Docker Desktop can still be useful for running the supporting services.

If you want to use Docker Desktop, the usual approach is:

- run MySQL in a container
- run RabbitMQ in a container
- keep the Laravel apps running on the host machine with PHP and Composer

Typical service ports:

- MySQL: `3306`
- RabbitMQ AMQP: `5672`
- RabbitMQ Management UI: `15672`

If the containers are published to the host, your `.env` files can still use:

- `DB_HOST=127.0.0.1`
- `RABBITMQ_HOST=127.0.0.1`

You would also need to make sure the databases and RabbitMQ users/queues are created inside the containers.

If you want, I can also create a `docker-compose.yml` for this project so the two Laravel apps, MySQL, and RabbitMQ can be started together from Docker Desktop.

## Setup After Copying

### 1. Install dependencies

Run these in each app folder:

```bash
composer install
npm install
```

### 2. Configure environment files

Copy `.env.example` to `.env` in both applications, then update:

- `APP_URL`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- RabbitMQ host, port, user, password, virtual host, exchange, and queue values

### 3. Generate app keys

If needed, run:

```bash
php artisan key:generate
```

### 4. Import the databases

Create and import these databases on the new device:

- `enrollment_system`
- `accounting_system`

If the database is empty, you can also run migrations and seeders:

```bash
php artisan migrate --seed
```

### 5. Run the applications

Enrollment System:

```bash
php artisan serve --port=8000
```

Accounting System:

```bash
php artisan serve --port=8001
```

### 6. Start the RabbitMQ consumer

In the Accounting System folder, run:

```bash
php artisan rabbitmq:consume-enrollments
```

## Environment Summary

### Enrollment System

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

### Accounting System

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

### Enrollment System

- `admin@example.com` / `password`
- `registrar@example.com` / `password`
- `staff@example.com` / `password`

### Accounting System

- `admin@accounting.test` / `password`
- `cashier@accounting.test` / `password`
- `staff@accounting.test` / `password`

## Verification

Run tests in each app folder:

```bash
php artisan test
```

## Notes

- The Enrollment System publishes student, course, subject, and enrollment events.
- The Accounting System listens to those events, mirrors the student, course, subject, and enrollment records into its own database, and writes an activity log for each message.
- If RabbitMQ is not running, the two apps will still open, but the synchronization flow will fail.

## Architecture

For a deeper system breakdown, see [`ARCHITECTURE.md`](./ARCHITECTURE.md).

For a full step-by-step guide to run the project on a different device, see [`README_NEW_DEVICE_SETUP.md`](./README_NEW_DEVICE_SETUP.md).
