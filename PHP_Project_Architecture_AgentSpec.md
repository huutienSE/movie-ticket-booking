# PHP Vanilla Project — Architecture Specification for AI Agent

## Overview

> **Stack:** Pure PHP (Vanilla PHP) — no Laravel, Symfony, or CodeIgniter.
> **Pattern:** Separated Frontend (SPA) + Backend (REST API) communicating exclusively via JSON over HTTP.
> **Goal:** Apply modern software design principles (SoC, SRP, Layered Architecture) without any framework.

---

## Architecture Model

```
Frontend (SPA)
    └── fetch() → JSON
            ↓
    API Endpoint  (api/*.php)
            ↓
    Controller    (app/Controllers/)
            ↓
    Service       (app/Services/)
            ↓
    Model         (app/Models/)
            ↓
    MySQL Database
```

---

## Layer Responsibilities

### 1. API Endpoint (`api/*.php`)
- **Role:** Entry point for all HTTP requests from frontend.
- **Responsibilities:**
  - Instantiate dependencies: `Database`, `Model`, `Service`, `Controller`.
  - Pass the request into the appropriate `Controller`.
- **Hard constraints:**
  - ❌ No business logic.
  - ❌ No SQL queries.
  - ❌ No direct DB access.

### 2. Controller (`app/Controllers/`)
- **Role:** Orchestrates request/response flow.
- **Responsibilities:**
  - Read input from `$_GET`, `$_POST`, or `php://input` (JSON body).
  - Call the appropriate `Service` method.
  - Return JSON response to client.
- **Hard constraints:**
  - ❌ No SQL queries.
  - ❌ No business logic.
  - ❌ No direct DB access.

### 3. Service (`app/Services/`)
- **Role:** Contains all business logic.
- **Responsibilities (examples):**
  - Check if a movie is currently showing.
  - Check if a seat is already booked.
  - Validate a schedule.
  - Calculate total price.
  - Apply a voucher/discount.
  - Check user authentication status.
  - Check user permissions/roles.
- **Capabilities:**
  - May call multiple `Model` classes to complete a business operation.
- **Hard constraints:**
  - ❌ No SQL queries directly.
  - ❌ No HTTP response logic.

### 4. Model (`app/Models/`)
- **Role:** Database access layer only.
- **Responsibilities:**
  - Execute `SELECT`, `INSERT`, `UPDATE`, `DELETE` queries.
  - Return raw data to the `Service`.
- **Hard constraints:**
  - ❌ No business logic.
  - ❌ No decision-making (e.g., do NOT check if a seat is available — return seat data and let Service decide).

### 5. Database Config (`config/database.php`)
- **Role:** Establish and provide a MySQL connection.
- **Responsibilities:**
  - Create a PDO or MySQLi connection.
  - Inject the connection object into Models.
- **Hard constraints:**
  - ❌ No business logic.
  - ❌ No query execution.

### 6. Frontend (`public/`)
- **Role:** SPA (Single Page Application) — UI only.
- **Responsibilities:**
  - Render HTML/CSS interface.
  - Handle user events via JavaScript.
  - Call backend via `fetch()` and render JSON responses.
- **Hard constraints:**
  - ❌ No direct database access.
  - ❌ No business logic.
  - ❌ No knowledge of backend DB schema.

---

## Directory Structure

```
project/
│
├── public/                   # Frontend SPA (HTML, CSS, JS only)
│   ├── index.php             # Single entry point for frontend
│   ├── css/
│   ├── js/
│   └── assets/
│
├── api/                      # API Endpoints (HTTP entry points)
│   ├── auth.php
│   ├── movie.php
│   ├── booking.php
│   ├── seat.php
│   └── schedule.php
│
├── app/                      # Backend application logic
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── MovieController.php
│   │   ├── BookingController.php
│   │   ├── SeatController.php
│   │   └── ScheduleController.php
│   │
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── MovieService.php
│   │   ├── BookingService.php
│   │   ├── SeatService.php
│   │   └── ScheduleService.php
│   │
│   ├── Models/
│   │   ├── UserModel.php
│   │   ├── MovieModel.php
│   │   ├── BookingModel.php
│   │   ├── SeatModel.php
│   │   └── ScheduleModel.php
│   │
│   ├── Helpers/              # Reusable utility functions
│   ├── Middleware/           # Auth checks, CORS, rate limiting (optional)
│   └── Validators/           # Input validation logic (optional)
│
├── config/
│   └── database.php          # DB connection only
│
└── uploads/                  # File uploads storage
```

---

## Request Flow (Example: Get Movie List)

```
1. Browser         → fetch("GET /api/movie.php")
2. api/movie.php   → new Database() → new MovieModel(db) → new MovieService(model) → new MovieController(service)
                   → controller->handleRequest()
3. MovieController → reads $_GET params → calls MovieService->getMovies()
4. MovieService    → applies business rules → calls MovieModel->findAll()
5. MovieModel      → executes SELECT query → returns raw array
6. MovieService    → filters/transforms data → returns to Controller
7. MovieController → json_encode(data) → echo JSON with HTTP 200
8. Browser         → JavaScript renders UI from JSON
```

---

## Code Conventions

| Layer | Naming | Returns |
|---|---|---|
| API Endpoint | `api/resource.php` | — (delegates only) |
| Controller | `ResourceController.php` | JSON via `echo` |
| Service | `ResourceService.php` | PHP array/object |
| Model | `ResourceModel.php` | PHP array/object |
| Config | `database.php` | PDO/MySQLi connection |

### Response Format (JSON Standard)
```json
{
  "status": "success",
  "data": { },
  "message": ""
}
```
```json
{
  "status": "error",
  "message": "Seat already booked."
}
```

### HTTP Headers (every API endpoint must set)
```php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
```

---

## Design Principles (Non-Negotiable)

| Rule | Details |
|---|---|
| No SQL in Controller | All queries go through Model only |
| No SQL in JavaScript | Frontend never touches DB |
| No business logic in Model | Model = data retrieval only |
| No business logic in API Endpoint | Endpoint = dependency wiring only |
| No HTML mixed with PHP logic | Strict frontend/backend separation |
| No direct DB access from JavaScript | All DB interaction via API |
| Frontend is DB-agnostic | Frontend only knows the JSON contract |
| Backend is frontend-agnostic | Backend returns JSON, doesn't care if client is HTML/React/Vue/Mobile |

---

## Agent Planning Notes

- **Frontend and Backend are independently deployable units** — plan them separately.
- The **API contract (endpoints + JSON schema)** must be defined before coding either side.
- All **business rule logic lives exclusively in Services** — this is the most critical layer.
- **Models must be thin** — no conditional logic, no role checks, no availability checks.
- **Controllers must be thin** — read input → call service → write output.
- Dependencies flow: `API Endpoint → Controller → Service → Model → DB`.
- No layer should skip a level (e.g., Controller must not call Model directly).
- When adding a new feature: create `Model` → `Service` → `Controller` → `API Endpoint` → wire frontend `fetch()`.
