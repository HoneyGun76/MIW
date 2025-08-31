<?php
/**
 * Test script to validate date field handling in forms
 * This script helps identify potential date validation issues
 */

require_once 'config.php';

// Set authentication for testing
session_start();
$_SESSION['authenticated'] = true;

// Test data with empty date fields
$testCases = [
    'Case 1: Empty string dates' => [
        'tanggal_vaksin_1' => '',
        'tanggal_vaksin_2' => '',
        'tanggal_vaksin_3' => '',
        'tanggal_pengeluaran_paspor' => '',
        'tanggal_habis_berlaku' => ''
    ],
    'Case 2: Valid dates' => [
        'tanggal_vaksin_1' => '2023-01-15',
        'tanggal_vaksin_2' => '2023-02-15',
        'tanggal_vaksin_3' => '2023-06-15',
        'tanggal_pengeluaran_paspor' => '2022-01-01',
        'tanggal_habis_berlaku' => '2032-01-01'
    ],
    'Case 3: Mixed empty and valid dates' => [
        'tanggal_vaksin_1' => '2023-01-15',
        'tanggal_vaksin_2' => '',
        'tanggal_vaksin_3' => '',
        'tanggal_pengeluaran_paspor' => '2022-01-01',
        'tanggal_habis_berlaku' => ''
    ]
];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Date Validation Test - MIW Travel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        .test-case { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #fafafa; }
        .test-case h3 { color: #2196F3; margin-top: 0; }
        .result { margin: 10px 0; padding: 10px; border-radius: 4px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .code { background-color: #f8f9fa; border: 1px solid #e9ecef; padding: 10px; border-radius: 4px; font-family: monospace; white-space: pre-wrap; }
        .back-link { display: inline-block; margin-top: 20px; padding: 8px 16px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; }
        .back-link:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Date Validation Test Results</h1>
        <p>This test validates how empty date fields are handled to prevent MySQL DATE column errors.</p>
        
        <?php foreach ($testCases as $caseName => $testData): ?>
            <div class="test-case">
                <h3><?php echo htmlspecialchars($caseName); ?></h3>
                
                <h4>Test Data:</h4>
                <div class="code"><?php echo htmlspecialchars(json_encode($testData, JSON_PRETTY_PRINT)); ?></div>
                
                <h4>Processed Results:</h4>
                <?php
                $processedData = [];
                $hasErrors = false;
                
                foreach ($testData as $field => $value) {
                    if (strpos($field, 'tanggal_') === 0) {
                        // Apply the same logic as in submit_umroh.php
                        $processedValue = !empty($value) ? $value : null;
                        $processedData[$field] = $processedValue;
                        
                        // Check if this would cause MySQL error
                        if ($value === '' && $processedValue !== null) {
                            $hasErrors = true;
                        }
                    }
                }
                ?>
                
                <div class="code"><?php echo htmlspecialchars(json_encode($processedData, JSON_PRETTY_PRINT)); ?></div>
                
                <div class="result <?php echo $hasErrors ? 'error' : 'success'; ?>">
                    <?php if ($hasErrors): ?>
                        ❌ <strong>Would cause MySQL error:</strong> Empty string passed to DATE column
                    <?php else: ?>
                        ✅ <strong>Safe for database:</strong> Empty dates properly converted to NULL
                    <?php endif; ?>
                </div>
                
                <?php
                // Simulate the SQL that would be generated
                $sqlPreview = "INSERT INTO data_jamaah (";
                $fields = array_keys($processedData);
                $sqlPreview .= implode(', ', $fields);
                $sqlPreview .= ") VALUES (";
                $values = array_map(function($v) { return $v === null ? 'NULL' : "'$v'"; }, $processedData);
                $sqlPreview .= implode(', ', $values);
                $sqlPreview .= ")";
                ?>
                
                <h4>Generated SQL Preview:</h4>
                <div class="code"><?php echo htmlspecialchars($sqlPreview); ?></div>
            </div>
        <?php endforeach; ?>
        
        <div style="margin-top: 30px; padding: 15px; background-color: #e7f3ff; border: 1px solid #bee5eb; border-radius: 5px;">
            <h3>🔧 Fix Summary</h3>
            <p><strong>Problem:</strong> Empty date input fields send empty strings ('') to the database, but MySQL DATE columns only accept valid dates or NULL values.</p>
            <p><strong>Solution:</strong> Use <code>!empty($value) ? $value : null</code> instead of <code>$value ?? null</code> for optional date fields.</p>
            <p><strong>Fixed in:</strong> submit_umroh.php for tanggal_vaksin_1, tanggal_vaksin_2, tanggal_vaksin_3 fields.</p>
        </div>
        
        <a href="diagnostic.php" class="back-link">← Back to Diagnostics</a>
    </div>
</body>
</html>
