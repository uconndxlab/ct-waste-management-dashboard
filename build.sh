#!/bin/bash

echo "🚀 Starting build process..."

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Create SQLite database file
echo "🗄️ Setting up SQLite database..."
mkdir -p database
touch database/database.sqlite

# Generate app key if not set
echo "🔑 Generating application key..."
php artisan key:generate --force

# Run migrations
echo "📊 Running database migrations..."
php artisan migrate --force

# Import data
echo "📥 Importing application data..."
php artisan import:all-data

# Cache configuration for production
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

echo "✅ Build completed successfully!"