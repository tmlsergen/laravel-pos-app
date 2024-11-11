# Laravel Pos Application

A simple POS application built with Laravel & Vue.

## Features
- Garanti Bank POS integration
- Multiple POS support

## Getting Started

These instructions will guide you to run the project on your local machine for development and testing purposes.

### Prerequisites

Software required to run the project:

- Docker
- Docker Compose

### Installation and Running

1. Clone the project:
   ```
   git clone https://github.com/tmlsergen/laravel-pos-app.git
   ```
   ```
   cd laravel-pos-app
   ```
2. Create a copy of the `.env.example` file and rename it to `.env`:
   ```
   cp .env.example .env
   ```
   
3. Install Composer dependencies:
   ```
   make composer-install
   ```
   This command will run a Docker container and install the dependencies.

4. To run the application:
   ```
   make run-app
   ```
   
5. Install NPM dependencies:
   ```
   make npm-install
   ```
6. Migrate the database:
   ```
   make migrate
   ```
   
7. To stop the application:
   ```
   make stop-app
   ```

### Finally open your browser and go to http://localhost

## Users

- Admin
  - Email: admin@case.com
  - Password: Test1234!

- Support
  - Email: support@case.com
  - Password: Test1234!

- User
  - Email: user@case.com
  - Password: Test1234!

