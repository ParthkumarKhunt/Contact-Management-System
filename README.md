# Contact Management System - Setup & Run Guide

## Overview

The Contact Management System is a Laravel-based web application that allows users to manage contacts with custom fields, merge functionality, and a modern user interface. This guide will help you set up and run the application on your local development environment.

## Installation Steps

### Step 1: Clone the Repository

```bash
# Clone the repository
git clone <repository-url>
cd Contact-Management-System-new

# Or if you have the files directly, navigate to the project directory
cd Contact-Management-System-new
```

### Step 2: Install PHP Dependencies

```bash
# Install Composer dependencies
composer install

# If you encounter memory issues, use:
composer install --ignore-platform-reqs
```

### Step 3: Environment Configuration

```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Database

1. **Create a MySQL database** for the application
2. **Edit the `.env` file** with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=contact_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 5: Run Database Migrations

```bash
# Run database migrations
php artisan migrate

# Seed the database with initial data (optional)
php artisan db:seed
```

### Step 6: Running the Application

### Development Server

# Start the Laravel development server
php artisan serve
