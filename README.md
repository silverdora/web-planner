# Web Planner

Web Planner is a web-based task management application that helps users organize tasks, track deadlines, and monitor progress over time.

The application is built with a separated frontend and backend architecture:
- **Frontend:** Vue.js
- **Backend:** PHP REST API
- **Database:** MariaDB
- **Authentication:** JWT
- **Styling:** Tailwind CSS

The project was developed for the Web Development 2 assignment.



# Main functionality

The application includes the following features:

- User registration and login
- JWT-based authentication
- Create, read, update, and delete tasks
- Categories for organizing tasks
- Dashboard with task statistics
- Upcoming deadlines overview
- Filtering by status, priority, category, and search
- Pagination for task lists
- Separate completed tasks page

# Technologies used

## Frontend
- Vue.js
- Vue Router
- Tailwind CSS
- Axios

## Backend
- PHP
- FastRoute
- Composer
- PDO MySQL
- JWT authentication

## Database / tooling
- MariaDB
- PHPMyAdmin
- Docker


# GitHub repository

`https://github.com/silverdora/web-planner`


# Testing credentials

Replace these with the user that actually exists in your database.

#### Test user 
- login: `annavandertest@example.com`
- password: `12345678`


You can also register a new account through the application.

# Example database import

In the root directory of this project included a sample database export `planner.sql`. This file can be imported into MariaDB/MySQL to preload the database with example data for demonstration and testing purposes.


# Project structure

This project uses a separated frontend/backend architecture:

- `frontend/` → Vue.js single-page application
- `backend/` → PHP REST API
- `database/` or SQL import file → database structure/data
- `docker-compose.yml` → Docker services for local development


# Setup

## Requirements

Make sure the following tools are installed:

- Docker Desktop (Windows/Mac) or Docker Engine (Linux)
- Node.js and npm
- Git


# Backend setup

From the project root, start the Docker containers:

```bash
docker compose up
```

This setup includes:

- NGINX
- PHP
- MariaDB
- PHPMyAdmin
- Composer

# Composer install
If Composer dependencies are not installed yet, run:
```bash
docker compose run --rm php composer install
```

If you add new classes or change namespaces, regenerate the autoloader:
```bash
docker compose run --rm php composer dump-autoload
```

# Backend availability

After startup, the backend should be available through the configured local API URL from your Docker setup.

# Frontend setup

Open a second terminal and go to the frontend folder:

```bash
cd frontend
```

Install dependencies:

```bash
npm install
```

Start the Vue development server:

```bash
npm run dev
```

The frontend should then be available at http://localhost:5173

# PHPMyAdmin

PHPMyAdmin is available for database administration at: http://localhost:8080

Default credentials from the Docker template are usually:
username: `root`
password: `secret123`

Check your docker-compose.yml to confirm the exact values used in this project.

# How to use the application

Start Docker:

```bash
docker compose up
```

Start the frontend:
```bash
cd frontend
npm install
npm run dev
```

Open the app in your browser:

http://localhost:5173

Log in with an existing test user or register a new one.

# Implemented assignment criteria

This project was designed to match the Web Development 2 assignment requirements.

Implemented aspects include:

-  Authentic use case: personal task and deadline planner
-  Separated frontend and backend architecture
-  REST API with GET, POST, PUT, and DELETE endpoints
-  JWT-based authentication
-  Vue routing
-  Component-based frontend structure
-  State-oriented frontend organization
-  Filtering and pagination
-  Dashboard statistics and deadline tracking

# AI disclosure statement

AI tools were used during the development of this project as a support tool, mainly for:

-  explaining course concepts
-  debugging backend and frontend issues
-  refactoring components
-  suggesting improvements in code structure
-  improving UI text and documentation

AI was not used to blindly generate the full application.
All generated or suggested code was reviewed, adapted, tested, and integrated manually.

The final implementation, debugging, integration, and understanding of the code were done without AI.

Examples of how AI was used in this project:

-  identifying bugs in Vue component communication
-  improving dashboard logic
-  helping refactor task-related components
-  suggesting better structure for routing and completed tasks page
-  helping write project documentation

# Accessibility and usability

This project aims to follow basic accessibility and usability principles:

-  semantic structure with headings and sections
-  clearly labeled form inputs
-  descriptive buttons and links
-  responsive layout for desktop and mobile
-  readable contrast and spacing
-  feedback messages for loading, success, and error states

# Security notes
-  Authentication is handled with JWT
-  Protected routes require a valid token
-  Users should only access their own tasks and categories
-  Sensitive operations such as update/delete are tied to authenticated users

# Stopping the project
To stop the running containers, press `Ctrl + C` or run:
```bash
docker compose down
```

Notes for the evaluation
-  The application can be tested by logging in with the provided test user or by registering a new account.
-  The dashboard contains statistics, upcoming deadlines, and task filtering.
-  Completed tasks are shown on a separate page.
-  The project uses a separated Vue frontend and PHP backend, connected through REST API endpoints.