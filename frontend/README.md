# Web Planner Frontend

A Vue 3 application for the Web Planner project, built with Vite and Tailwind CSS.

The frontend communicates with a PHP REST API and provides the user interface for authentication, task management, filtering, dashboard statistics, upcoming deadlines, and completed task tracking.



## Tech Stack

- **Vue 3**
- **Vite**
- **Vue Router**
- **Axios**
- **Tailwind CSS**

The project follows a component-based structure and uses routing, which are part of the course requirements for the frontend architecture.


## Project Structure

``` id="3gax4r"
frontend/
├── src/
│   ├── assets/                 # Global styles
│   ├── components/
│   │   ├── atoms/              # Basic UI building blocks
│   │   ├── molecules/          # Small composed UI pieces
│   │   ├── organisms/          # Larger feature components
│   │   ├── templates/          # Layout wrappers
│   │   └── pages/              # Route-based pages
│   ├── router/                 # Vue Router configuration
│   ├── utils/                  # Axios and frontend helpers
│   ├── config.js               # App configuration
│   ├── App.vue                 # Root application component
│   └── main.js                 # App entry point
```

The component structure is based on reusable Vue components and aligns with the course emphasis on logical frontend component architecture.

## Main Pages / Features

The frontend includes:

- Home page
- Login page
- Registration page
- Dashboard page
- Create task page
- Categories page
- Completed tasks page

Routing is configured in Vue Router and protected routes require authentication.

## Authentication

The frontend uses JWT-based authentication.

After login:

1) the token is stored in local storage
2) authenticated requests automatically include the token in the Authorization header through the Axios instance

Protected routes are checked in the router before navigation.

## Setup
Prerequisites
- Node.js
- npm
- Installation

Open a terminal in the frontend folder and run: 

`npm install`

## Available Scripts
### Development
`npm run dev`

Starts the Vite development server.

### Production build
`npm run build`

Builds the frontend for production.

### Preview production build
`npm run preview`

Previews the production build locally.

## Running the Frontend

Start the frontend development server:

`npm run dev`

The application will typically be available at: http://localhost:5173

## API Configuration

The frontend uses a central Axios instance for API requests.

Example configuration flow:

1) base API domain is set in config.js
2) Axios uses that base URL
3) JWT token is automatically attached to requests after login

If needed, update the frontend API domain to match your backend setup.

## Core Frontend Features
### Dashboard

The dashboard includes:

- task list
- statistics
- upcoming deadlines
- filtering
- pagination
- category statistics

The project proposal explicitly includes upcoming deadlines, progress tracking, statistics, and filtering/pagination as planned functionality.

### Upcoming deadlines

The dashboard shows:

- overdue tasks
- tasks due today
- tasks due in the next 7 days


### Completed tasks

Completed tasks are displayed on a separate page.

### Categories

Users can manage categories and assign tasks to them.

## Component Architecture

The frontend is divided into reusable components such as:

- Atoms → buttons, labels, inputs, text, headings, badges
- Molecules → form fields, feedback messages, task actions, pagination controls
- Organisms → dashboard overview, grouped task list, upcoming deadlines, category stats, task cards

Examples of these reusable UI components exist throughout the project structure.


## Development Notes

- The app uses Vue 3 Composition API with `<script setup>`
- Navigation is handled through Vue Router
- API requests are handled with Axios
- Auth token logic is centralized in the Axios utility
- The app uses a reusable component structure for consistency

## Notes

- Make sure the backend API is running before starting the frontend.
- Authentication and protected routes depend on a valid backend connection.
- Some features on the dashboard depend on authenticated API endpoints from the backend.