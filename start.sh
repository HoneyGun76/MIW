#!/bin/bash
# Railway Startup Script for MIW Travel Management System
# This ensures proper PORT variable handling

# Set default port if not provided
export PORT=${PORT:-8080}

echo "Starting MIW Travel Management System on port $PORT"
echo "Environment: ${RAILWAY_ENVIRONMENT:-local}"

# Start PHP built-in server
exec php -S 0.0.0.0:$PORT -t .
