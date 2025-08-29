# MIW Railway Deployment Guide

## Environment Variables Required for Railway

Set these in your Railway service:

### Database (Auto-filled when you connect MySQL service)
```
DB_HOST=${{MySQL.MYSQL_HOST}}
DB_PORT=${{MySQL.MYSQL_PORT}}
DB_NAME=${{MySQL.MYSQL_DATABASE}}
DB_USER=${{MySQL.MYSQL_USER}}
DB_PASS=${{MySQL.MYSQL_PASSWORD}}
```

### Application Settings
```
APP_ENV=production
MAX_FILE_SIZE=10M
MAX_EXECUTION_TIME=300
```

### Email Configuration
```
SMTP_HOST=smtp.gmail.com
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_PORT=587
SMTP_ENCRYPTION=tls
```

## Deployment Notes

1. **Builder**: NixPacks (default)
2. **Start Command**: `php -S 0.0.0.0:$PORT -t .`
3. **Health Check**: Root path `/`
4. **PHP Version**: 8.1+ (specified in composer.json)

## Database Initialization

After deployment, visit: `https://your-app.railway.app/init_database_universal.php`
