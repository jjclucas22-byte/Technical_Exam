# Technical Exam - Customer Management Application

A full-stack customer management application built for the technical exam.

The project provides a Laravel REST API, a React frontend, a MySQL database, and an Elasticsearch search service. The application allows users to create, view, update, delete, list, and search customer records.


---

## Features

- Create a customer
- View a customer
- Update a customer
- Delete a customer
- List all customers
- Search customers by name or email address
- Synchronize created and updated customers to Elasticsearch
- Remove deleted customers from Elasticsearch
- Laravel request validation
- Unique email validation
- React CRUD interface
- PHPUnit feature and unit tests
- Docker Compose setup with four required services

---

## Required Docker Services

The Docker setup contains the four required services:

| Service | Purpose |
| --- | --- |
| `api` | Laravel PHP-FPM backend application |
| `controller` | Nginx controller/load balancer that serves React and forwards `/api` requests to Laravel |
| `database` | MySQL relational database |
| `searcher` | Elasticsearch search service for customer records |

The React frontend is built and served through the `controller` service so the Compose setup remains focused on the four required services.

---

## Tech Stack

### Backend

- PHP
- Laravel
- MySQL
- Laravel HTTP Client
- PHPUnit
- Laravel Pint

### Frontend

- React
- Vite
- Bootstrap
- Fetch API

### Infrastructure

- Docker
- Docker Compose
- Nginx
- Elasticsearch

---

## Customer Data

Each customer record contains:

| Field | Requirement |
| --- | --- |
| `first_name` | Required string |
| `last_name` | Required string |
| `email` | Required valid email and unique |
| `contact_number` | Required string |

---

## Project Structure

```text
Technical-Exam/
├── api/
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   └── Api/
│   │   │   │       └── CustomerController.php
│   │   │   └── Requests/
│   │   │       ├── StoreCustomerRequest.php
│   │   │       └── UpdateCustomerRequest.php
│   │   ├── Models/
│   │   │   └── Customer.php
│   │   ├── Observers/
│   │   │   └── CustomerObserver.php
│   │   └── Services/
│   │       └── CustomerSearchService.php
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   │       ├── CustomerSeeder.php
│   │       └── DatabaseSeeder.php
│   ├── routes/
│   │   └── api.php
│   └── tests/
│       ├── Feature/
│       │   └── CustomerApiTest.php
│       └── Unit/
│           └── CustomerSearchServiceTest.php
├── controller/
├── frontend/
│   └── src/
│       ├── components/
│       │   ├── CustomerDetails.jsx
│       │   ├── CustomerForm.jsx
│       │   └── CustomerTable.jsx
│       ├── services/
│       │   └── customerApi.js
│       ├── App.jsx
│       ├── index.css
│       └── main.jsx
├── scripts/
├── docker-compose.yml
└── README.md
```

---

## Environment Configuration

Each developer must create their own local `.env` files. The project does not commit personal `.env` files because they may contain machine-specific settings or secrets.

Create the root environment file:

```bash
cp .env.example .env
```

Create the Laravel API environment file:

```bash
cp api/.env.example api/.env
```

On Windows PowerShell:

```powershell
Copy-Item .env.example .env
Copy-Item api/.env.example api/.env
```

After creating `api/.env`, make sure it contains the following relevant values:

```env
APP_NAME="Technical Exam"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

LOG_CHANNEL=stderr

DB_CONNECTION=mysql
DB_HOST=database
DB_PORT=3306
DB_DATABASE=sugarcrm
DB_USERNAME=sugarcrm
DB_PASSWORD=secret

ELASTICSEARCH_URL=http://searcher:9200
ELASTICSEARCH_INDEX=customers
```

Inside Docker Compose, the API connects to MySQL and Elasticsearch through the service names `database` and `searcher`.

If `api/.env` does not exist, Laravel commands and Docker startup may fail.

---

## Installation

### 1. Clone the repository

```bash
git clone <your-repository-url>
cd Technical-Exam
```

### 2. Start the Docker containers

```bash
docker compose up -d --build
```

### 3. Generate the Laravel application key

```bash
docker compose exec api php artisan key:generate --force
```

### 4. Run database migrations and seeders

```bash
docker compose exec api php artisan migrate:fresh --seed
```

### 5. Open the application

```text
http://localhost:8080
```

---

## Common Development Commands

### View running containers

```bash
docker compose ps
```

### Rebuild and restart the project

```bash
docker compose up -d --build
```

### Stop the project

```bash
docker compose down
```

### Stop the project and remove database/search volumes

```bash
docker compose down -v
```

### Run Laravel commands

```bash
docker compose exec api php artisan
```

### Clear Laravel caches

```bash
docker compose exec api php artisan optimize:clear
```

### Recreate the database and seed sample data

```bash
docker compose exec api php artisan migrate:fresh --seed
```

### Reset the Elasticsearch customers index

```bash
docker compose exec api curl -X DELETE http://searcher:9200/customers
docker compose exec api php artisan db:seed
```

---

## API Endpoints

All API routes are public. 

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/customers` | List all customers |
| `GET` | `/api/customers?search=Maria` | Search customers by name or email |
| `POST` | `/api/customers` | Create a customer |
| `GET` | `/api/customers/{id}` | View a customer |
| `PUT` | `/api/customers/{id}` | Update a customer |
| `PATCH` | `/api/customers/{id}` | Partially update a customer |
| `DELETE` | `/api/customers/{id}` | Delete a customer |

---

## Example API Requests

### Create a customer

```bash
curl -X POST http://localhost:8080/api/customers \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "first_name": "Maria",
    "last_name": "Santos",
    "email": "maria.santos@example.com",
    "contact_number": "09171234567"
  }'
