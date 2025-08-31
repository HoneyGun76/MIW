<?php
/**
 * MIW Backup Management Dashboard
 * Web interface for managing database and file backups
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/database_backup.php';
require_once __DIR__ . '/file_backup.php';

// Authentication check
session_start();
if (!isset($_SESSION['backup_authenticated']) || $_SESSION['backup_authenticated'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        // Simple password protection (use environment variable in production)
        $correctPassword = isProduction() ? getenv('BACKUP_PASSWORD') : 'backup123';
        if ($_POST['password'] === $correctPassword) {
            $_SESSION['backup_authenticated'] = true;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error = 'Invalid password';
        }
    }
    
    // Show login form
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>MIW Backup Management - Login</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .login-form { max-width: 300px; margin: 100px auto; padding: 20px; border: 1px solid #ddd; }
            input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; }
            button { width: 100%; padding: 10px; background: #007cba; color: white; border: none; }
            .error { color: red; margin: 10px 0; }
        </style>
    </head>
    <body>
        <div class="login-form">
            <h2>Backup Management Access</h2>
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Enter backup password" required>
                <button type="submit">Access Backup System</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'create_db_backup':
            $backup = new DatabaseBackup();
            echo json_encode($backup->createBackup());
            exit;
            
        case 'create_file_backup':
            $backup = new FileBackup();
            echo json_encode($backup->createBackup());
            exit;
            
        case 'list_db_backups':
            $backup = new DatabaseBackup();
            echo json_encode($backup->listBackups());
            exit;
            
        case 'list_file_backups':
            $backup = new FileBackup();
            echo json_encode($backup->listBackups());
            exit;
            
        case 'restore_db_backup':
            if (isset($_POST['filename'])) {
                $backup = new DatabaseBackup();
                echo json_encode($backup->restoreBackup($_POST['filename']));
            } else {
                echo json_encode(['success' => false, 'message' => 'Filename required']);
            }
            exit;
            
        case 'restore_file_backup':
            if (isset($_POST['filename'])) {
                $backup = new FileBackup();
                echo json_encode($backup->restoreBackup($_POST['filename']));
            } else {
                echo json_encode(['success' => false, 'message' => 'Filename required']);
            }
            exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIW Backup Management Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .header h1 { margin-bottom: 10px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .section { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .section h2 { color: #333; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #eee; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007cba; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-danger { background: #dc3545; color: white; }
        .btn:hover { opacity: 0.9; }
        .backup-list { margin-top: 20px; }
        .backup-item { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #007cba; }
        .backup-item h4 { color: #333; margin-bottom: 5px; }
        .backup-meta { color: #666; font-size: 0.9em; margin-bottom: 10px; }
        .status { padding: 20px; border-radius: 5px; margin: 10px 0; }
        .status.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status.info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .loading { text-align: center; padding: 20px; }
        .environment-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .tools-section { grid-column: span 2; }
        .tools-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; }
        .tool-card { background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #dee2e6; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛡️ MIW Backup Management Dashboard</h1>
            <p>Environment: <strong><?= getCurrentEnvironment() ?></strong> | Last updated: <?= date('Y-m-d H:i:s') ?></p>
        </div>

        <div class="environment-info">
            <strong>Current Configuration:</strong>
            Environment: <?= getCurrentEnvironment() ?> | 
            Database: <?= $db_config['type'] ?> | 
            Upload Directory: <?= getUploadDirectory() ?>
        </div>

        <div id="status-area"></div>

        <div class="grid">
            <!-- Database Backup Section -->
            <div class="section">
                <h2>📊 Database Backups</h2>
                <div>
                    <button class="btn btn-primary" onclick="createDatabaseBackup()">Create Database Backup</button>
                    <button class="btn btn-success" onclick="loadDatabaseBackups()">Refresh List</button>
                </div>
                <div id="db-backup-list" class="backup-list">
                    <div class="loading">Loading database backups...</div>
                </div>
            </div>

            <!-- File Backup Section -->
            <div class="section">
                <h2>📁 File Backups</h2>
                <div>
                    <button class="btn btn-primary" onclick="createFileBackup()">Create File Backup</button>
                    <button class="btn btn-success" onclick="loadFileBackups()">Refresh List</button>
                </div>
                <div id="file-backup-list" class="backup-list">
                    <div class="loading">Loading file backups...</div>
                </div>
            </div>
        </div>

        <!-- Tools Section -->
        <div class="section tools-section">
            <h2>🔧 Backup Tools & Actions</h2>
            <div class="tools-grid">
                <div class="tool-card">
                    <h4>🔄 Full System Backup</h4>
                    <p>Create both database and file backups simultaneously</p>
                    <button class="btn btn-primary" onclick="createFullBackup()">Create Full Backup</button>
                </div>
                
                <div class="tool-card">
                    <h4>🗂️ Backup Information</h4>
                    <p>View backup storage usage and statistics</p>
                    <button class="btn btn-success" onclick="showBackupInfo()">View Info</button>
                </div>
                
                <div class="tool-card">
                    <h4>⚠️ Emergency Actions</h4>
                    <p>Quick recovery and emergency procedures</p>
                    <button class="btn btn-warning" onclick="showEmergencyOptions()">Emergency Menu</button>
                </div>
                
                <div class="tool-card">
                    <h4>🔗 Railway Integration</h4>
                    <p>Backup deployment and branch management</p>
                    <button class="btn btn-primary" onclick="showRailwayOptions()">Railway Tools</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-load backups on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadDatabaseBackups();
            loadFileBackups();
        });

        function createDatabaseBackup() {
            showStatus('Creating database backup...', 'info');
            fetch('?action=create_db_backup')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showStatus('Database backup created: ' + data.filename + ' (' + data.size + ')', 'success');
                        loadDatabaseBackups();
                    } else {
                        showStatus('Database backup failed: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showStatus('Error creating database backup: ' + error.message, 'error');
                });
        }

        function createFileBackup() {
            showStatus('Creating file backup...', 'info');
            fetch('?action=create_file_backup')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showStatus('File backup created: ' + data.backup_name + ' (' + data.size + ', ' + data.files_count + ' files)', 'success');
                        loadFileBackups();
                    } else {
                        showStatus('File backup failed: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showStatus('Error creating file backup: ' + error.message, 'error');
                });
        }

        function createFullBackup() {
            showStatus('Creating full system backup...', 'info');
            Promise.all([
                fetch('?action=create_db_backup').then(r => r.json()),
                fetch('?action=create_file_backup').then(r => r.json())
            ]).then(results => {
                const [dbResult, fileResult] = results;
                let messages = [];
                
                if (dbResult.success) {
                    messages.push('Database: ' + dbResult.filename);
                } else {
                    messages.push('Database failed: ' + dbResult.message);
                }
                
                if (fileResult.success) {
                    messages.push('Files: ' + fileResult.backup_name);
                } else {
                    messages.push('Files failed: ' + fileResult.message);
                }
                
                const allSuccess = dbResult.success && fileResult.success;
                showStatus('Full backup completed. ' + messages.join('. '), allSuccess ? 'success' : 'error');
                
                if (allSuccess) {
                    loadDatabaseBackups();
                    loadFileBackups();
                }
            });
        }

        function loadDatabaseBackups() {
            fetch('?action=list_db_backups')
                .then(response => response.json())
                .then(backups => {
                    const container = document.getElementById('db-backup-list');
                    if (backups.length === 0) {
                        container.innerHTML = '<p>No database backups found.</p>';
                        return;
                    }
                    
                    container.innerHTML = backups.map(backup => `
                        <div class="backup-item">
                            <h4>${backup.filename}</h4>
                            <div class="backup-meta">Size: ${backup.size} | Created: ${backup.created}</div>
                            <button class="btn btn-warning" onclick="restoreDatabase('${backup.filename}')">Restore</button>
                        </div>
                    `).join('');
                });
        }

        function loadFileBackups() {
            fetch('?action=list_file_backups')
                .then(response => response.json())
                .then(backups => {
                    const container = document.getElementById('file-backup-list');
                    if (backups.length === 0) {
                        container.innerHTML = '<p>No file backups found.</p>';
                        return;
                    }
                    
                    container.innerHTML = backups.map(backup => `
                        <div class="backup-item">
                            <h4>${backup.filename}</h4>
                            <div class="backup-meta">Size: ${backup.size} | Created: ${backup.created}</div>
                            <button class="btn btn-warning" onclick="restoreFiles('${backup.filename}')">Restore</button>
                        </div>
                    `).join('');
                });
        }

        function restoreDatabase(filename) {
            if (!confirm('Are you sure you want to restore the database from ' + filename + '? This will overwrite all current data!')) {
                return;
            }
            
            showStatus('Restoring database from ' + filename + '...', 'info');
            
            const formData = new FormData();
            formData.append('filename', filename);
            
            fetch('?action=restore_db_backup', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatus('Database restored successfully from ' + filename, 'success');
                } else {
                    showStatus('Database restore failed: ' + data.message, 'error');
                }
            });
        }

        function restoreFiles(filename) {
            if (!confirm('Are you sure you want to restore files from ' + filename + '? This will overwrite current uploaded files!')) {
                return;
            }
            
            showStatus('Restoring files from ' + filename + '...', 'info');
            
            const formData = new FormData();
            formData.append('filename', filename);
            
            fetch('?action=restore_file_backup', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatus('Files restored successfully from ' + filename, 'success');
                } else {
                    showStatus('File restore failed: ' + data.message, 'error');
                }
            });
        }

        function showStatus(message, type) {
            const statusArea = document.getElementById('status-area');
            statusArea.innerHTML = `<div class="status ${type}">${message}</div>`;
            setTimeout(() => {
                statusArea.innerHTML = '';
            }, 5000);
        }

        function showBackupInfo() {
            alert('Backup information feature coming soon!');
        }

        function showEmergencyOptions() {
            alert('Emergency recovery options coming soon!');
        }

        function showRailwayOptions() {
            alert('Railway integration tools coming soon!');
        }
    </script>
</body>
</html>
