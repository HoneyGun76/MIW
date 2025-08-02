# 📁 MIW Upload Configuration & Railway Integration

## 🎯 Overview

This document describes the upload system configuration fixes applied to ensure proper file upload functionality on Railway with persistent storage using mounted volumes.

## 🚀 Railway Volume Setup

### Volume Configuration
- **Volume Name:** `endearing-volume`
- **Mounted To:** `web` service
- **Mount Path:** `/app/uploads`
- **Storage:** 5GB (416MB currently used)
- **Region:** Southeast Asia (Singapore)

### Verification Commands
```bash
# Check volume status
railway volume list

# Expected output:
# Volume: endearing-volume
# Attached to: web
# Mount path: /app/uploads
# Storage used: 416MB/5000MB
```

## 🔧 Code Changes Applied

### 1. **upload_handler.php** - Environment Detection
**Problem:** Used hardcoded `$_SERVER['DOCUMENT_ROOT'] . '/MIW/'` path
**Solution:** Added Railway environment detection

```php
public function __construct($uploadBaseDir = 'uploads') {
    // Detect Railway environment and set appropriate upload directory
    if ($this->isRailwayEnvironment()) {
        // Railway: Use mounted volume at /app/uploads
        $this->uploadBaseDir = '/app/' . $uploadBaseDir;
    } else {
        // Local development: Use document root path
        $this->uploadBaseDir = rtrim($_SERVER['DOCUMENT_ROOT'] . '/MIW/' . $uploadBaseDir, '/');
    }
    // ... rest of constructor
}

private function isRailwayEnvironment() {
    return isset($_ENV['RAILWAY_ENVIRONMENT']) || 
           isset($_ENV['RAILWAY_PROJECT_ID']) || 
           getenv('RAILWAY_ENVIRONMENT') ||
           isset($_ENV['DB_HOST']);
}
```

### 2. **config.php** - Upload Directory Function
**Problem:** Used `sys_get_temp_dir() . '/uploads'` for Railway
**Solution:** Updated to use mounted volume

```php
function getUploadDirectory() {
    if (isProduction()) {
        // Railway: use mounted volume for persistent uploads
        return '/app/uploads';
    } else {
        // Local: use uploads directory
        return __DIR__ . '/uploads';
    }
}
```

### 3. **file_handler.php** - File Serving
**Problem:** Hardcoded local path for serving files
**Solution:** Added environment detection for file serving

```php
// Setup paths - detect Railway environment
if (isset($_ENV['RAILWAY_ENVIRONMENT']) || isset($_ENV['RAILWAY_PROJECT_ID']) || getenv('RAILWAY_ENVIRONMENT')) {
    // Railway: Use mounted volume
    $uploadsDir = '/app/uploads/';
} else {
    // Local development
    $uploadsDir = __DIR__ . '/uploads/';
}
```

### 4. **start.sh** - Startup Initialization
**Problem:** No directory structure initialization on startup
**Solution:** Added comprehensive directory setup

```bash
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
    # ... (creates index.php for all subdirectories)
    
    echo "✅ Upload directories initialized successfully"
fi
```

### 5. **diagnostic.php** - Error Log Management
**Problem:** Only checked local error logs directory
**Solution:** Added support for persistent Railway error logs

```php
// Check both Railway persistent and local error log directories
$logDirs = [];

if (getenv('RAILWAY_ENVIRONMENT')) {
    // Railway: Check persistent error logs first, then local
    $logDirs = [
        '/app/error_logs',
        __DIR__ . '/error_logs'
    ];
} else {
    // Local development
    $logDirs = [__DIR__ . '/error_logs'];
}
```

## 📂 Directory Structure

### Railway Production Structure
```
/app/
├── uploads/                    # Mounted volume (persistent)
│   ├── documents/              # Document uploads
│   ├── payments/               # Payment receipts
│   ├── cancellations/          # Cancellation documents
│   └── index.php              # Security file
├── error_logs/                # Persistent error logs
│   └── *.log                  # Application error logs
└── [application files]
```

