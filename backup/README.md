# 🛡️ MIW Railway Backup & Recovery System

**Version 1.0.0** - Production-Ready Backup Solution for Railway Deployment

## 📋 Overview

This backup system provides comprehensive protection for your MIW Travel Management System deployed on Railway. It includes database backups, file backups, emergency rollback procedures, and branch management tools.

### 🎯 Key Features

- **🔄 Automated Database Backups** - Scheduled MySQL dumps with compression
- **📁 File System Backups** - Complete upload directory preservation
- **🚨 Emergency Rollback** - One-click rollback to stable production state
- **🔀 Branch Management** - Switch Railway deployment between git branches
- **🖥️ Web Dashboard** - User-friendly backup management interface
- **⚡ CLI Tools** - Command-line backup and restore utilities

---

## 🏗️ System Architecture

### Branch Strategy
```
main (development)
├── production-backup (stable production mirror)
├── staging (testing environment)
└── Railway deploys from: main (can be switched)
```

### Backup Structure
```
backup/
├── database_backup.php      # Database backup engine
├── file_backup.php         # File system backup engine
├── backup_dashboard.php    # Web management interface
├── railway_switcher.php    # Branch deployment switcher
├── emergency_rollback.sh   # Unix emergency script
├── emergency_rollback.bat  # Windows emergency script
├── database/              # Database backup storage
├── files/                 # File backup storage
└── README.md             # This documentation
```

---

## 🚀 Quick Start Guide

### 1. Initial Setup

#### Create Backup Directory Structure
```bash
# In your MIW project root
mkdir -p backup/database backup/files
chmod 755 backup/database backup/files
```

#### Set Environment Variables (Railway)
Add to your Railway service environment variables:
```env
BACKUP_PASSWORD=your_secure_backup_password
```

#### Set Permissions
```bash
# Make emergency scripts executable
chmod +x backup/emergency_rollback.sh
```

### 2. Access Backup Dashboard

Visit: `https://your-app.railway.app/backup/backup_dashboard.php`

**Default Password:** `backup123` (local) / Set `BACKUP_PASSWORD` (production)

### 3. Create Your First Backup

#### Via Web Dashboard:
1. Open backup dashboard
2. Click "Create Database Backup" 
3. Click "Create File Backup"
4. Or use "Create Full Backup" for both

#### Via Command Line:
```bash
# Database backup
php backup/database_backup.php create

# File backup  
php backup/file_backup.php create

# List backups
php backup/database_backup.php list
php backup/file_backup.php list
```

---

## 🔧 Detailed Usage

### Database Backup System

#### Features:
- **Compression:** Automatic gzip compression
- **Retention:** 7-day automatic cleanup (configurable)
- **Railway Compatible:** Works with Railway MySQL service
- **Cross-Platform:** Windows/Linux/macOS support

#### CLI Commands:
```bash
# Create backup
php backup/database_backup.php create

# List all backups
php backup/database_backup.php list

# Restore from backup
php backup/database_backup.php restore miw_backup_2025-08-02_14-30-15.sql.gz
```

#### Backup File Format:
- **Filename:** `miw_backup_YYYY-MM-DD_HH-MM-SS.sql.gz`
- **Location:** `backup/database/` (local) or `/app/backups/database/` (Railway)
- **Compression:** Gzip level 9

### File Backup System

#### Features:
- **Complete Upload Directory:** Backs up all uploaded documents
- **Archive Creation:** TAR.GZ or ZIP format
- **Exclusion Patterns:** Automatic exclusion of cache, logs, etc.
- **Incremental:** Only backs up changed files

#### CLI Commands:
```bash
# Create file backup
php backup/file_backup.php create

# List file backups
php backup/file_backup.php list

# Restore from backup
php backup/file_backup.php restore miw_files_2025-08-02_14-30-15.tar.gz
```

#### Backup Locations:
- **Source:** `/app/uploads` (Railway) or `uploads/` (local)
- **Destination:** `/app/backups/files/` (Railway) or `backup/files/` (local)

---

## 🚨 Emergency Procedures

### Scenario 1: Application Breaking Changes

**Problem:** New deployment breaks the application

**Solution:** Emergency Rollback
```bash
# Unix/Linux/macOS
./backup/emergency_rollback.sh

# Windows
backup\emergency_rollback.bat
```

**What it does:**
1. Switches to `production-backup` branch
2. Force pushes to `main` (triggers Railway deployment)
3. Restores stable application state
4. Takes ~2-3 minutes

### Scenario 2: Database Corruption

**Problem:** Database data is corrupted or lost

**Solution:** Database Restore
```bash
# Via CLI
php backup/database_backup.php list
php backup/database_backup.php restore [backup_filename]

# Or via web dashboard:
# https://your-app.railway.app/backup/backup_dashboard.php
```