```

### List customers

```bash
curl http://localhost:8080/api/customers
```

### Search customers

```bash
curl "http://localhost:8080/api/customers?search=Maria"
```

### View a customer

```bash
curl http://localhost:8080/api/customers/1
```

### Update a customer

```bash
curl -X PUT http://localhost:8080/api/customers/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "first_name": "Maria",
    "last_name": "Reyes",
    "email": "maria.reyes@example.com",
    "contact_number": "09179999999"
  }'
```

### Delete a customer

```bash
curl -X DELETE http://localhost:8080/api/customers/1
```

---

## PowerShell API Examples

### Create a customer

```powershell
$body = @{
    first_name     = "Maria"
    last_name      = "Santos"
    email          = "maria.santos@example.com"
    contact_number = "09171234567"
} | ConvertTo-Json

Invoke-RestMethod `
    -Method Post `
    -Uri "http://localhost:8080/api/customers" `
    -ContentType "application/json" `
    -Body $body
```

### List customers

```powershell
Invoke-RestMethod `
    -Method Get `
    -Uri "http://localhost:8080/api/customers"
```

### Search customers

```powershell
Invoke-RestMethod `
    -Method Get `
    -Uri "http://localhost:8080/api/customers?search=Maria"
```

---

## Elasticsearch Synchronization

The application synchronizes customer records to Elasticsearch manually through Laravel's HTTP client.

Laravel Scout is not used.

Synchronization flow:

1. A customer is created, updated, or deleted through the Laravel API.
2. `CustomerObserver` listens for the model event.
3. `CustomerSearchService` sends the correct HTTP request to Elasticsearch.
4. The `GET /api/customers?search=...` endpoint searches Elasticsearch by customer name and email.
5. Matching Elasticsearch document IDs are used to retrieve the final records from MySQL.

This keeps Elasticsearch as the search index while MySQL remains the source of truth.

---

## Backend Code Organization

The backend follows a simple separation of responsibilities:

| Class/File | Responsibility |
| --- | --- |
| `CustomerController` | Handles HTTP requests and JSON responses |
| `StoreCustomerRequest` | Validates customer creation input |
| `UpdateCustomerRequest` | Validates customer update input |
| `Customer` | Represents the customer database model |
| `CustomerObserver` | Reacts to create, update, and delete model events |
| `CustomerSearchService` | Handles direct Elasticsearch HTTP communication |

This keeps the controller thin and avoids placing validation, database schema logic, and Elasticsearch logic in one class.

---

## Frontend Code Organization

The React frontend is separated into reusable files:

| File | Responsibility |
| --- | --- |
| `App.jsx` | Manages page-level state and coordinates CRUD actions |
| `CustomerForm.jsx` | Handles create and update form UI |
| `CustomerTable.jsx` | Displays customer list and action buttons |
| `CustomerDetails.jsx` | Displays selected customer details |
| `customerApi.js` | Centralizes API requests to Laravel |

The frontend uses relative API paths such as `/api/customers`, allowing Nginx to route API calls to the Laravel backend.

---

## Running Tests

Run all tests:

```bash
docker compose exec api php artisan test
```

Run only the customer-related tests:

```bash
docker compose exec api php artisan test tests/Feature/CustomerApiTest.php tests/Unit/CustomerSearchServiceTest.php
```

The tests cover:

- Listing customers
- Creating customers
- Required field validation
- Unique email validation
- Viewing customers
- Updating customers
- Deleting customers
- Elasticsearch indexing
- Elasticsearch searching
- Elasticsearch deletion

Authentication tests are not included because authentication is not part of this project scope.

---

## Code Formatting

Run Laravel Pint:

```bash
docker compose exec api ./vendor/bin/pint
```

For the frontend, run:

```bash
cd frontend
npm run build
```

---

## Troubleshooting

### `Unable to connect to the remote server`

Check that Docker containers are running:

```bash
docker compose ps
```

If the `controller` service is not running, restart the project:

```bash
docker compose up -d --build
```

### Laravel log permission error

If Laravel cannot write to `storage/logs/laravel.log`, run:

```bash
docker compose exec -u root api sh -c "mkdir -p storage/logs bootstrap/cache && chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache"
docker compose exec api php artisan optimize:clear
```

The project is also configured to use `LOG_CHANNEL=stderr` for Docker-friendly logging.

### Missing customer test data

Recreate the database and seed it again:

```bash
docker compose exec api php artisan migrate:fresh --seed
```

### Elasticsearch search has stale data

Delete the customer index and seed again:

```bash
docker compose exec api curl -X DELETE http://searcher:9200/customers
docker compose exec api php artisan db:seed
```

### API routes are not appearing

Check the route list:

```bash
docker compose exec api php artisan route:list --path=api
```

Expected routes:

```text
GET       api/customers
POST      api/customers
GET       api/customers/{customer}
PUT       api/customers/{customer}
PATCH     api/customers/{customer}
DELETE    api/customers/{customer}
```

---

## Git Workflow

Recommended commit flow:

```bash
git add .
git commit -m "Set up Docker environment"
git commit -m "Implement customer CRUD API"
git commit -m "Add Elasticsearch customer synchronization"
git commit -m "Implement React customer CRUD interface"
git commit -m "Add customer API and search service tests"
git commit -m "Update project README"
```

Before submitting:

```bash
docker compose up -d --build
docker compose exec api php artisan migrate:fresh --seed
docker compose exec api php artisan test
```

Then push to a public Git repository.

---

## Final Notes

- The project does not use Laravel Sail.
- The project does not use Laravel Scout.
- MySQL is the source of truth.
- Elasticsearch is used only for searching customer records.
- Docker Compose runs the required services: `api`, `controller`, `database`, and `searcher`.
