#!/bin/bash

set -e

echo "🚀 Starting BCMS Development Environment..."

# Navigate to project root
cd "$(dirname "$0")/.."

# Check if .env files exist
if [ ! -f "apps/api/.env" ]; then
    echo "📝 Creating API .env file..."
    cp apps/api/.env.example apps/api/.env
fi

if [ ! -f "apps/web/. env. local" ]; then
    echo "📝 Creating Web .env.local file..."
    cp apps/web/.env.example apps/web/.env.local
fi

# Start containers
echo "🐳 Starting Docker containers..."
docker compose -f infra/docker/compose/docker-compose.yml -f infra/docker/compose/docker-compose.dev.yml up -d --build

# Wait for services
echo "⏳ Waiting for services to be ready..."
sleep 10

# Run migrations
echo "🗃️ Running database migrations..."
docker compose exec api php artisan migrate --force

# Seed database (only if empty)
echo "🌱 Seeding database..."
docker compose exec api php artisan db:seed --force

# Generate key if not set
echo "🔑 Checking application key..."
docker compose exec api php artisan key: generate --force

echo ""
echo "✅ BCMS Development Environment is ready!"
echo ""
echo "📌 Access points:"
echo "   Frontend: http://localhost:3000"
echo "   API:       http://localhost:8000"
echo "   Horizon:  http://localhost:8000/horizon"
echo ""
echo "🔐 Default credentials:"
echo "   Email:     abramz@maroon-net.id"
echo "   Password: password123"
echo ""