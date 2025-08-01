// Roomlist Management Script
// This script handles roomlist export and management functionality

// Wait for document ready
jQuery(document).ready(function($) {
    // Initialize DataTables with destroy option
    const tables = $('.package-table').each(function() {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable(this)) {
            $(this).DataTable().destroy();
        }
        
        // Initialize new DataTable
        $(this).DataTable({
            destroy: true, // Enable table destruction
            responsive: true,
            columnDefs: [
                {
                    targets: [1], // NIK column index
                    width: '120px' // Set width for NIK column
                }
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data yang ditampilkan",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    });

    // Roomlist export function
    $(document).on('click', '.export-roomlist', function() {
        const pakId = $(this).data('pakid');
        console.log('Exporting roomlist for package:', pakId);
        if (!pakId) {
            alert('No package ID found for roomlist export');
            return;
        }
        exportRoomlistToExcel(pakId);
    });

    function exportRoomlistToExcel(pakId) {
        console.log("=== ROOMLIST EXPORT START ===");
        console.log("Package ID:", pakId);
        
        // Fetch roomlist data via AJAX
        $.ajax({
            url: 'export_manifest.php',
            type: 'POST',
            data: { pak_id: pakId, export_type: 'roomlist' },
            success: function(response) {
                if (!response.success) {
                    alert(response.message || 'Error during roomlist export');
                    return;
                }
                
                if (!response.data || !response.data.roomLists) {
                    alert('No roomlist data found for export');
                    return;
                }
                
                const data = response.data;
                console.log("Roomlist data received:", data);
                
                // Create workbook for roomlist export
                const wb = XLSX.utils.book_new();
                
                // Create roomlist sheet (following manifest_template.xlsx structure)
                if ((data.roomLists.medinah && data.roomLists.medinah.length > 0) || 
                    (data.roomLists.makkah && data.roomLists.makkah.length > 0)) {
                    console.log("Creating roomlist sheet (following manifest_template structure)");
                    
                    const roomlistHeaderInfo = [
                        [`Roomlist - ${data.package.name || 'Unknown Package'}`],
                        [`Tanggal ${data.package.departure_date || 'Unknown Date'}`],
                        [`Program ${data.package.type || 'Unknown Type'}`],
                        [''],
                        ['Room Number', 'Type', 'Guest', 'Name', 'NIK', 'Sex', 'Age', 'Relation']
                    ];
                    
                    // Combine both Medinah and Makkah data into a single unified list
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
                    
                    // Add Makkah rooms directly (unified list as per manifest_template.xlsx)
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
                    
                    // Merge cells for title rows (8 columns: A-H)
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
                
                // Create filename for roomlist
                let packageName = (data.package.name || 'unknown_package').replace(/[^a-z0-9]/gi, '_').toLowerCase();
                if (packageName.length > 30) {
                    packageName = packageName.substring(0, 30);
                }
                
                let departureDate = (data.package.departure_date || '').replace(/\//g, '');
                const filename = `roomlist_${packageName}_${departureDate}_pak${pakId}.xlsx`;
                
                console.log("Generating roomlist Excel file:", filename);
                
                // Export the workbook to file
                XLSX.writeFile(wb, filename);
                
                console.log(`Roomlist export successful: ${filename}`);
                console.log("=== ROOMLIST EXPORT END ===");
                
                // Show success message
                alert(`Combined roomlist export completed successfully!
                
File: ${filename}
Package: ${data.package.name}
Total rooms: ${(data.roomLists.medinah ? data.roomLists.medinah.length : 0) + (data.roomLists.makkah ? data.roomLists.makkah.length : 0)}
                
The file has been downloaded to your Downloads folder.`);
                
            },
            error: function(xhr, status, error) {
                console.error('Roomlist export error:', error);
                let message = 'Error generating roomlist Excel file';
                try {
                    const response = JSON.parse(xhr.responseText);
                    message = response.message || message;
                } catch (e) {}
                alert('Error during roomlist export: ' + message);
            }
        });
    }

    // Update roomlist record
    $('.update-manifest').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        // Create object for roomlist data only
        const roomlistData = {
            nik: form.find('input[name="nik"]').val(),
            pak_id: form.find('input[name="pak_id"]').val(),
            room_prefix: form.find('select[name="room_prefix"]').val(),
            medinah_number: form.find('input[name="medinah_number"]').val(),
            mekkah_number: form.find('input[name="mekkah_number"]').val()
        };

        // Add relation only if the field has a value
        const relation = form.find('input[name="relation"]').val();
        if (relation) {
            roomlistData.relation = relation;
        }
        
        $.ajax({
            url: 'update_manifest.php',
            type: 'POST',
            data: roomlistData,
            success: function(response) {
                if (response.success) {
                    alert('Roomlist record updated successfully');
                    location.reload();
                } else {
                    // Check if we need to redirect for table setup
                    if (response.error_code === 'table_not_exists' && response.redirect) {
                        if (confirm('The manifest table does not exist. Would you like to run the setup script now?')) {
                            window.location.href = response.redirect;
                        }
                        return;
                    }
                    alert(response.message || 'Error updating roomlist record');
                }
            },
            error: function() {
                alert('Error updating roomlist record');
            }
        });
    });

    // Handle roomlist form submission
    $('.manifest-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitButton = form.find('button[type="submit"]');
        
        // Disable submit button to prevent double submission
        submitButton.prop('disabled', true);
        
        $.ajax({
            url: 'update_manifest.php',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Roomlist updated successfully');
                    location.reload();
                } else {
                    // Check if we need to redirect for table setup
                    if (response.error_code === 'table_not_exists' && response.redirect) {
                        if (confirm('The manifest table does not exist. Would you like to run the setup script now?')) {
                            window.location.href = response.redirect;
                        }
                        return;
                    }
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error updating roomlist: ' + error);
            },
            complete: function() {
                // Re-enable submit button
                submitButton.prop('disabled', false);
            }
        });
    });

    // Make functions available globally
    window.exportRoomlistToExcel = exportRoomlistToExcel;
});
