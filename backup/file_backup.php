<?php
/**
 * MIW File/Volume Backup System
 * Backs up uploaded files and important application data
 */

require_once __DIR__ . '/../config.php';

class FileBackup {
    private $sourceDir;
    private $backupDir;
    private $excludePatterns;
    
    public function __construct() {
        // Determine source directory based on environment
        if (isProduction()) {
            $this->sourceDir = '/app/uploads';
            $this->backupDir = '/app/backups/files';
        } else {
            $this->sourceDir = __DIR__ . '/../uploads';
            $this->backupDir = __DIR__ . '/files';
        }
        
        $this->excludePatterns = [
            '.git',
            'node_modules',
            'vendor',
            'cache',
            '*.log',
            '.env*'
        ];
        
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
            $backupName = "miw_files_{$timestamp}";
            $backupPath = $this->backupDir . '/' . $backupName;
            
            // Create backup directory
            mkdir($backupPath, 0755, true);
            
            $result = $this->copyDirectory($this->sourceDir, $backupPath);
            
            if ($result['success']) {
                // Create archive
                $archivePath = $backupPath . '.tar.gz';
                $this->createArchive($backupPath, $archivePath);
                
                // Remove uncompressed backup
                $this->removeDirectory($backupPath);
                
                // Clean old backups
                $this->cleanOldBackups();
                
                return [
                    'success' => true,
                    'message' => 'File backup created successfully',
                    'backup_name' => $backupName . '.tar.gz',
                    'path' => $archivePath,
                    'size' => $this->formatBytes(filesize($archivePath)),
                    'files_count' => $result['files_count']
                ];
            } else {
                return $result;
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'File backup error: ' . $e->getMessage()
            ];
        }
    }
    
    private function copyDirectory($source, $destination) {
        if (!is_dir($source)) {
            return [
                'success' => false,
                'message' => 'Source directory does not exist: ' . $source
            ];
        }
        
        $filesCount = 0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            
            if ($item->isDir()) {
                if (!is_dir($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                if (!$this->shouldExclude($item->getPathname())) {
                    $targetDir = dirname($target);
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }
                    copy($item->getPathname(), $target);
                    $filesCount++;
                }
            }
        }
        
        return [
            'success' => true,
            'files_count' => $filesCount
        ];
    }
    
    private function shouldExclude($filepath) {
        foreach ($this->excludePatterns as $pattern) {
            if (fnmatch($pattern, basename($filepath))) {
                return true;
            }
        }
        return false;
    }
    
    private function createArchive($sourceDir, $archivePath) {
        if (class_exists('ZipArchive')) {
            $this->createZipArchive($sourceDir, str_replace('.tar.gz', '.zip', $archivePath));
        } else {
            // Use tar command if available
            $command = sprintf(
                'tar -czf %s -C %s .',
                escapeshellarg($archivePath),
                escapeshellarg($sourceDir)
            );
            exec($command);
        }
    }
    
    private function createZipArchive($sourceDir, $zipPath) {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $relativePath = substr($file->getPathname(), strlen($sourceDir) + 1);
                    $zip->addFile($file->getPathname(), $relativePath);
                }
            }
            $zip->close();
        }
    }
    
    private function removeDirectory($dir) {
        if (is_dir($dir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            
            foreach ($iterator as $file) {
                if ($file->isDir()) {
                    rmdir($file->getPathname());
                } else {
                    unlink($file->getPathname());
                }
            }
            rmdir($dir);
        }
    }
    
    private function cleanOldBackups($retentionDays = 7) {
        $files = glob($this->backupDir . '/miw_files_*.{tar.gz,zip}', GLOB_BRACE);
        $cutoff = time() - ($retentionDays * 24 * 60 * 60);
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
            }
        }
    }
    
    public function listBackups() {
        $files = glob($this->backupDir . '/miw_files_*.{tar.gz,zip}', GLOB_BRACE);
        $backups = [];
        
        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'path' => $file,
                'size' => $this->formatBytes(filesize($file)),
                'created' => date('Y-m-d H:i:s', filemtime($file))
            ];
        }
        
        usort($backups, function($a, $b) {
            return strcmp($b['created'], $a['created']);
        });
        
        return $backups;
    }
    
    public function restoreBackup($filename) {
        $backupPath = $this->backupDir . '/' . $filename;
        
        if (!file_exists($backupPath)) {
            return [
                'success' => false,
                'message' => 'Backup file not found'
            ];
        }
        
        try {
            // Create temporary extraction directory
            $tempDir = $this->backupDir . '/temp_restore_' . time();
            mkdir($tempDir, 0755, true);
            
            // Extract archive
            if (pathinfo($filename, PATHINFO_EXTENSION) === 'zip') {
                $this->extractZipArchive($backupPath, $tempDir);
            } else {
                $command = sprintf(
                    'tar -xzf %s -C %s',
                    escapeshellarg($backupPath),
                    escapeshellarg($tempDir)
                );
                exec($command);
            }
            
            // Backup current files
            $currentBackup = $this->sourceDir . '_backup_' . time();
            if (is_dir($this->sourceDir)) {
                rename($this->sourceDir, $currentBackup);
            }
            
            // Restore files
            rename($tempDir, $this->sourceDir);
            
            return [
                'success' => true,
                'message' => 'Files restored successfully from ' . $filename,
                'previous_backup' => $currentBackup
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'File restore error: ' . $e->getMessage()
            ];
        }
    }
    
    private function extractZipArchive($zipPath, $extractTo) {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($extractTo);
            $zip->close();
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
    $backup = new FileBackup();
    
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
                    echo "Usage: php file_backup.php restore <filename>\n";
                }
                break;
                
            default:
                echo "Usage: php file_backup.php [create|list|restore]\n";
        }
    } else {
        echo "Usage: php file_backup.php [create|list|restore]\n";
    }
}
?>
