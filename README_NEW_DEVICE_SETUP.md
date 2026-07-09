# Run The System On A New Device

This guide explains how to move this project to a different Windows device and run it again from scratch.

It covers:

- cloning or pulling the code from GitHub
- installing the required software
- installing useful VS Code extensions
- setting up Docker Desktop for MySQL and RabbitMQ
- configuring both Laravel apps
- running the actual commands
- common troubleshooting steps

## Project Overview

This repository contains two separate Laravel apps:

- `student-api - M/Enrollment-system`
- `student-api - M/Accounting-System`

They communicate through RabbitMQ.

- Enrollment System publishes student, course, subject, and enrollment events.
- Accounting System mirrors those records into its own database and processes enrollment assessments.

## 1. Install Required Software

Install these on the new device first:

- Git
- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL 8 or MariaDB
- RabbitMQ
- Docker Desktop
- Visual Studio Code

### Recommended VS Code extensions

These are optional but helpful:

- PHP Intelephense
- Laravel Blade Snippets
- Laravel Artisan
- Tailwind CSS IntelliSense
- DotENV

## 2. Get The Code From GitHub

### First time on the new device

Open PowerShell and run:

```powershell
git clone <your-github-repo-url>
cd "LARAVEL PROJECT - ENROLLMENTxACCOUNTING SYSTEM"
```

### If the code is already cloned

Use this to update it later:

```powershell
cd "C:\xampp\htdocs\LARAVEL PROJECT - ENROLLMENTxACCOUNTING SYSTEM"
git pull origin main
```

## 3. Start Docker Desktop

Open Docker Desktop and wait until it says it is running.

If you want Docker to provide MySQL and RabbitMQ, use the commands below.

### MySQL container

If port `3306` is free:

```powershell
docker run -d --name school-mysql -e MYSQL_ROOT_PASSWORD=rootpass -p 3306:3306 mysql:8.4
```

If port `3306` is already taken, use another host port such as `3307`:

```powershell
docker run -d --name school-mysql -e MYSQL_ROOT_PASSWORD=rootpass -p 3307:3306 mysql:8.4
```

### Create the databases

If you used port `3306` and want to create both databases inside the container:

```powershell
docker exec -it school-mysql mysql -uroot -prootpass -e "CREATE DATABASE enrollment_system; CREATE DATABASE accounting_system;"
```

If you used host port `3307`, the databases are still inside the same container. The port only changes what the host connects to.

### RabbitMQ container

Run RabbitMQ with the management UI:

```powershell
docker run -d --name school-rabbitmq -p 5672:5672 -p 15672:15672 rabbitmq:3-management
```

Open the management UI here:

- `http://localhost:15672`

Default login:

- username: `guest`
- password: `guest`

## 4. Configure The Enrollment System

Open a new PowerShell terminal:

```powershell
cd "C:\xampp\htdocs\LARAVEL PROJECT - ENROLLMENTxACCOUNTING SYSTEM\student-api - M\Enrollment-system"
Copy-Item .env.example .env
composer install
npm install
php artisan key:generate
```

Edit the `.env` file and set the database and RabbitMQ values.

### Example `.env` values for Enrollment System

```env
APP_NAME="Enrollment System"
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=enrollment_system
DB_USERNAME=root
DB_PASSWORD=rootpass
RABBITMQ_HOST=127.0.0.1
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/
RABBITMQ_EXCHANGE=school.events
RABBITMQ_QUEUE=enrollment.events
```

If you used Docker MySQL on port `3307`, change `DB_PORT=3307`.

Then run:

```powershell
php artisan migrate --seed
npm run build
php artisan serve --port=8000
```

## 5. Configure The Accounting System

Open another PowerShell terminal:

```powershell
cd "C:\xampp\htdocs\LARAVEL PROJECT - ENROLLMENTxACCOUNTING SYSTEM\student-api - M\Accounting-System"
Copy-Item .env.example .env
composer install
npm install
php artisan key:generate
```

Edit the `.env` file and set the database and RabbitMQ values.

### Example `.env` values for Accounting System

```env
APP_NAME="Accounting System"
APP_URL=http://localhost:8001
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=accounting_system
DB_USERNAME=root
DB_PASSWORD=rootpass
QUEUE_CONNECTION=rabbitmq
RABBITMQ_HOST=127.0.0.1
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/
RABBITMQ_EXCHANGE=school.events
RABBITMQ_QUEUE=accounting.events
```

