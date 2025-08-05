# Enhanced PlantUML Diagram Export Script
# Exports all main diagrams to PNG format using PlantUML JAR

$sourceDir = "diagrams"
$exportDir = "diagrams_export"
$plantUMLJar = "plantuml.jar"

# Create export directory if it doesn't exist
if (!(Test-Path $exportDir)) {
    New-Item -ItemType Directory -Path $exportDir
    Write-Host "Created export directory: $exportDir" -ForegroundColor Green
}

# Check if PlantUML JAR exists
if (!(Test-Path $plantUMLJar)) {
    Write-Host "PlantUML JAR not found. Please ensure plantuml.jar is in the current directory." -ForegroundColor Red
    exit 1
}

# Main diagrams to export
$mainDiagrams = @(
    "01_class_diagram.puml",
    "02_database_diagram.puml", 
    "03_deployment_diagram.puml",
    "04_sequence_1_registration.puml",
    "05_sequence_2_payment_verification.puml",
    "06_sequence_3_room_management.puml",
    "07_sequence_4_package_management.puml", 
    "08_sequence_5_cancellation_process.puml"
)

Write-Host "=== PlantUML Diagram Export Tool ===" -ForegroundColor Cyan
Write-Host "Source Directory: $sourceDir" -ForegroundColor White
Write-Host "Export Directory: $exportDir" -ForegroundColor White
Write-Host "PlantUML JAR: $plantUMLJar" -ForegroundColor White
Write-Host ""

$successCount = 0
$totalCount = $mainDiagrams.Count

foreach ($diagram in $mainDiagrams) {
    $sourcePath = Join-Path $sourceDir $diagram
    
    if (Test-Path $sourcePath) {
        Write-Host "Processing: $diagram" -ForegroundColor Yellow
        
        try {
            # Export to PNG using PlantUML JAR
            $exportCommand = "java -jar `"$plantUMLJar`" -tpng -o `"../$exportDir`" `"$sourcePath`""
            Invoke-Expression $exportCommand
            
            # Check if export was successful
            $baseName = [System.IO.Path]::GetFileNameWithoutExtension($diagram)
            $expectedOutput = Join-Path $exportDir "$baseName.png"
            
            if (Test-Path $expectedOutput) {
                Write-Host "  Success: $baseName.png" -ForegroundColor Green
                $successCount++
            } else {
                Write-Host "  Failed: $diagram" -ForegroundColor Red
            }
        }
        catch {
            Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Red
        }
    } else {
        Write-Host "  Not found: $sourcePath" -ForegroundColor Red
    }
    
    Write-Host ""
}

Write-Host "=== Export Summary ===" -ForegroundColor Cyan
Write-Host "Total diagrams: $totalCount" -ForegroundColor White
Write-Host "Successfully exported: $successCount" -ForegroundColor Green
Write-Host "Failed: $($totalCount - $successCount)" -ForegroundColor Red

if ($successCount -gt 0) {
    Write-Host ""
    Write-Host "Exported diagrams are available in: $exportDir" -ForegroundColor Cyan
    Write-Host "Opening export directory..." -ForegroundColor White
    Start-Process $exportDir
} else {
    Write-Host ""
    Write-Host "No diagrams were exported successfully." -ForegroundColor Red
}