### Scenario 3: File Upload Issues

**Problem:** Uploaded files are missing or corrupted

**Solution:** File Restore
```bash
# Via CLI
php backup/file_backup.php list
php backup/file_backup.php restore [backup_filename]

# Or via web dashboard
```

### Scenario 4: Complete System Recovery

**Problem:** Need to restore everything

**Solution:** Full System Restore
1. Database restore (from latest backup)
2. File restore (from latest backup)
3. Emergency rollback (if needed)
4. Verify functionality

---

## 🔀 Branch Management

### Railway Deployment Switcher

Access: `https://your-app.railway.app/backup/railway_switcher.php`

#### Available Branches:
- **main:** Development branch (latest features)
- **production-backup:** Stable production mirror (recommended for production)
- **staging:** Testing branch (for testing new features)

#### Switching Process:
1. Choose target branch
2. System fetches latest code
3. Force pushes to main branch
4. Railway automatically deploys

#### Use Cases:
- **Emergency:** Switch to `production-backup` for stability
- **Testing:** Switch to `staging` for feature testing
- **Development:** Switch to `main` for latest features

---

## 📊 Monitoring & Maintenance

### Backup Monitoring

#### Check Backup Status:
```bash
# Recent database backups
ls -la backup/database/ | head -10

# Recent file backups
ls -la backup/files/ | head -10

# Backup sizes
du -sh backup/database/*
du -sh backup/files/*
```

#### Automated Monitoring:
- Backups older than 7 days are automatically deleted
- Failed backups generate error logs
- Web dashboard shows backup status

### Storage Management

#### Disk Usage:
```bash
# Check backup directory size
du -sh backup/

# Check available space (Railway)
df -h /app/

# Check MySQL database size
mysql -e "SELECT table_schema AS 'Database', 
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS 'DB Size in MB' 
    FROM information_schema.tables GROUP BY table_schema;"
```

### Performance Optimization

#### Database Backup Optimization:
- Use `--single-transaction` for InnoDB tables
- Backup during low-traffic periods
- Consider incremental backups for large databases

#### File Backup Optimization:
- Exclude large temporary files
- Use compression for better storage efficiency
- Implement differential backups

---

## ⚙️ Configuration

### Environment Variables

#### Required (Railway):
```env
# Database (auto-configured by Railway MySQL service)
DB_HOST=${{MySQL.MYSQL_HOST}}
DB_PORT=${{MySQL.MYSQL_PORT}} 
DB_NAME=${{MySQL.MYSQL_DATABASE}}
DB_USER=${{MySQL.MYSQL_USER}}
DB_PASS=${{MySQL.MYSQL_PASSWORD}}

# Backup system
BACKUP_PASSWORD=your_secure_password
```

#### Optional:
```env
# Backup retention (days)
BACKUP_RETENTION_DAYS=7

# Backup compression level (1-9)
BACKUP_COMPRESSION_LEVEL=9

# Email notifications (future feature)
BACKUP_EMAIL_NOTIFICATIONS=true
BACKUP_EMAIL_RECIPIENTS=admin@miw.com
```

### Customization

#### Modify Retention Period:
```php
// In database_backup.php, line ~15
$this->retentionDays = 14; // Change from 7 to 14 days
```

#### Add Backup Locations:
```php
// In file_backup.php, add to $this->sourceDir array
$additionalDirs = [
    '/app/config',
    '/app/logs',
    '/app/cache'
];
```

---

## 🔧 Troubleshooting

### Common Issues

#### 1. "Permission Denied" Error
```bash
# Fix file permissions
chmod 755 backup/
chmod 644 backup/*.php
chmod +x backup/*.sh

# Railway: Check volume mount permissions
ls -la /app/uploads
```

#### 2. "Database Connection Failed"
```bash
# Verify Railway environment variables
railway variables

# Test database connection
php -r "require 'config.php'; var_dump(isset($conn));"
```

#### 3. "Backup Directory Not Found"
```bash
# Create backup directories
mkdir -p backup/database backup/files

# Railway: Check volume mounts
df -h | grep app
```

#### 4. "Git Command Failed"
```bash
# Check git configuration
git config --list

# Verify remote connection
git remote -v

# Check branch availability
git branch -a
```

### Debug Mode

Enable debug output:
```bash
# Add to backup scripts
export DEBUG=1
php backup/database_backup.php create
```

View detailed logs:
```bash
# Railway logs
railway logs --tail 100

# Local error logs
tail -f error_logs/php_error.log
```

---

## 📈 Advanced Features

### Automated Backup Scheduling

#### Using Cron (Local):
```bash
# Add to crontab (crontab -e)
# Daily database backup at 2 AM
0 2 * * * cd /path/to/miw && php backup/database_backup.php create

# Weekly file backup on Sunday at 3 AM  
0 3 * * 0 cd /path/to/miw && php backup/file_backup.php create
```

