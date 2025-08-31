#!/bin/bash
# Railway Startup Script for MIW Travel Management System
# This ensures proper PORT variable handling and directory setup

# Set default port if not provided
export PORT=${PORT:-8080}

echo "Starting MIW Travel Management System on port $PORT"
echo "Environment: ${RAILWAY_ENVIRONMENT:-local}"

# Initialize upload directories and permissions for Railway
if [ ! -z "$RAILWAY_ENVIRONMENT" ]; then
    echo "?? Initializing Railway upload directories..."

    # Create upload subdirectories in the mounted volume
    mkdir -p /app/uploads/documents
    mkdir -p /app/uploads/payments
    mkdir -p /app/uploads/cancellations
    mkdir -p /app/error_logs

    # Set proper permissions
    chmod -R 755 /app/uploads
    chmod -R 755 /app/error_logs

    # Create index.php files to prevent directory listing
    echo '<?php header("HTTP/1.0 403 Forbidden"); exit("Directory listing is not allowed."); ?>' > /app/uploads/index.php
    echo '<?php header("HTTP/1.0 403 Forbidden"); exit("Directory listing is not allowed."); ?>' > /app/uploads/documents/index.php
    echo '<?php header("HTTP/1.0 403 Forbidden"); exit("Directory listing is not allowed."); ?>' > /app/uploads/payments/index.php
    echo '<?php header("HTTP/1.0 403 Forbidden"); exit("Directory listing is not allowed."); ?>' > /app/uploads/cancellations/index.php

    echo "? Upload directories initialized successfully"
    
    # Install and configure sendmail for fallback email functionality
    echo "?? Setting up email fallback system..."
    
    # Update package list and install sendmail
    apt-get update -qq > /dev/null 2>&1
    apt-get install -y sendmail sendmail-cf > /dev/null 2>&1
    
    # Configure sendmail for basic operation
    echo "127.0.0.1 localhost" >> /etc/hosts
    echo "localhost.localdomain localhost" >> /etc/hosts
    
    # Start sendmail service
    service sendmail start > /dev/null 2>&1
    
    echo "? Email fallback system configured"
fi

# Start PHP built-in server from current directory
exec php -S 0.0.0.0:$PORT
