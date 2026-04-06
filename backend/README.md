# Web Planner API

A RESTful API for the Web Planner application, built with PHP and following an MVC-based architecture.

The API provides authentication, task management, category management, dashboard statistics, and upcoming deadline data for the Vue frontend.


## Project Structure

``` id="2xar4q"
backend/
├── app/
│   ├── public/
│   │   └── index.php              # Entry point and route dispatcher
│   └── src/
│       ├── Controllers/           # Request handlers
│       ├── Services/              # Business logic layer
│       ├── Repositories/          # Data access layer
│       ├── Models/                # Data models
│       ├── Enums/                 # Status and priority enums
│       ├── Framework/             # Base controller and repository classes
│       └── Exceptions/            # Custom exceptions
├── docker-compose.yml             # Docker services configuration
├── nginx.conf                     # Nginx configuration
├── PHP.Dockerfile                 # PHP Docker image configuration
└── composer.json                  # PHP dependencies and autoloading
```

## API Endpoints

### Authentication
#### Register

`POST /auth/register`

Content-Type: `application/json`

Example body:
```
{
"name": "Test User",
"email": "test@example.com",
"password": "00000000"
}
```

Creates a new user account.

#### Login
`POST /auth/login`

Content-Type: `application/json`

Example body:
```
{
"email": "test@example.com",
"password": "00000000"
}
```

Returns a JWT token and basic user data.

#### Logout
`POST /auth/logout`

Returns a success message. Authentication state is handled mainly on the client side by removing the token.

### Tasks
#### Get dashboard tasks
`GET /tasks/dashboard`

Supports query parameters such as:

-  search
-  status
-  priority
-  category_id
-  sort
-  page
-  limit

Returns paginated dashboard task data. The backend includes dedicated dashboard routes and filtering/pagination support.

#### Get dashboard statistics
`GET /tasks/dashboard-stats`

Returns task statistics such as:

-  total tasks
-  done tasks
-  pending tasks
-  overdue tasks

#### Get upcoming deadlines
`GET /tasks/upcoming`

Returns grouped task deadlines:

-  overdue
-  today
-  week

#### Get task by ID
`GET /tasks/{id}`

Returns a specific task for the authenticated user.

#### Create task
`POST /tasks`

Content-Type: application/json

Example body:

```
{
"title": "Finish README",
"description": "Write project documentation",
"categoryId": 1,
"priority": "high",
"dueDate": "2026-04-10 18:00:00"
}
```

Creates a new task for the authenticated user.

#### Update task
`PUT /tasks/{id}`

Content-Type: `application/json`

Updates an existing task owned by the authenticated user.

#### Change task status
`PUT /tasks/change-status`

Content-Type: `application/json`

Example body:

```
{
"taskId": 12,
"status": "done"
}
```

Updates only the task status.

#### Delete task
`DELETE /tasks/{id}`

Deletes a task owned by the authenticated user.

### Categories
#### Get all categories
`GET /categories`


#### Get category by ID
`GET /categories/{id}`


#### Create category
`POST /categories`

Content-Type: `application/json`


#### Update category
`PUT /categories/{id}`

Content-Type: `application/json`


#### Delete category
`DELETE /categories/{id}`

The backend routing for auth, tasks, dashboard routes, and categories is defined in the project router.

### Authentication

This API uses JWT authentication.

After logging in, the frontend stores the token and sends it in the Authorization header for authenticated requests. JWT-based authentication is part of the project requirements and is implemented in both backend and frontend.

Example header:

`Authorization: Bearer <your-jwt-token>`

## Getting Started
Prerequisites
- Docker
- Docker Compose
- Installation

From the backend folder, start the Docker containers:

`docker compose up`

Install Composer dependencies:

`docker compose run --rm php composer install`

If you add new classes or change namespaces, regenerate the autoloader:

`docker compose run --rm php composer dump-autoload`

The course setup uses Composer and PSR-4 autoloading for backend architecture.

## Running the Application

The application will typically be available at:

API: http://localhost

phpMyAdmin: http://localhost:8080

Check your Docker configuration if the ports differ.

### Database

The backend uses MariaDB/MySQL.

A sample database export is included in the root folder of the project:

`planner.sql`

This file can be imported into MariaDB/MySQL to preload the database with example data for testing the application.

You can import it through PHPMyAdmin or another MySQL-compatible tool.

### Docker Services

Typical services in this project:

- nginx → web server
- php → PHP-FPM
- mysql / mariadb → database
- phpmyadmin → database management interface

Check docker-compose.yml for the exact service names, ports, and credentials used in your setup.

## Notes
-  The API is structured using controllers, services, repositories, models, and framework classes. This matches the required MVC-style backend architecture for the assignment.
-  User-specific authorization is enforced through authenticated requests and ownership checks.
-  Tasks and categories are linked to the logged-in user.