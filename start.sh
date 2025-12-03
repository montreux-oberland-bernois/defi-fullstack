#!/bin/bash

set -euo pipefail

# Colors for better readability
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Trap errors and cleanup
trap 'handle_error $? $LINENO' ERR

handle_error() {
    echo ""
    echo -e "${RED}[ERROR]${NC} Script failed at line $2 with exit code $1"
    echo -e "${YELLOW}[INFO]${NC} Cleaning up containers..."
    docker-compose down 2>/dev/null || true
    exit $1
}

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Header
echo ""
echo "========================================================="
echo "  Train Routing & Analytics Application - Startup"
echo "========================================================="
echo ""

# Check Docker
log_info "Checking Docker installation..."
if ! command -v docker &> /dev/null; then
    log_error "Docker is not installed. Please install Docker first."
    echo "  Visit: https://docs.docker.com/get-docker/"
    exit 1
fi
log_success "Docker found: $(docker --version)"

# Check Docker Compose
log_info "Checking Docker Compose installation..."
if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
    log_error "Docker Compose is not installed. Please install Docker Compose first."
    echo "  Visit: https://docs.docker.com/compose/install/"
    exit 1
fi

if docker compose version &> /dev/null; then
    COMPOSE_CMD="docker compose"
    log_success "Docker Compose found: $(docker compose version)"
else
    COMPOSE_CMD="docker-compose"
    log_success "Docker Compose found: $(docker-compose --version)"
fi

# Check if Docker daemon is running
log_info "Checking Docker daemon..."
if ! docker info &> /dev/null; then
    log_error "Docker daemon is not running. Please start Docker first."
    exit 1
fi
log_success "Docker daemon is running"

# Clean up any existing containers from previous failed runs
log_info "Cleaning up previous containers..."
$COMPOSE_CMD down -v --remove-orphans 2>/dev/null || true

# Build and start containers
echo ""
log_info "Building Docker images..."
if ! $COMPOSE_CMD build; then
    log_error "Failed to build Docker images"
    exit 1
fi
log_success "Docker images built successfully"

echo ""
log_info "Starting containers..."
if ! $COMPOSE_CMD up -d; then
    log_error "Failed to start containers"
    exit 1
fi
log_success "Containers started"

# Wait for services with progress indicator
echo ""
log_info "Waiting for services to be healthy..."

wait_for_service() {
    local service=$1
    local port=$2
    local max_attempts=30
    local attempt=1

    while [ $attempt -le $max_attempts ]; do
        if nc -z localhost $port 2>/dev/null || curl -f -s -o /dev/null "http://localhost:$port" 2>/dev/null; then
            log_success "$service is ready"
            return 0
        fi
        echo -n "."
        sleep 2
        attempt=$((attempt + 1))
    done

    log_warning "$service did not respond after $max_attempts attempts"
    return 1
}

# Check PostgreSQL
echo -n "  Database (PostgreSQL:5432): "
if command -v nc &> /dev/null; then
    wait_for_service "Database" 5432 || log_warning "Database may need more time to initialize"
else
    sleep 5
    log_warning "netcat not found, skipping port check"
fi

# Check Backend
echo -n "  Backend (PHP:8000): "
wait_for_service "Backend" 8000 || log_warning "Backend may need more time to initialize"

# Check Frontend
echo -n "  Frontend (Vue:3000): "
wait_for_service "Frontend" 3000 || log_warning "Frontend may need more time to initialize"

# Check Nginx HTTPS
echo -n "  Nginx HTTPS (8443): "
wait_for_service "Nginx HTTPS" 8443 || log_warning "Nginx may need more time to initialize"

# Display status
echo ""
echo ""
log_success "Application is running!"
echo ""
echo "========================================================="
echo "  Access Points"
echo "========================================================="
echo "  Frontend:  http://localhost:3000"
echo "  Backend:   http://localhost:8000/api/v1"
echo "  Nginx:     https://localhost:8443 (HTTPS)"
echo "  Nginx:     http://localhost:8080 (redirects to HTTPS)"
echo "  Database:  localhost:5432"
echo ""
echo "========================================================="
echo "  Important Notes"
echo "========================================================="
echo "  * You'll see a browser warning about the self-signed"
echo "    SSL certificate. This is normal for local development."
echo "    Click 'Advanced' and proceed to the site."
echo ""
echo "  * First startup may take longer as images are built"
echo "    and dependencies are installed."
echo ""
echo "========================================================="
echo "  Useful Commands"
echo "========================================================="
echo "  Stop services:     $COMPOSE_CMD down"
echo "  View logs:         $COMPOSE_CMD logs -f"
echo "  View status:       $COMPOSE_CMD ps"
echo "  Restart:           $COMPOSE_CMD restart"
echo "  Rebuild:           $COMPOSE_CMD up -d --build"
echo ""
echo "========================================================="
echo "  Documentation"
echo "========================================================="
echo "  * README.md - Project overview and architecture"
echo ""
echo "========================================================="
echo ""
