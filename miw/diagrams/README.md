# UML DIAGRAMS - SISTEM MIW TRAVEL
=====================================================

Direktori ini berisi diagram UML untuk sistem manajemen travel umroh dan haji MIW. Semua diagram menggunakan standar UML 2.5 dengan styling hitam-putih untuk keperluan akademis.

## Struktur Direktori

### 01_class_diagrams/
Diagram kelas yang menggambarkan struktur objek dan relasi dalam sistem:
- `main_class_diagram.puml` - Diagram kelas lengkap dengan relasi
- `classes_only.puml` - Struktur internal kelas tanpa relasi
- `relationships_only.puml` - Hanya menampilkan relasi antar kelas

### 02_sequence_diagrams/
Diagram sekuensial yang menggambarkan interaksi antar komponen:
- `01_registration_process.puml` - Proses pendaftaran jamaah
- `02_payment_verification.puml` - Verifikasi pembayaran
- `03_package_management.puml` - Pengelolaan paket travel
- `04_manifest_export.puml` - Export data manifest
- `05_cancellation_process.puml` - Proses pembatalan booking
- `06_basic_registration.puml` - Registrasi sederhana
- `07_basic_manifest.puml` - Manifest sederhana

### 03_database_diagrams/
Diagram database dan skema data:
- `main_database_diagram.puml` - Skema lengkap database

### 04_deployment_diagrams/
Diagram deployment dan arsitektur infrastruktur:
- `main_deployment_diagram.puml` - Arsitektur deployment Railway.com

### 05_use_case_diagrams/
Diagram use case yang menggambarkan fungsionalitas sistem:
- `main_use_case_diagram.puml` - Use case lengkap sistem

### 06_activity_diagrams/
Diagram aktivitas yang menggambarkan workflow bisnis:
- `register_activity.puml` - Alur registrasi
- `payment_activity.puml` - Alur pembayaran
- `manage_packages_activity.puml` - Alur pengelolaan paket
- `upload_documents_activity.puml` - Alur upload dokumen
- `cancellation_activity.puml` - Alur pembatalan

### diagrams_export/
Direktori berisi hasil export diagram dalam format PNG untuk presentasi dan dokumentasi.

## File Pendukung

- `uml_black_white_style.puml` - File styling hitam-putih standar UML 2.5
- `description.txt` - Deskripsi detail setiap diagram
- `index.html` - Gallery web untuk preview diagram
- `README.md` - Dokumentasi ini

## Current Status
- ✅ Directory structure organized
- ✅ Files properly named and categorized  
- ✅ Styling standardized across all diagrams
- ✅ Include paths verified and working
- ✅ Description.txt created with comprehensive explanations
- ✅ Documentation complete

## Penggunaan

### Generating Diagrams
Untuk menggenerate diagram PNG dari file .puml:
```bash
# Contoh untuk class diagram
java -jar plantuml.jar -tpng 01_class_diagrams/main_class_diagram.puml -o diagrams_export/

# Generate semua diagram sekaligus
java -jar plantuml.jar -tpng **/*.puml -o diagrams_export/
```

### Style Guide
Semua diagram menggunakan file `uml_black_white_style.puml` yang memastikan:
- Tampilan hitam-putih untuk printing
- Font konsisten (Arial, 10pt)
- Layout yang bersih tanpa shadow/gradient
- Sesuai standar UML 2.5

### Include Path
Diagram dalam subdirectory menggunakan relative path:
```plantuml
!include ../uml_black_white_style.puml
```

## Informasi Teknis

- **Format**: PlantUML (.puml)
- **Export**: PNG format
- **Styling**: UML 2.5 Black & White Standard
- **Encoding**: UTF-8
- **Kompatibilitas**: PlantUML 1.2021.0+

## Referensi
- UML 2.5 Standard: https://www.uml-diagrams.org/
- PlantUML Documentation: https://plantuml.com/
- Academic UML Guidelines: ISO/IEC 19505-2:2012
