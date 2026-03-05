# Marketplace Deployment on InfinityFree

## Prerequisites

- InfinityFree account (free hosting)
- FTP client (FileZilla, WinSCP, or similar)
- Domain name (can use InfinityFree's subdomain)

## Project Structure

The project has been configured for InfinityFree deployment:

```
htdocs/
├── index.html (React app entry point)
├── assets/ (compiled React assets)
├── api/ (PHP backend API)
│   ├── config/ (configuration files)
│   │   ├── db.php (database connection)
│   │   ├── cors.php (CORS configuration)
│   │   └── jwt.php (JWT authentication)
│   ├── auth/ (authentication endpoints)
│   │   ├── register.php
│   │   ├── login.php
│   │   └── me.php
│   ├── categories/ (categories endpoints)
│   │   └── index.php
│   ├── products/ (products endpoints)
│   │   ├── index.php
│   │   ├── [id].php
│   │   ├── create.php
│   │   ├── update.php
│   │   └── delete.php
│   ├── shops/ (shops endpoints)
│   │   ├── index.php
│   │   ├── [id].php
│   │   ├── create.php
│   │   └── me.php
│   ├── orders/ (orders endpoints)
│   │   ├── index.php
│   │   ├── [id].php
│   │   └── create.php
│   ├── payments/ (payments endpoints)
│   │   ├── index.php
│   │   └── create.php
│   ├── reports/ (reports endpoints)
│   │   ├── index.php
│   │   ├── [id].php
│   │   ├── create.php
│   │   └── me.php
│   ├── admin/ (admin endpoints)
│   │   ├── users.php
│   │   ├── shops.php
│   │   ├── products.php
│   │   ├── orders.php
│   │   ├── reports.php
│   │   └── categories.php
│   └── .htaccess (API directory protection)
└── .htaccess (React Router SPA fallback)
```

## Deployment Steps

### 1. Build the Frontend

```bash
cd frontend
npm install
npm run build
```

This will compile the React app into the `htdocs/` directory.

### 2. Configure Database

1. Log into your InfinityFree account
2. Go to "MySQL Databases"
3. Note your database credentials:
   - Database Name: `if0_41302424_marketplace`
   - Username: `if0_41302424`
   - Password: `Obanana0917203`
4. Import the database schema from `sql/schema.sql` using phpMyAdmin

### 3. Upload Files via FTP

1. Open your FTP client
2. Connect to InfinityFree FTP server using your account credentials
3. Navigate to the `htdocs/` directory
4. Upload all files from the project's `htdocs/` directory to the server's `htdocs/` directory
5. Ensure the `api/` directory has the correct permissions (755 for directories, 644 for files)

### 4. Verify Deployment

1. Open a web browser and navigate to your domain
2. Test the main functionality:
   - User registration and login
   - Product browsing
   - Shop creation
   - Checkout process
3. Test API endpoints using browser DevTools or curl

## API Configuration

The backend API is configured to:

- Use MySQLi for database connections (InfinityFree compatible)
- Support CORS from your domain and local development (http://localhost:5173)
- Use JWT for authentication
- Return JSON responses with proper status codes

## Common Issues

### 1. File Permissions

If you encounter "Permission denied" errors:
- Ensure all directories have 755 permissions
- Ensure all files have 644 permissions

### 2. Database Connection

If you get "Database connection failed" errors:
- Verify your database credentials in `htdocs/api/config/db.php`
- Ensure your IP is whitelisted (InfinityFree may require this for external connections)

### 3. CORS Issues

If you get "CORS policy" errors:
- Check the `htdocs/api/config/cors.php` file
- Ensure your domain is in the `$allowedOrigins` array

### 4. Rewrite Rules

If React Router routes don't work (404 errors):
- Ensure the `.htaccess` file is present in the `htdocs/` directory
- Verify that mod_rewrite is enabled (InfinityFree should have this enabled)

## Development vs. Production

### Local Development

```bash
cd frontend
npm run dev
```

This will start a local development server at http://localhost:5173 with API proxying to the server.

### Production

The production version is compiled using:

```bash
cd frontend
npm run build
```

This creates optimized production files in the `htdocs/` directory.

## Database Management

- Use phpMyAdmin from the InfinityFree control panel to manage your database
- The initial schema is located at `sql/schema.sql`
- Regularly back up your database using phpMyAdmin's export feature

## Security Considerations

- Keep your JWT secret in `htdocs/api/config/jwt.php` secure
- Do not share your database credentials
- Regularly update your dependencies
- Enable HTTPS if possible (InfinityFree offers free SSL certificates)

## Technical Support

If you encounter issues:

1. Check the browser's developer console for errors
2. Check the server's error logs (InfinityFree's control panel has a "Errors" section)
3. Verify your database connection
4. Check file permissions

For additional support, contact InfinityFree's customer support or refer to their documentation.
