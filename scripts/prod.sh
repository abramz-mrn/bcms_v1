#!/bin/bash

set -e

echo "🚀 Starting BCMS Production Environment..."

# Navigate to project root
cd "$(dirname "$0")/.."

# Check if .env files exist
if [ ! -f "apps/api/.env" ]; then
    echo "❌ Error: apps/api/.env file not found!"
    echo "Please create the .env file with production settings."
    exit 1
fi

if [ ! -f "apps/web/.env.local" ]; then
    echo "❌ Error: apps/web/.env.local file not found!"
    echo "Please create the .env. local file with production settings."
    exit 1
fi

# Build and start containers
echo "🐳 Building and starting Docker containers..."
docker compose -f infra/docker/compose/docker-compose.yml -f infra/docker/compose/docker-compose. prod.yml up -d --build

# Wait for services
echo "⏳ Waiting for services to be ready..."
sleep 15

# Run migrations
echo "🗃️ Running database migrations..."
docker compose exec api php artisan migrate --force

# Optimize Laravel
echo "⚡ Optimizing Laravel..."
docker compose exec api php artisan config:cache
docker compose exec api php artisan route:cache
docker compose exec api php artisan view:cache
docker compose exec api php artisan optimize

echo ""
echo "✅ BCMS Production Environment is ready!"
echo ""