### Local Development Structure
```
project_root/
├── uploads/                    # Local uploads directory
│   ├── documents/
│   ├── payments/
│   └── cancellations/
├── error_logs/                # Local error logs
└── [application files]
```

## 🔍 Validation & Testing

### 1. Upload Path Verification
```php
// Test upload directory detection
echo "Upload Directory: " . getUploadDirectory();
// Railway: Should output "/app/uploads"
// Local: Should output "[project_path]/uploads"
```

### 2. File Upload Test
```bash
# Via diagnostic.php - Check "File Permissions" section
# Railway should show:
# ✅ /app/uploads - Exists: Yes, Readable: Yes, Writable: Yes
# ✅ /app/uploads/documents - Exists: Yes, Readable: Yes, Writable: Yes
```

### 3. File Serving Test
```bash
# Test file access via file_handler.php
curl "https://your-app.railway.app/file_handler.php?file=test.pdf&type=documents&action=preview"
# Should return file content or proper error message
```

### 4. Error Logging Test
```php
// Test error log creation in persistent directory
error_log("Test error message", 3, "/app/error_logs/test.log");
// Check via diagnostic.php if error appears in logs
```

## 🚨 Troubleshooting

### Common Issues

#### 1. **403 Forbidden on File Access**
**Cause:** File not found in mounted volume
**Solution:** 
- Check if volume is properly mounted: `railway volume list`
- Verify file exists: Check diagnostic.php "File Permissions"
- Test upload functionality first

#### 2. **Files Disappear After Deployment**
**Cause:** Using ephemeral storage instead of mounted volume
**Solution:**
- Verify upload handler uses `/app/uploads` path
- Check environment detection in `isRailwayEnvironment()`

#### 3. **Upload Directory Not Writable**
**Cause:** Permissions not set during startup
**Solution:**
- Check start.sh execution: `railway logs --tail 50`
- Look for "🔧 Initializing Railway upload directories..." message
- Manually verify permissions: `chmod -R 755 /app/uploads`

#### 4. **Error Logs Not Appearing**
**Cause:** Writing to wrong directory
**Solution:**
- Check if `/app/error_logs` directory exists
- Verify error_log configuration in PHP
- Use diagnostic.php to check all log locations

### Environment Variables to Check
```bash
# Railway CLI
railway variables

# Should include:
# UPLOAD_PATH=/app/uploads/
# RAILWAY_ENVIRONMENT=production
# RAILWAY_PROJECT_ID=[project-id]
```

## 📈 Performance Considerations

### File Upload Optimization
- **Max File Size:** 10MB (configurable via `MAX_FILE_SIZE` env var)
- **Allowed Types:** PDF, JPG, PNG only
- **Volume Size:** 5GB total capacity
- **Backup:** Automatic via Railway volume snapshots

### Monitoring
- **Storage Usage:** Monitor via `railway volume list`
- **Upload Errors:** Check diagnostic.php error logs
- **Performance:** Use diagnostic.php system health checks

## 🔒 Security Features

### Directory Protection
- **Index Files:** Prevent directory listing
- **File Validation:** Type and size restrictions
- **Path Traversal Protection:** `realpath()` validation
- **Access Control:** Subdirectory-based organization

### File Access Control
```php
// Valid file types mapping
$validTypes = [
    'documents' => 'documents',     # User documents
    'payments' => 'payments',       # Payment receipts  
    'cancellations' => 'cancellations'  # Cancellation docs
];
```

## 📝 Maintenance

### Regular Tasks
1. **Monitor Storage Usage:** `railway volume list`
2. **Check Error Logs:** Via diagnostic.php daily
3. **Backup Verification:** Test file restoration periodically
4. **Permission Check:** Ensure directories remain writable

### Updates Required
When updating upload-related code:
1. Test both Railway and local environments
2. Verify environment detection works correctly
3. Update this documentation if paths change
4. Test file serving functionality

---

**Created:** August 2, 2025  
**Last Updated:** August 2, 2025  
**Version:** 1.0.0  
**Author:** GitHub Copilot  
