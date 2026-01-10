# BCMS - Billing & Customer Management System

[![CI](https://github.com/abramz-mrn/bcms_v1/actions/workflows/ci.yml/badge.svg)](https://github.com/abramz-mrn/bcms_v1/actions/workflows/ci.yml)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A comprehensive Billing and Customer Management System for Internet Service Providers (ISPs) with Mikrotik integration, automated billing, and multi-channel notifications.

## 🚀 Features

- **Multi-role Authentication** - Granular RBAC with Laravel Sanctum
- **Service Provisioning** - PPPoE & Static IP with auto-provisioning
- **Billing Automation** - Invoice generation, reminders, auto-suspend
- **Payment Integration** - Midtrans, Xendit, virtual accounts, manual payments
- **Mikrotik Integration** - RouterOS API with TLS + SSH fallback
- **Notification System** - Email, SMS & WhatsApp Business API
- **Ticketing System** - Customer support with SLA tracking
- **Reporting** - Export Excel/PDF with custom filters
- **Audit Logs** - Complete action tracking

## 🛠️ Tech Stack

| Layer | Technology |
|-------|------------|
| Frontend | Next.js 22, TypeScript, Tailwind CSS |
| Backend | Laravel 12, PHP 8.3, Laravel Sanctum |
| Database | PostgreSQL 18 |
| Cache/Queue | Redis 8, Laravel Horizon |
| Runtime | Laravel Octane (RoadRunner) |
| Container | Docker, Docker Compose |
| Web Server | Nginx 1.28 with SSL/TLS |

## 📋 Prerequisites

- Docker & Docker Compose
- Git
- Domain name (for production SSL)

## 🚀 Quick Start

### Development

```bash
# Clone repository
git clone https://github.com/abramz-mrn/bcms_v1.git
cd bcms_v1

# Copy environment files
cp apps/api/.env.example apps/api/.env
cp apps/web/.env.example apps/web/. env. local

# Start development environment
make dev

# Run migrations and seeders
make migrate
make seed

# Access application
# Frontend: http://localhost:3000
# API: http://localhost:8000
# Horizon: http://localhost:8000/horizon