If you used Docker MySQL on port `3307`, change `DB_PORT=3307`.

Then run:

```powershell
php artisan migrate --seed
npm run build
php artisan serve --port=8001
```

## 6. Start The RabbitMQ Consumer

Open a third PowerShell terminal inside the Accounting System folder:

```powershell
cd "C:\xampp\htdocs\LARAVEL PROJECT - ENROLLMENTxACCOUNTING SYSTEM\student-api - M\Accounting-System"
php artisan rabbitmq:consume-enrollments
```

Keep this terminal open.

## 7. Daily Startup Order

When you already have everything installed, use this order:

1. Start Docker Desktop
2. Start MySQL container, if you use Docker for MySQL
3. Start RabbitMQ container, if you use Docker for RabbitMQ
4. Start the Enrollment System
5. Start the Accounting System
6. Start the Accounting consumer

### Quick command list

Enrollment System:

```powershell
cd "C:\xampp\htdocs\LARAVEL PROJECT - ENROLLMENTxACCOUNTING SYSTEM\student-api - M\Enrollment-system"
php artisan serve --port=8000
```

Accounting System:

```powershell
cd "C:\xampp\htdocs\LARAVEL PROJECT - ENROLLMENTxACCOUNTING SYSTEM\student-api - M\Accounting-System"
php artisan serve --port=8001
php artisan rabbitmq:consume-enrollments
```

## 8. Useful Verification Commands

Check Laravel migrations:

```powershell
php artisan migrate:status
```

Clear caches if something looks stale:

```powershell
php artisan optimize:clear
```

Build front-end assets:

```powershell
npm run build
```

Run tests:

Enrollment System:

```powershell
php artisan test
```

Accounting System:

```powershell
php artisan test
```

## 9. Docker Desktop Notes

- Docker is optional for the Laravel apps themselves.
- Docker is useful if you want MySQL and RabbitMQ to be portable.
- The apps can still run on the host machine with `php artisan serve`.
- If MySQL or RabbitMQ are already installed locally, you can skip Docker for those services.

## 10. Common Troubleshooting

### `Base table or view not found`

This means migrations have not been run yet.

Fix:

```powershell
php artisan migrate
```

### `SQLSTATE[HY000] [1049] Unknown database`

Create the database first, then update `.env`.

### RabbitMQ consumer does not receive messages

Check these:

- RabbitMQ container or service is running
- `RABBITMQ_HOST`, `RABBITMQ_PORT`, `RABBITMQ_USER`, and `RABBITMQ_PASSWORD` are correct
- the consumer terminal is open
- the exchange name is `school.events`

### Port already in use

If ports `8000`, `8001`, `3306`, or `5672` are busy, change them to free ports and update the `.env` files.

### Cached config still shows old settings

Run:

```powershell
php artisan optimize:clear
```

## 11. Suggested Port Layout

If you want a clean local setup, this is a simple layout:

- Enrollment System: `http://localhost:8000`
- Accounting System: `http://localhost:8001`
- MySQL: `127.0.0.1:3306`
- RabbitMQ AMQP: `127.0.0.1:5672`
- RabbitMQ UI: `http://localhost:15672`

## 12. If You Want A Fresh Reinstall

If the project is broken and you want to start over on the new device:

1. Delete only the local `vendor` and `node_modules` folders if needed.
2. Re-run `composer install`.
3. Re-run `npm install`.
4. Recreate `.env` from `.env.example`.
5. Run migrations again.

Example:

```powershell
composer install
npm install
php artisan key:generate
php artisan migrate --seed
```

## 13. What Each App Does

Enrollment System:

- student registration
- course management
- subject management
- enrollment creation
- RabbitMQ publishing

Accounting System:

- student sync
- course sync
- subject sync
- enrollment mirror sync
- assessment generation
- ledger entries
- payment history
- activity logging

## 14. After Everything Is Running

Open these in your browser:

- `http://localhost:8000`
- `http://localhost:8001`
- `http://localhost:15672` for RabbitMQ management

If you want, I can also make a `docker-compose.yml` so MySQL, RabbitMQ, Enrollment System, and Accounting System can all start with one command.
