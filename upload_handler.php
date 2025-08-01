<?php
// upload_handler.php

class UploadHandler {
    private $uploadBaseDir;
    private $allowedTypes;
    private $maxSize;
    private $errors = [];

    public function __construct($uploadBaseDir = 'uploads') {
        $this->uploadBaseDir = rtrim($_SERVER['DOCUMENT_ROOT'] . '/MIW/' . $uploadBaseDir, '/');
        $this->allowedTypes = [
            'image/jpeg',
            'image/png',
            'application/pdf'
        ];
        $this->maxSize = 2 * 1024 * 1024; // 2MB
    }

    /**
     * Handle file upload with custom naming
     * @param array $file $_FILES array element
     * @param string $targetDir Subdirectory under uploads/
     * @param string $customName Custom name for the file (without extension)
     * @return array|false Returns array with file info or false on failure
     */
    public function handleUpload($file, $targetDir, $customName) {
        $this->errors = [];

        // Basic validation
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = "File upload failed";
            return false;
        }

        // File type validation
        if (!in_array($file['type'], $this->allowedTypes)) {
            $this->errors[] = "Invalid file type. Allowed types: JPG, PNG, PDF";
            return false;
        }

        // Size validation
        if ($file['size'] > $this->maxSize) {
            $this->errors[] = "File size exceeds limit (2MB)";
            return false;
        }

        // Create target directory if it doesn't exist
        $targetPath = $this->uploadBaseDir . '/' . trim($targetDir, '/');
        if (!file_exists($targetPath)) {
            if (!mkdir($targetPath, 0755, true)) {
                $this->errors[] = "Failed to create upload directory";
                return false;
            }
        }

        // Get file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Build final filename
        $filename = $customName . '.' . $extension;
        $finalPath = $targetPath . '/' . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $finalPath)) {
            $this->errors[] = "Failed to move uploaded file";
            return false;
        }

        return [
            'filename' => $filename,
            'path' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $finalPath),
            'fullPath' => $finalPath,
            'type' => $file['type'],
            'size' => $file['size']
        ];
    }

    /**
     * Get upload errors if any
     * @return array Array of error messages
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Generate custom filename for uploads
     * @param string $nik NIK of the registrant
     * @param string $documentType Type of document (kk, ktp, etc)
     * @param string|null $pakId Optional package ID
     * @return string Generated filename without extension
     */
    public function generateCustomFilename($nik, $documentType, $pakId = null) {
        $parts = [];
        
        if ($pakId) {
            $parts[] = $pakId;
        }
        
        $parts[] = $nik;
        $parts[] = $documentType;
        $parts[] = date('YmdHis');
        
        return implode('_', $parts);
    }

    /**
     * Check if file exists in upload directory
     * @param string $filepath Relative path to file
     * @return bool True if file exists
     */
    public function fileExists($filepath) {
        $fullPath = $this->uploadBaseDir . '/' . ltrim($filepath, '/');
        return file_exists($fullPath);
    }

    /**
     * Get full server path for a relative upload path
     * @param string $filepath Relative path to file
     * @return string Full server path
     */
    public function getFullPath($filepath) {
        return $this->uploadBaseDir . '/' . ltrim($filepath, '/');
    }
}
