#!/bin/bash
# Railway Startup Script for MIW Travel Management System
# This ensures proper PORT variable handling and directory setup

# Set default port if not provided
export PORT=${PORT:-8080}

echo "Starting MIW Travel Management System on port $PORT"
echo "Environment: ${RAILWAY_ENVIRONMENT:-local}"

# Initialize upload directories and permissions for Railway
if [ ! -z "$RAILWAY_ENVIRONMENT" ]; then
    echo "🔧 Initializing Railway upload directories..."
    
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
    
    echo "✅ Upload directories initialized successfully"
fi

# Start PHP built-in server
exec php -S 0.0.0.0:$PORT -t .
