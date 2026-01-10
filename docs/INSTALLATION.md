# BCMS Installation Guide

## Complete Step-by-Step Installation for Ubuntu Server

### Prerequisites
- Ubuntu Server 22.04 LTS or newer
- Minimum 4GB RAM, 2 CPU cores, 50GB storage
- Root or sudo access
- Domain name (for SSL/TLS)

---

## Phase 1: Ubuntu Server Preparation

### 1.1 Update System
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl wget git unzip software-properties-common apt-transport-https ca-certificates gnupg lsb-release
```

### 1.2 Set Timezone
```bash
sudo timedatectl set-timezone Asia/Jakarta
```

### 1.3 Configure Firewall
```bash
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

---

## Phase 2: Install Docker & Docker Compose

### 2.1 Install Docker
```bash
# Add Docker's official GPG key
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Add Docker repository
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list. d/docker.list > /dev/null

# Install Docker
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd. io docker-buildx-plugin docker-compose-plugin

# Add user to docker group
sudo usermod -aG docker $USER
newgrp docker

# Verify installation
docker --version
docker compose version
```

---

## Phase 3: Clone and Configure Repository

### 3.1 Clone Repository
```bash
cd /opt
sudo git clone https://github.com/a3ramz-code/bcms_v1.git bcms
sudo chown -R $USER:$USER /opt/bcms
cd /opt/bcms
```

### 3.2 Configure Environment Variables

#### API (Laravel) Environment
```bash
cp apps/api/. env.example apps/api/.env
nano apps/api/.env
```

Update the following values:
```env
APP_NAME="BCMS"
APP_ENV=production
APP_KEY=  # Will be generated
APP_DEBUG=false
APP_URL=https://api.yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=bcms
DB_USERNAME=bcms_user
DB_PASSWORD=your_secure_password

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Payment Gateways
MIDTRANS_SERVER_KEY=your_midtrans_server_key
MIDTRANS_CLIENT_KEY=your_midtrans_client_key
MIDTRANS_IS_PRODUCTION=true

XENDIT_SECRET_KEY=your_xendit_secret_key
XENDIT_PUBLIC_KEY=your_xendit_public_key

# WhatsApp Business API
WHATSAPP_API_URL=https://graph.facebook.com/v17.0
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_ACCESS_TOKEN=your_access_token

# SMS Gateway
SMS_GATEWAY_URL=https://your-sms-gateway.com/api
SMS_GATEWAY_API_KEY=your_sms_api_key
```

#### Web (Next.js) Environment
```bash
cp apps/web/.env. example apps/web/.env. local
nano apps/web/.env.local
```

Update:
```env
NEXT_PUBLIC_API_URL=https://api.yourdomain.com
NEXT_PUBLIC_APP_NAME="BCMS - Maroon-NET"
```

---

## Phase 4: SSL Certificate Setup (Let's Encrypt)

### 4.1 Install Certbot
```bash
sudo apt install -y certbot
```

### 4.2 Generate Certificates
```bash
# Stop any running services on port 80
sudo certbot certonly --standalone -d yourdomain.com -d api.yourdomain.com

# Certificates will be saved to: 
# /etc/letsencrypt/live/yourdomain. com/fullchain.pem
# /etc/letsencrypt/live/yourdomain.com/privkey.pem
```

### 4.3 Update Nginx Configuration
Update `infra/docker/nginx/conf.d/web.conf` and `api. conf` with your domain.

---

## Phase 5: Build and Run with Docker

### 5.1 Development Environment
```bash
# Build and start all services
make dev

# Or using docker compose directly
docker compose -f infra/docker/compose/docker-compose.yml -f infra/docker/compose/docker-compose.dev.yml up -d --build
```

### 5.2 Production Environment
```bash
# Build and start all services
make prod

# Or using docker compose directly
docker compose -f infra/docker/compose/docker-compose. yml -f infra/docker/compose/docker-compose.prod. yml up -d --build
```

---

## Phase 6: Initialize Application

### 6.1 Generate Application Key
```bash
docker compose exec api php artisan key:generate
```

### 6.2 Run Migrations
```bash
docker compose exec api php artisan migrate
```

### 6.3 Seed Database
```bash
docker compose exec api php artisan db:seed
```

### 6.4 Create Storage Link
```bash
docker compose exec api php artisan storage:link
```

### 6.5 Clear and Optimize
```bash
docker compose exec api php artisan config:cache
docker compose exec api php artisan route:cache
docker compose exec api php artisan view:cache
docker compose exec api php artisan optimize
```

---

## Phase 7: Setup Queue Workers (Laravel Horizon)

### 7.1 Start Horizon
```bash
docker compose exec api php artisan horizon
```

Horizon dashboard available at: `https://api.yourdomain.com/horizon`

---

## Phase 8: Setup Cron Jobs (Scheduler)

The scheduler container automatically runs `php artisan schedule:run` every minute. 

Verify it's running:
```bash
docker compose logs -f scheduler
```

---

## Phase 9: Verify Installation

### 9.1 Check All Services
```bash
docker compose ps
```

All services should show "Up" status: 
- nginx
- api
- web
- postgres
- redis
- horizon
- scheduler

### 9.2 Access Application
- **Frontend**: https://yourdomain.com
- **API**: https://api.yourdomain.com
- **Horizon**: https://api.yourdomain.com/horizon

### 9.3 Default Login Credentials
After seeding, use these credentials: 

| User | Email | Password | Role |
|------|-------|----------|------|
| Abramz | abramz@maroon-net.id | password123 | Administrator |
| Fandi | fandi@maroon-net.id | password123 | Supervisor |
| Meci | meci@maroon-net.id | password123 | Finance |
| Yogi | yogi@maroon-net.id | password123 | NOC/Technician |

---

## Maintenance Commands

### Backup Database
```bash
docker compose exec postgres pg_dump -U bcms_user bcms > backup_$(date +%Y%m%d).sql
```

### Restore Database
```bash
cat backup_file.sql | docker compose exec -T postgres psql -U bcms_user bcms
```

### View Logs
```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f api
docker compose logs -f web
```

### Restart Services
```bash
docker compose restart
```

### Update Application
```bash
git pull origin main
docker compose down
docker compose up -d --build
docker compose exec api php artisan migrate
docker compose exec api php artisan optimize: clear
```

---

## Troubleshooting

### Permission Issues
```bash
docker compose exec api chmod -R 775 storage bootstrap/cache
docker compose exec api chown -R www-data:www-data storage bootstrap/cache
```

### Database Connection Issues
```bash
# Check PostgreSQL is running
docker compose logs postgres

# Test connection
docker compose exec api php artisan tinker
>>> DB::connection()->getPdo();
```

### Queue Not Processing
```bash
# Restart Horizon
docker compose restart horizon

# Check Horizon status
docker compose exec api php artisan horizon:status
```
```