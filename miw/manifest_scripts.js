// Manifest Export Script
// This script handles the export of manifest data to Excel format

// Wait for document ready
jQuery(document).ready(function($) {
    // Export to Excel functions
    $(document).on('click', '.export-manifest', function() {
        const pakId = $(this).data('pakid');
        console.log('Exporting manifest for package:', pakId);
        if (!pakId) {
            alert('No package ID found for export');
            return;
        }
        exportToExcelAjax(pakId, 'manifest');
    });

    function exportToExcel(pakId, type, data) {
        console.log("=== COMBINED MANIFEST & ROOMLIST & KELENGKAPAN EXPORT START ===");
        console.log("Package ID:", pakId);
        console.log("Type:", type);
        console.log("Data received:", data);
        console.log("Number of jamaah records:", data.manifest ? data.manifest.length : 0);
        console.log("Roomlist data available:", data.roomLists ? 'Yes' : 'No');
        console.log("Kelengkapan data available:", data.kelengkapan ? data.kelengkapan.length : 0, "records");
        
        // Create a new workbook - no template, just build from data
        const wb = XLSX.utils.book_new();
        
        if (type === 'manifest' && data.manifest && data.manifest.length > 0) {
            console.log("Processing manifest data for combined export (with kelengkapan)");
            
            // Create header information based on manifest_template.xlsx reference (hotel info removed)
            const headerInfo = [
                [`Manifest Jamaah ${data.package.name || 'Unknown Package'}`],
                [`Tanggal ${data.package.departure_date || 'Unknown Date'}`], 
                [`Program ${data.package.type || 'Unknown Type'}`],
                [''],
                ['No', 'Sex', 'Name of Passport', 'Marketing', 'Nama Ayah', 'Birth: Date', 'Birth: City',
                 'Passport: No.Passport', 'Passport: Issuing Office', 'Passport: Date of Issue',
                 'Passport: Date of Expiry', 'Relation', 'Age', 'Cabang', 'Roomlist', 'NIK', 'Alamat', 'Keterangan']
            ];
            
            console.log("Header info created:", headerInfo);
            
            // Convert manifest data to array format - ensure no empty values
            const manifestRows = data.manifest.map((row, index) => {
                console.log(`Processing jamaah ${index + 1}:`, row);
                
                const processedRow = [
                    row['No'] || (index + 1),
                    row['Sex'] || '',
                    row['Name of Passport'] || '',
                    row['Marketing'] || '',
                    row['Nama Ayah'] || '',
                    row['Birth: Date'] || '',
                    row['Birth: City'] || '',
                    row['Passport: No.Passport'] || '',
                    row['Passport: Issuing Office'] || '',
                    row['Passport: Date of Issue'] || '',
                    row['Passport: Date of Expiry'] || '',
                    row['Relation'] || '',
                    row['Age'] || '',
                    row['Cabang'] || '',
                    row['Roomlist'] || '',
                    row['NIK'] || '',
                    row['Alamat'] || '',
                    row['Keterangan'] || ''
                ];
                
                console.log(`Processed row ${index + 1}:`, processedRow);
                return processedRow;
            });
            
            console.log("All manifest rows processed:", manifestRows);
            
            // Combine header and data
            const allData = [...headerInfo, ...manifestRows];
            console.log("Final Excel data structure:", allData);
            
            // Create worksheet from array of arrays
            const wsManifest = XLSX.utils.aoa_to_sheet(allData);
            
            // Apply styling similar to manifest template
            if (!wsManifest['!merges']) wsManifest['!merges'] = [];
            
            // Merge cells for title row (A1:R1)
            wsManifest['!merges'].push({s: {r: 0, c: 0}, e: {r: 0, c: 17}});
            // Merge cells for date row (A2:R2)
            wsManifest['!merges'].push({s: {r: 1, c: 0}, e: {r: 1, c: 17}});
            // Merge cells for program row (A3:R3)
            wsManifest['!merges'].push({s: {r: 2, c: 0}, e: {r: 2, c: 17}});
            
            // Apply cell styles with enhanced formatting
            const headerRowIndex = 3; // Row with column headers (0-indexed)
            const headerCells = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R'];
            
            // Style for title rows with enhanced formatting
            ['A1', 'A2', 'A3'].forEach((cellRef, index) => {
                if (!wsManifest[cellRef]) wsManifest[cellRef] = {v: '', t: 's'};
                wsManifest[cellRef].s = {
                    font: {bold: true, sz: 16, color: {rgb: '000000'}},
                    alignment: {horizontal: 'center', vertical: 'center'},
                    fill: {patternType: 'solid', fgColor: {rgb: 'F2F2F2'}},
                    border: {
                        top: {style: 'medium', color: {rgb: '000000'}},
                        bottom: {style: 'medium', color: {rgb: '000000'}},
                        left: {style: 'medium', color: {rgb: '000000'}},
                        right: {style: 'medium', color: {rgb: '000000'}}
                    }
                };
            });
            
            // Style for header row with enhanced formatting
            headerCells.forEach(col => {
                const cellRef = col + (headerRowIndex + 1);
                if (!wsManifest[cellRef]) wsManifest[cellRef] = {v: '', t: 's'};
                wsManifest[cellRef].s = {
                    font: {bold: true, sz: 11, color: {rgb: '000000'}},
                    alignment: {horizontal: 'center', vertical: 'center', wrapText: true},
                    fill: {patternType: 'solid', fgColor: {rgb: 'D9D9D9'}},
                    border: {
                        top: {style: 'medium', color: {rgb: '000000'}},
                        bottom: {style: 'medium', color: {rgb: '000000'}},
                        left: {style: 'thin', color: {rgb: '000000'}},
                        right: {style: 'thin', color: {rgb: '000000'}}
                    }
                };
            });
            
            // Style data rows with enhanced borders and formatting
            for (let i = headerRowIndex + 1; i < allData.length; i++) {
                headerCells.forEach(col => {
                    const cellRef = col + (i + 1);
                    if (!wsManifest[cellRef]) wsManifest[cellRef] = {v: '', t: 's'};
                    wsManifest[cellRef].s = {
                        font: {sz: 10, color: {rgb: '000000'}},
                        border: {
                            top: {style: 'thin', color: {rgb: '000000'}},
                            bottom: {style: 'thin', color: {rgb: '000000'}},
                            left: {style: 'thin', color: {rgb: '000000'}},
                            right: {style: 'thin', color: {rgb: '000000'}}
                        },
                        alignment: {horizontal: 'left', vertical: 'top', wrapText: true}
                    };
                });
                
                // Set special formatting for No column (center alignment)
                const noCell = 'A' + (i + 1);
                if (wsManifest[noCell]) {
                    wsManifest[noCell].s.alignment.horizontal = 'center';
                }
                
                // Set special formatting for Sex column (center alignment)
                const sexCell = 'B' + (i + 1);
                if (wsManifest[sexCell]) {
                    wsManifest[sexCell].s.alignment.horizontal = 'center';
                }
            }
            
            // Set column widths for better formatting (based on manifest template)
            wsManifest['!cols'] = [
                {wch: 4},   // No
                {wch: 6},   // Sex  
                {wch: 30},  // Name of Passport
                {wch: 20},  // Marketing
                {wch: 25},  // Nama Ayah
                {wch: 12},  // Birth: Date
                {wch: 20},  // Birth: City
                {wch: 18},  // Passport: No.Passport
                {wch: 18},  // Passport: Issuing Office
                {wch: 15},  // Passport: Date of Issue
                {wch: 15},  // Passport: Date of Expiry
                {wch: 15},  // Relation
                {wch: 5},   // Age
                {wch: 12},  // Cabang
                {wch: 12},  // Roomlist
                {wch: 18},  // NIK
                {wch: 50},  // Alamat
                {wch: 25}   // Keterangan
            ];
            
            // Add the manifest sheet to workbook
            XLSX.utils.book_append_sheet(wb, wsManifest, 'Manifest');
            
            // Add roomlist sheet if data is available (following manifest_template.xlsx structure)
            if (data.roomLists && (data.roomLists.medinah?.length > 0 || data.roomLists.makkah?.length > 0)) {
                console.log("Adding roomlist sheet to the export (following manifest_template structure)");
                
                // Create roomlist sheet following exact manifest_template.xlsx structure (without Location column)
                const roomlistHeaderInfo = [
                    [`Roomlist - ${data.package.name || 'Unknown Package'}`],
                    [`Tanggal ${data.package.departure_date || 'Unknown Date'}`],
                    [`Program ${data.package.type || 'Unknown Type'}`],
                    [''],
                    ['Room Number', 'Type', 'Guest', 'Name', 'NIK', 'Sex', 'Age', 'Relation']
                ];
                
                // Combine both Medinah and Makkah data into a single list (as per manifest_template.xlsx)
                const combinedRoomlistRows = [];
                
                // Add Medinah rooms first
                if (data.roomLists.medinah && data.roomLists.medinah.length > 0) {
                    console.log("Adding Medinah rooms to roomlist");
                    data.roomLists.medinah.forEach(row => {
                        combinedRoomlistRows.push([
                            row['Room Number'] || '',
                            row['Type'] || '',
                            row['Guest'] || '',
                            row['Name'] || '',
                            row['NIK'] || '',
                            row['Sex'] || '',
                            row['Age'] || '',
                            row['Relation'] || ''
                        ]);
                    });
                }
                
                // Add Makkah rooms without separator (unified roomlist as per template)
                if (data.roomLists.makkah && data.roomLists.makkah.length > 0) {
                    console.log("Adding Makkah rooms to roomlist");
                    data.roomLists.makkah.forEach(row => {
                        combinedRoomlistRows.push([
                            row['Room Number'] || '',
                            row['Type'] || '',
                            row['Guest'] || '',
                            row['Name'] || '',
                            row['NIK'] || '',
                            row['Sex'] || '',
                            row['Age'] || '',
                            row['Relation'] || ''
                        ]);
                    });
                }
                
                const roomlistData = [...roomlistHeaderInfo, ...combinedRoomlistRows];
                const wsRoomlist = XLSX.utils.aoa_to_sheet(roomlistData);
                
                // Apply styling for roomlist sheet
                if (!wsRoomlist['!merges']) wsRoomlist['!merges'] = [];
                
                // Merge cells for title rows (8 columns now, not 9)
                wsRoomlist['!merges'].push({s: {r: 0, c: 0}, e: {r: 0, c: 7}});
                wsRoomlist['!merges'].push({s: {r: 1, c: 0}, e: {r: 1, c: 7}});
                wsRoomlist['!merges'].push({s: {r: 2, c: 0}, e: {r: 2, c: 7}});
                
                // Style title rows
                ['A1', 'A2', 'A3'].forEach(cellRef => {
                    if (!wsRoomlist[cellRef]) wsRoomlist[cellRef] = {v: '', t: 's'};
                    wsRoomlist[cellRef].s = {
                        font: {bold: true, sz: 14, color: {rgb: '000000'}},
                        alignment: {horizontal: 'center', vertical: 'center'},
                        fill: {patternType: 'solid', fgColor: {rgb: 'F2F2F2'}},
                        border: {
                            top: {style: 'medium', color: {rgb: '000000'}},
                            bottom: {style: 'medium', color: {rgb: '000000'}},
                            left: {style: 'medium', color: {rgb: '000000'}},
                            right: {style: 'medium', color: {rgb: '000000'}}
                        }
                    };
                });
                
                // Style header row (8 columns: A-H)
                const roomHeaderCells = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                const roomHeaderRowIndex = 4;
                roomHeaderCells.forEach(col => {
                    const cellRef = col + (roomHeaderRowIndex + 1);
                    if (!wsRoomlist[cellRef]) wsRoomlist[cellRef] = {v: '', t: 's'};
                    wsRoomlist[cellRef].s = {
                        font: {bold: true, sz: 11, color: {rgb: '000000'}},
                        alignment: {horizontal: 'center', vertical: 'center'},
                        fill: {patternType: 'solid', fgColor: {rgb: 'D9D9D9'}},
                        border: {
                            top: {style: 'medium', color: {rgb: '000000'}},
                            bottom: {style: 'medium', color: {rgb: '000000'}},
                            left: {style: 'thin', color: {rgb: '000000'}},
                            right: {style: 'thin', color: {rgb: '000000'}}
                        }
                    };
                });
                
                // Style data rows with borders
                for (let i = roomHeaderRowIndex + 1; i < roomlistData.length; i++) {
                    roomHeaderCells.forEach(col => {
                        const cellRef = col + (i + 1);
                        if (!wsRoomlist[cellRef]) wsRoomlist[cellRef] = {v: '', t: 's'};
                        wsRoomlist[cellRef].s = {
                            font: {sz: 10, color: {rgb: '000000'}},
                            border: {
                                top: {style: 'thin', color: {rgb: '000000'}},
                                bottom: {style: 'thin', color: {rgb: '000000'}},
                                left: {style: 'thin', color: {rgb: '000000'}},
                                right: {style: 'thin', color: {rgb: '000000'}}
                            },
                            alignment: {horizontal: 'left', vertical: 'top'}
                        };
                    });
                }
                
                // Set column widths (following manifest_template.xlsx roomlist structure)
                wsRoomlist['!cols'] = [
                    {wch: 12}, // Room Number
                    {wch: 10}, // Type
                    {wch: 8},  // Guest
                    {wch: 25}, // Name
                    {wch: 20}, // NIK
                    {wch: 5},  // Sex
                    {wch: 5},  // Age
                    {wch: 15}  // Relation
                ];
                
                XLSX.utils.book_append_sheet(wb, wsRoomlist, 'Roomlist');
                
                console.log("Roomlist sheet added successfully (following manifest_template structure)");
            }
            
            // Add kelengkapan sheet if data is available
            if (data.kelengkapan && data.kelengkapan.length > 0) {
                console.log("Adding kelengkapan sheet to the export");
                
                const kelengkapanHeaderInfo = [
                    [`Kelengkapan Dokumen - ${data.package.name || 'Unknown Package'}`],
                    [`Tanggal ${data.package.departure_date || 'Unknown Date'}`],
                    [`Program ${data.package.type || 'Unknown Type'}`],
                    [''],
                    ['No', 'NIK', 'Name', 'Sex', 'Buku Kuning', 'Foto', 'FC KTP', 'FC Ijazah', 'FC KK', 'FC Buku Nikah', 'FC Akta Lahir', 'Upload Date BK', 'Upload Date Foto', 'Completion Status']
                ];
                
                const kelengkapanRows = data.kelengkapan.map(row => [
                    row['No'] || '',
                    row['NIK'] || '',
                    row['Name'] || '',
                    row['Sex'] || '',
                    row['Buku Kuning'] || '',
                    row['Foto'] || '',
                    row['FC KTP'] || '',
                    row['FC Ijazah'] || '',
                    row['FC KK'] || '',
                    row['FC Buku Nikah'] || '',
                    row['FC Akta Lahir'] || '',
                    row['Upload Date BK'] || '',
                    row['Upload Date Foto'] || '',
                    row['Completion Status'] || ''
                ]);
                
                const kelengkapanData = [...kelengkapanHeaderInfo, ...kelengkapanRows];
                const wsKelengkapan = XLSX.utils.aoa_to_sheet(kelengkapanData);
                
                // Apply styling for kelengkapan sheet
                if (!wsKelengkapan['!merges']) wsKelengkapan['!merges'] = [];
                
                // Merge cells for title rows
                wsKelengkapan['!merges'].push({s: {r: 0, c: 0}, e: {r: 0, c: 13}});
                wsKelengkapan['!merges'].push({s: {r: 1, c: 0}, e: {r: 1, c: 13}});
                wsKelengkapan['!merges'].push({s: {r: 2, c: 0}, e: {r: 2, c: 13}});
                
                // Style title rows
                ['A1', 'A2', 'A3'].forEach(cellRef => {
                    if (!wsKelengkapan[cellRef]) wsKelengkapan[cellRef] = {v: '', t: 's'};
                    wsKelengkapan[cellRef].s = {
                        font: {bold: true, sz: 14, color: {rgb: '000000'}},
                        alignment: {horizontal: 'center', vertical: 'center'},
                        fill: {patternType: 'solid', fgColor: {rgb: 'F2F2F2'}},
                        border: {
                            top: {style: 'medium', color: {rgb: '000000'}},
                            bottom: {style: 'medium', color: {rgb: '000000'}},
                            left: {style: 'medium', color: {rgb: '000000'}},
                            right: {style: 'medium', color: {rgb: '000000'}}
                        }
                    };
                });
                
                // Style header row
                const kelengkapanHeaderCells = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];
                const kelengkapanHeaderRowIndex = 4;
                kelengkapanHeaderCells.forEach(col => {
                    const cellRef = col + (kelengkapanHeaderRowIndex + 1);
                    if (!wsKelengkapan[cellRef]) wsKelengkapan[cellRef] = {v: '', t: 's'};
                    wsKelengkapan[cellRef].s = {
                        font: {bold: true, sz: 11, color: {rgb: '000000'}},
                        alignment: {horizontal: 'center', vertical: 'center', wrapText: true},
                        fill: {patternType: 'solid', fgColor: {rgb: 'D9D9D9'}},
                        border: {
                            top: {style: 'medium', color: {rgb: '000000'}},
                            bottom: {style: 'medium', color: {rgb: '000000'}},
                            left: {style: 'thin', color: {rgb: '000000'}},
                            right: {style: 'thin', color: {rgb: '000000'}}
                        }
                    };
                });
                
                // Style data rows with enhanced formatting for status indicators
                for (let i = kelengkapanHeaderRowIndex + 1; i < kelengkapanData.length; i++) {
                    kelengkapanHeaderCells.forEach((col, colIndex) => {
                        const cellRef = col + (i + 1);
                        if (!wsKelengkapan[cellRef]) wsKelengkapan[cellRef] = {v: '', t: 's'};
                        
                        let cellStyle = {
                            font: {sz: 10, color: {rgb: '000000'}},
                            border: {
                                top: {style: 'thin', color: {rgb: '000000'}},
                                bottom: {style: 'thin', color: {rgb: '000000'}},
                                left: {style: 'thin', color: {rgb: '000000'}},
                                right: {style: 'thin', color: {rgb: '000000'}}
                            },
                            alignment: {horizontal: 'left', vertical: 'top'}
                        };
                        
                        // Special styling for document status columns (E-K: checkmarks)
                        if (colIndex >= 4 && colIndex <= 10) {
                            cellStyle.alignment.horizontal = 'center';
                            if (wsKelengkapan[cellRef].v === '✓') {
                                cellStyle.font.color = {rgb: '008000'}; // Green for completed
                                cellStyle.font.bold = true;
                            } else if (wsKelengkapan[cellRef].v === '✗') {
                                cellStyle.font.color = {rgb: 'FF0000'}; // Red for missing
                                cellStyle.font.bold = true;
                            }
                        }
                        
                        // Special styling for completion status column (N)
                        if (colIndex === 13) {
                            cellStyle.alignment.horizontal = 'center';
                            const status = wsKelengkapan[cellRef].v;
                            if (status === 'Complete') {
                                cellStyle.fill = {patternType: 'solid', fgColor: {rgb: 'C6EFCE'}};
                                cellStyle.font.color = {rgb: '006100'};
                                cellStyle.font.bold = true;
                            } else if (status === 'Almost Complete') {
                                cellStyle.fill = {patternType: 'solid', fgColor: {rgb: 'FFEB9C'}};
                                cellStyle.font.color = {rgb: '9C6500'};
                                cellStyle.font.bold = true;
                            } else if (status === 'In Progress') {
                                cellStyle.fill = {patternType: 'solid', fgColor: {rgb: 'FFCCCB'}};
                                cellStyle.font.color = {rgb: '9C2500'};
                                cellStyle.font.bold = true;
                            } else if (status === 'Incomplete') {
                                cellStyle.fill = {patternType: 'solid', fgColor: {rgb: 'FFC7CE'}};
                                cellStyle.font.color = {rgb: '9C0006'};
                                cellStyle.font.bold = true;
                            }
                        }
                        
                        wsKelengkapan[cellRef].s = cellStyle;
                    });
                }
                
                // Set column widths for kelengkapan sheet
                wsKelengkapan['!cols'] = [
                    {wch: 4},   // No
                    {wch: 18},  // NIK
                    {wch: 25},  // Name
                    {wch: 8},   // Sex
                    {wch: 8},   // Buku Kuning
                    {wch: 6},   // Foto
                    {wch: 8},   // FC KTP
                    {wch: 10},  // FC Ijazah
                    {wch: 8},   // FC KK
                    {wch: 12},  // FC Buku Nikah
                    {wch: 12},  // FC Akta Lahir
                    {wch: 12},  // Upload Date BK
                    {wch: 12},  // Upload Date Foto
                    {wch: 15}   // Completion Status
                ];
                
                XLSX.utils.book_append_sheet(wb, wsKelengkapan, 'Kelengkapan');
                
                console.log("Kelengkapan sheet added successfully");
            }
            
            // Create descriptive filename
            let packageName = (data.package.name || 'unknown_package').replace(/[^a-z0-9]/gi, '_').toLowerCase();
            if (packageName.length > 30) {
                packageName = packageName.substring(0, 30);
            }
            
            let departureDate = (data.package.departure_date || '').replace(/\//g, '');
            const filename = `manifest_with_roomlist_kelengkapan_${packageName}_${departureDate}_pak${pakId}.xlsx`;
            
            console.log("Generating Excel file:", filename);
            
            // Export the workbook to file
            XLSX.writeFile(wb, filename);
            
            console.log(`Combined export successful: ${filename}`);
            console.log("Manifest records exported:", data.manifest.length);
            console.log("Roomlist sheets added:", data.roomLists ? (data.roomLists.medinah?.length || 0) + (data.roomLists.makkah?.length || 0) : 0);
            console.log("Kelengkapan records exported:", data.kelengkapan ? data.kelengkapan.length : 0);
            console.log("=== COMBINED MANIFEST & ROOMLIST & KELENGKAPAN EXPORT END ===");
            
            // Show success message with details
            const roomlistCount = data.roomLists ? 
                (data.roomLists.medinah?.length || 0) + (data.roomLists.makkah?.length || 0) : 0;
            const kelengkapanCount = data.kelengkapan ? data.kelengkapan.length : 0;
            
            let sheetsInfo = 'Manifest';
            if (data.roomLists?.medinah?.length > 0) sheetsInfo += ', Roomlist';
            if (data.roomLists?.makkah?.length > 0) sheetsInfo += ', Makkah Roomlist';
            if (kelengkapanCount > 0) sheetsInfo += ', Kelengkapan';
            
            alert(`Manifest, Roomlist & Kelengkapan export completed successfully!
            
File: ${filename}
Package: ${data.package.name}
Manifest records: ${data.manifest.length}
Roomlist records: ${roomlistCount}
Kelengkapan records: ${kelengkapanCount}
Sheets: ${sheetsInfo}
            
The file has been downloaded to your Downloads folder.`);
            
        } else {
            console.error('No data found for export');
            console.log("Data structure received:", data);
            
            alert('No manifest data found for export. Please check that the package has jamaah assigned to it.');
        }
    }

    function exportToExcelAjax(pakId, type) {
        $.ajax({
            url: 'export_manifest.php',
            type: 'POST',
            data: { pak_id: pakId, export_type: type },
            success: function(response) {
                if (!response.success) {
                    // Check if we need to redirect for table setup
                    if (response.error_code === 'table_not_exists' && response.redirect) {
                        if (confirm('The manifest table does not exist. Would you like to run the setup script now?')) {
                            window.location.href = response.redirect;
                        }
                        return;
                    }
                    alert(response.message || 'Error during export');
                    return;
                }
                if (!response.data) {
                    alert('No data found for export');
                    return;
                }
                
                if (type === 'manifest' && (!response.data.manifest || response.data.manifest.length === 0)) {
                    alert('No manifest data found for export');
                    return;
                }

                // Use the simplified export function
                exportToExcel(pakId, type, response.data);
            },
            error: function(xhr, status, error) {
                console.error('Export error:', error);
                let message = 'Error generating Excel file';
                try {
                    const response = JSON.parse(xhr.responseText);
                    message = response.message || message;
                } catch (e) {}
                alert('Error during export: ' + message);
            }
        });
    }

    // Make the exportToExcel function available globally
    window.exportToExcel = exportToExcel;
    window.exportToExcelAjax = exportToExcelAjax;
});
