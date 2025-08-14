#!/bin/bash

echo "🚀 Starting build process..."

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Create SQLite database file
echo "🗄️ Setting up SQLite database..."
touch database/database.sqlite

# Generate app key
echo "🔑 Generating application key..."
php artisan key:generate --force

# Run migrations
echo "📊 Running database migrations..."
php artisan migrate --force

# Import data
echo "📥 Importing application data..."
php artisan import:all-data

# Cache configuration
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build completed successfully!"