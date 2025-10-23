# Simple Email API - Setup and Run Guide

This is a Laravel-based email API with OAuth2 authentication using Laravel Passport. The API allows authenticated users to send emails through a queue system.

## Prerequisites

- PHP 8.1 or higher
- Composer
- PostgreSQL (default) or MySQL database
- Node.js (for frontend assets, if needed)

## Step-by-Step Setup

### 1. Install Dependencies

```bash
composer install
```

### 2. Environment Configuration

Create a `.env` file by copying the example:

```bash
cp .env.example .env
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Run Database Migrations

```bash
php artisan migrate
```

### 5. Seed the Database

```bash
php artisan db:seed --class=SuperAdminSeeder
```

This creates two test users:
- **Super Admin**: `superadmin@example.com` / `superadmin123`
- **Regular User**: `jedidiah@example.com` / `jedidiah123`

### 6. Install Laravel Passport

```bash
php artisan passport:install
```

This will create OAuth2 clients and generate client secrets. **Save the password grant client secret** - you'll need it for authentication.

### 7. Start the Application

**Option A: Using Laravel's built-in server**
```bash
php artisan serve
```
The API will be available at `http://localhost:8000`

### 8. Start the Queue Worker (Important!)

For email processing to work, you need to run the queue worker:

```bash
php artisan queue:work
```

Keep this running in a separate terminal window.

## API Usage

### 1. Get Access Token

**Endpoint:** `POST /api/oauth/token`

**Request Body:**
```json
{
    "grant_type": "password",
    "client_id": "2",
    "client_secret": "{password_grant_client_secret_from_step_6}",
    "username": "superadmin@example.com",
    "password": "superadmin123"
}
```

**Response:**
```json
{
    "token_type": "Bearer",
    "expires_in": 31536000,
    "access_token": "your_access_token_here"
}
```

### 2. Send Email

**Endpoint:** `POST /api/v1/emails`

**Headers:**
```
Authorization: Bearer {access_token_from_step_1}
Content-Type: application/json
```

**Request Body:**
```json
{
    "to": "recipient@example.com",
    "from": "sender@example.com",
    "subject": "Test Email Subject",
    "body": "This is the email body content"
}
```

