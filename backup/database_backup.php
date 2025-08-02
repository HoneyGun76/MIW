<?php
/**
 * MIW Database Backup System
 * Automated database backup with compression and retention
 */

require_once __DIR__ . '/../config.php';

class DatabaseBackup {
    private $conn;
    private $backupDir;
    private $retentionDays;
    
    public function __construct($retentionDays = 7) {
        global $conn;
        $this->conn = $conn;
        $this->retentionDays = $retentionDays;
        
        // Set backup directory based on environment
        if (isProduction()) {
            $this->backupDir = '/app/backups/database';
        } else {
            $this->backupDir = __DIR__ . '/database';
        }
        
        $this->ensureBackupDirectory();
    }
    
    private function ensureBackupDirectory() {
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }
    
    public function createBackup() {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "miw_backup_{$timestamp}.sql";
            $filepath = $this->backupDir . '/' . $filename;
            
            // Get database configuration
            global $db_config;
            
            $output = [];
            $result_code = 0;
            
            if (isProduction()) {
                // Railway environment - use environment variables
                $command = sprintf(
                    'mysqldump -h %s -P %s -u %s -p%s %s > %s 2>&1',
                    escapeshellarg($db_config['host']),
                    escapeshellarg($db_config['port']),
                    escapeshellarg($db_config['username']),
                    escapeshellarg($db_config['password']),
                    escapeshellarg($db_config['database']),
                    escapeshellarg($filepath)
                );
            } else {
                // Local environment
                $command = sprintf(
                    'mysqldump -h %s -u %s %s %s > %s 2>&1',
                    escapeshellarg($db_config['host']),
                    escapeshellarg($db_config['username']),
                    $db_config['password'] ? '-p' . escapeshellarg($db_config['password']) : '',
                    escapeshellarg($db_config['database']),
                    escapeshellarg($filepath)
                );
            }
            
            exec($command, $output, $result_code);
            
            if ($result_code === 0 && file_exists($filepath)) {
                // Compress the backup
                $this->compressBackup($filepath);
                
                // Clean old backups
                $this->cleanOldBackups();
                
                return [
                    'success' => true,
                    'message' => 'Database backup created successfully',
                    'filename' => $filename . '.gz',
                    'path' => $filepath . '.gz',
                    'size' => $this->formatBytes(filesize($filepath . '.gz'))
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Database backup failed: ' . implode('\n', $output),
                    'error_code' => $result_code
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Database backup error: ' . $e->getMessage()
            ];
        }
    }
    
    private function compressBackup($filepath) {
        if (function_exists('gzencode') && file_exists($filepath)) {
            $data = file_get_contents($filepath);
            $compressed = gzencode($data, 9);
            file_put_contents($filepath . '.gz', $compressed);
            unlink($filepath); // Remove uncompressed version
        }
    }
    
    private function cleanOldBackups() {
        $files = glob($this->backupDir . '/miw_backup_*.sql.gz');
        $cutoff = time() - ($this->retentionDays * 24 * 60 * 60);
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
            }
        }
    }
    
    public function listBackups() {
        $files = glob($this->backupDir . '/miw_backup_*.sql.gz');
        $backups = [];
        
        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'path' => $file,
                'size' => $this->formatBytes(filesize($file)),
                'created' => date('Y-m-d H:i:s', filemtime($file))
            ];
        }
        
        // Sort by creation time, newest first
        usort($backups, function($a, $b) {
            return strcmp($b['created'], $a['created']);
        });
        
        return $backups;
    }
    
    public function restoreBackup($filename) {
        $filepath = $this->backupDir . '/' . $filename;
        
        if (!file_exists($filepath)) {
            return [
                'success' => false,
                'message' => 'Backup file not found'
            ];
        }
        
        try {
            global $db_config;
            
            // Decompress if needed
            $sqlFile = $filepath;
            if (pathinfo($filepath, PATHINFO_EXTENSION) === 'gz') {
                $sqlFile = str_replace('.gz', '', $filepath);
                $compressed = file_get_contents($filepath);
                $decompressed = gzdecode($compressed);
                file_put_contents($sqlFile, $decompressed);
            }
            
            $output = [];
            $result_code = 0;
            
            if (isProduction()) {
                $command = sprintf(
                    'mysql -h %s -P %s -u %s -p%s %s < %s 2>&1',
                    escapeshellarg($db_config['host']),
                    escapeshellarg($db_config['port']),
                    escapeshellarg($db_config['username']),
                    escapeshellarg($db_config['password']),
                    escapeshellarg($db_config['database']),
                    escapeshellarg($sqlFile)
                );
            } else {
                $command = sprintf(
                    'mysql -h %s -u %s %s %s < %s 2>&1',
                    escapeshellarg($db_config['host']),
                    escapeshellarg($db_config['username']),
                    $db_config['password'] ? '-p' . escapeshellarg($db_config['password']) : '',
                    escapeshellarg($db_config['database']),
                    escapeshellarg($sqlFile)
                );
            }
            
            exec($command, $output, $result_code);
            
            // Clean up decompressed file if it was created
            if ($sqlFile !== $filepath && file_exists($sqlFile)) {
                unlink($sqlFile);
            }
            
            if ($result_code === 0) {
                return [
                    'success' => true,
                    'message' => 'Database restored successfully from ' . $filename
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Database restore failed: ' . implode('\n', $output),
                    'error_code' => $result_code
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Database restore error: ' . $e->getMessage()
            ];
        }
    }
    
    private function formatBytes($size, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . ' ' . $units[$i];
    }
}

// CLI usage
if (php_sapi_name() === 'cli') {
    $backup = new DatabaseBackup();
    
    if ($argc > 1) {
        switch ($argv[1]) {
            case 'create':
                $result = $backup->createBackup();
                echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
                break;
                
            case 'list':
                $backups = $backup->listBackups();
                echo json_encode($backups, JSON_PRETTY_PRINT) . "\n";
                break;
                
            case 'restore':
                if ($argc > 2) {
                    $result = $backup->restoreBackup($argv[2]);
                    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
                } else {
                    echo "Usage: php database_backup.php restore <filename>\n";
                }
                break;
                
            default:
                echo "Usage: php database_backup.php [create|list|restore]\n";
        }
    } else {
        echo "Usage: php database_backup.php [create|list|restore]\n";
    }
}
?>