#### Using Railway Cron Jobs:
```bash
# Add to railway.json
{
  "deploy": {
    "startCommand": "./start.sh",
    "healthcheckPath": "/health.php",
    "healthcheckTimeout": 100
  },
  "cron": [
    {
      "schedule": "0 2 * * *",
      "command": "php backup/database_backup.php create"
    }
  ]
}
```

### Remote Backup Storage

#### AWS S3 Integration:
```php
// Add to backup classes
private function uploadToS3($localFile, $s3Key) {
    // AWS S3 SDK implementation
    $s3Client = new S3Client([...]);
    $s3Client->putObject([
        'Bucket' => 'miw-backups',
        'Key' => $s3Key,
        'SourceFile' => $localFile
    ]);
}
```

### Backup Verification

#### Integrity Checks:
```bash
# Verify backup file integrity
gzip -t backup/database/miw_backup_*.sql.gz

# Test database restore (dry run)
php backup/database_backup.php verify [backup_file]
```

### Monitoring Integration

#### Health Checks:
```php
// Add to health.php
$backupHealth = [
    'latest_db_backup' => getLatestBackupTime('database'),
    'latest_file_backup' => getLatestBackupTime('files'),
    'backup_storage_usage' => getBackupStorageUsage()
];
```

---

## 🔒 Security Considerations

### Access Control
- Backup dashboard requires password authentication
- Use strong passwords for backup access
- Limit backup file access to authorized users only

### Data Protection
- Backup files contain sensitive customer data
- Ensure backup storage is encrypted
- Implement proper file permissions (644 for files, 755 for directories)

### Network Security
- Backup operations should occur over secure connections
- Consider VPN access for sensitive backup operations
- Regular security audits of backup procedures

---

## 📞 Support & Maintenance

### Regular Maintenance Tasks

#### Weekly:
- [ ] Verify backup completion
- [ ] Check backup file sizes
- [ ] Test restore procedure
- [ ] Monitor storage usage

#### Monthly:
- [ ] Full system backup test
- [ ] Update backup retention policies
- [ ] Review error logs
- [ ] Performance optimization

#### Quarterly:
- [ ] Disaster recovery drill
- [ ] Security audit
- [ ] Backup system updates
- [ ] Documentation updates

### Getting Help

#### Resources:
- **GitHub Issues:** Report bugs and feature requests
- **Documentation:** This README and inline code comments
- **Railway Support:** For platform-specific issues
- **Community:** MIW developer community

#### Error Reporting:
When reporting issues, include:
- Error messages (full text)
- Environment information (Railway/local)
- Steps to reproduce
- Backup logs and outputs

---

## 📝 Changelog

### Version 1.0.0 (2025-08-02)
- ✅ Initial backup system implementation
- ✅ Database backup with compression
- ✅ File system backup
- ✅ Web dashboard interface
- ✅ Emergency rollback procedures
- ✅ Branch management system
- ✅ CLI tools for automation
- ✅ Cross-platform compatibility

### Planned Features (v1.1.0)
- 🔄 Automated backup scheduling
- 📧 Email notifications
- ☁️ Cloud storage integration (S3, Google Cloud)
- 📊 Backup analytics and reporting
- 🔍 Advanced backup verification
- 📱 Mobile-responsive dashboard

---

## 📄 License & Credits

**MIW Backup System** - Developed for MIW Travel Management System

**Author:** GitHub Copilot Assistant  
**Created:** August 2, 2025  
**Version:** 1.0.0

**Dependencies:**
- PHP 8.1+
- MySQL/MariaDB
- Git
- Railway Platform
- Optional: AWS SDK, ZIP extension

---

## 🎯 Best Practices Summary

### Daily Operations:
1. **Monitor backups** - Check backup dashboard daily
2. **Verify functionality** - Test application after deployments
3. **Review logs** - Check for errors or warnings

### Weekly Operations:
1. **Test restore** - Practice restore procedures
2. **Clean old backups** - Remove unnecessary backup files
3. **Update documentation** - Keep procedures current

### Emergency Response:
1. **Stay calm** - Follow documented procedures
2. **Assess impact** - Understand scope of issues
3. **Execute rollback** - Use appropriate recovery method
4. **Verify recovery** - Test all functionality post-recovery
5. **Document incident** - Record lessons learned

### Security:
1. **Strong passwords** - Use complex backup passwords
2. **Limited access** - Restrict backup system access
3. **Regular audits** - Review backup security regularly
4. **Encrypted storage** - Protect backup data

---

**🎉 Congratulations! Your MIW application now has enterprise-grade backup and recovery capabilities.**

For questions or support, contact the development team or refer to the troubleshooting section above.
