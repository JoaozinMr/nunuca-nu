# Nunuca Nu - E-commerce Admin Panel

This is a Laravel-based administrative panel for managing products and orders. It uses Inertia.js for a modern single-page application experience.

## Overview

The application features a dashboard for key metrics and order tracking, alongside product management capabilities. 

### Selected Docstrings

Here are some partial docstrings from the core controllers to give you an idea of the internal structure:

**Dashboard Controller (`DashboardController.php`)**
```php
/**
 * Display the main dashboard with KPI cards and recent orders.
 * Computes revenue today, monthly revenue, order counts and low stock warnings.
 */
public function index(): Response
```

**Product Controller (`ProductController.php`)**
```php
/**
 * Update an existing product.
 *
 * File uploads can't use PUT/PATCH directly in HTML forms, so this route
 * accepts POST with an `_method=PATCH` field (Inertia method spoofing).
 */
public function update(UpdateProductRequest $request, Product $product): RedirectResponse
```

## Local Development

```bash
composer install
npm install
php artisan key:generate
php artisan migrate
npm run dev
```
