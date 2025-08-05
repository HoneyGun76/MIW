# Export PlantUML Diagrams to JPG
# This script exports all main PlantUML diagrams to JPG format

$sourceDir = "diagrams"
$exportDir = "diagrams_export"

# Create export directory if it doesn't exist
if (!(Test-Path $exportDir)) {
    New-Item -ItemType Directory -Path $exportDir
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

Write-Host "Starting PlantUML diagram export..." -ForegroundColor Green

foreach ($diagram in $mainDiagrams) {
    $sourcePath = Join-Path $sourceDir $diagram
    if (Test-Path $sourcePath) {
        $baseName = [System.IO.Path]::GetFileNameWithoutExtension($diagram)
        $outputPath = Join-Path $exportDir "$baseName.jpg"
        
        Write-Host "Exporting $diagram..." -ForegroundColor Yellow
        
        # Use PlantUML JAR for export (assuming it's available via VS Code extension)
        try {
            # Method 1: Try VS Code PlantUML extension export
            code --command "plantuml.exportDocument" "$sourcePath"
            Start-Sleep -Seconds 2
            
            # Check if image was created in source directory and move it
            $generatedImg = Join-Path $sourceDir "$baseName.png"
            if (Test-Path $generatedImg) {
                Move-Item $generatedImg $outputPath.Replace('.jpg', '.png')
                Write-Host "✓ Exported: $baseName.png" -ForegroundColor Green
            }
        }
        catch {
            Write-Host "✗ Failed to export: $diagram" -ForegroundColor Red
        }
    }
    else {
        Write-Host "✗ File not found: $sourcePath" -ForegroundColor Red
    }
}

Write-Host "Export process completed!" -ForegroundColor Green
Write-Host "Check the '$exportDir' directory for exported images." -ForegroundColor Cyan
