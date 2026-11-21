# Design Note - Task Management API

## 1. Tech Stack & Architecture
- **Language:** PHP 8.2 (No heavy framework, lightweight Front-Controller pattern for fast execution).
- **Database:** SQLite (Zero configuration, portable) using PHP PDO with Prepared Statements to prevent SQL injection.

## 2. Database Schema
Table: `tasks`
- `id`: INTEGER PRIMARY KEY AUTOINCREMENT
- `title`: VARCHAR(255) NOT NULL
- `description`: TEXT NULL
- `status`: VARCHAR(20) DEFAULT 'pending' (Allowed: pending, in_progress, completed)
- `created_at`: DATETIME DEFAULT CURRENT_TIMESTAMP

## 3. API Endpoints & Responses
- `POST /tasks` -> Validates `title`, inserts record, returns `201 Created` with task JSON.
- `GET /tasks` -> Returns `200 OK` with array of tasks. Supports `?status=` filter.
- `PATCH /tasks/{id}` -> Validates status enum, updates record, returns `200 OK` or `404 Not Found`.
- `DELETE /tasks/{id}` -> Deletes record, returns `200 OK` or `404 Not Found`.

## 4. Edge Cases Handled
- Empty or missing `title` returns `400 Bad Request`.
- Invalid `status` value returns `400 Bad Request`.
- Updating/Deleting non-existent ID returns `404 Not Found`.