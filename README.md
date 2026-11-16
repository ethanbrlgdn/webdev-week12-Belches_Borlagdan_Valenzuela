# Reading List API (Week 12)

A small token-protected JSON API built with Laravel, Sanctum and PostgreSQL.
Each user keeps a list of books they want to read, are reading, or have finished.

## Setup

    composer install
    cp .env.example .env        # set DB_* for PostgreSQL and SEED_PASSWORD
    php artisan key:generate
    php artisan migrate:fresh --seed
    php artisan serve

The seeder creates two users (alice@example.com, bob@example.com) with three books each.
Their password is the SEED_PASSWORD value from .env.

The Postman collection is `webdev-week12-collection.json`. Set its `password` variable to your
SEED_PASSWORD, then run the folders in order.

## Endpoints

All requests and responses are JSON. Protected routes need `Authorization: Bearer <token>`.

| Method    | URL             | Auth / ability     | Description                                              | Success |
|-----------|-----------------|--------------------|----------------------------------------------------------|---------|
| POST      | /api/register   | none               | Create an account, returns a full token                  | 201     |
| POST      | /api/login      | none               | Log in; send `"read_only": true` for a read-only token   | 200     |
| POST      | /api/logout     | token              | Delete the token used for this request                   | 200     |
| GET       | /api/me         | token              | Show the signed-in user                                  | 200     |
| GET       | /api/books      | token, books:read  | List my books, 10 per page                               | 200     |
| GET       | /api/books/{id} | token, books:read  | Show one of my books                                     | 200     |
| POST      | /api/books      | token, books:write | Create a book                                            | 201     |
| PUT/PATCH | /api/books/{id} | token, books:write | Change a book                                            | 200     |
| DELETE    | /api/books/{id} | token, books:write | Delete a book                                            | 200     |

Errors: 401 (no, wrong, or logged-out token / wrong password), 403 (read-only token on a write),
404 (book not found or belongs to someone else), 422 (invalid data, with an `errors` object).

## Questions

**What is the difference between a 401 and a 403?**
401 Unauthorized means the API does not know who you are: there is no token, the token is wrong,
or it was logged out. 403 Forbidden means the API knows who you are, but you are not allowed to do
this. In our API, a read-only token trying to create, change or delete a book gets a 403.

**What does the database store for a token?**
Each token is one row in `personal_access_tokens`: the owner (`tokenable_type` and `tokenable_id`),
a `name`, its `abilities` (for example `["books:read"]`), `last_used_at`, `expires_at` and timestamps.
The `token` column holds only a SHA-256 hash of the secret part, never the plain token. The client gets
`id|secret` once at login; Sanctum finds the row by id and compares the hash. Logging out deletes the row,
so the same token fails with 401 afterwards.

**Why use a Resource instead of returning the model?**
A Resource decides exactly which fields leave the API, so private columns like `user_id` or a password
are never sent by accident, even if the table changes later. It also keeps every response in the same
shape (`data`, plus `links` and `meta` for paginated lists).
