document.addEventListener('DOMContentLoaded', function() {
    // Handle add hotel button
    document.getElementById('addHotel')?.addEventListener('click', function() {
        addHotelField('hotelsContainer');
    });
    
    // Handle edit hotel button
    document.getElementById('addEditHotel')?.addEventListener('click', function() {
        addHotelField('editHotelsContainer');
    });
    
    // Auto-set currency based on package type
    document.getElementById('add_jenis_paket')?.addEventListener('change', function() {
        document.getElementById('add_currency').value = this.value === 'Haji' ? 'USD' : 'IDR';
    });
    
    document.getElementById('edit_jenis_paket')?.addEventListener('change', function() {
        document.getElementById('edit_currency').value = this.value === 'Haji' ? 'USD' : 'IDR';
    });
    
    // Initialize currency values on load
    if (document.getElementById('add_jenis_paket')) {
        document.getElementById('add_currency').value = 
            document.getElementById('add_jenis_paket').value === 'Haji' ? 'USD' : 'IDR';
    }
});

function addHotelField(containerId) {
    const container = document.getElementById(containerId);
    const index = container.children.length;
    
    const hotelDiv = document.createElement('div');
    hotelDiv.className = 'mb-3 hotel-entry';
    hotelDiv.innerHTML = `
        <div class="row">
            <div class="col-md-12">
                <label class="form-label">Hotel Name</label>
                <input type="text" class="form-control" name="additional_hotels[${index}][name]">
                
                <div class="row mt-2">
                    <div class="col-md-3">
                        <label class="form-label">Quad Room Numbers</label>
                        <input type="text" class="form-control" name="additional_hotels[${index}][quad_rooms]" placeholder="e.g., 101,102">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Triple Room Numbers</label>
                        <input type="text" class="form-control" name="additional_hotels[${index}][triple_rooms]" placeholder="e.g., 201,202">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Double Room Numbers</label>
                        <input type="text" class="form-control" name="additional_hotels[${index}][double_rooms]" placeholder="e.g., 301,302">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">HCN</label>
                        <input type="text" class="form-control" name="additional_hotels[${index}][hcn]" placeholder="e.g., IST-TK2026-3284">
                        <button type="button" class="btn btn-sm btn-danger mt-2 remove-hotel">Remove Hotel</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(hotelDiv);
    
    // Add event listener for remove button
    hotelDiv.querySelector('.remove-hotel')?.addEventListener('click', function() {
        hotelDiv.remove();
    });
}

function loadEditData(pakId) {
    // Show loading spinner or message
    const editModal = new bootstrap.Modal(document.getElementById('editPackageModal'));
    editModal.show();
    
    // Add a loading indicator to the modal body
    const modalBody = document.querySelector('#editPackageModal .modal-body');
    if (modalBody) {
        modalBody.style.opacity = '0.5';
        modalBody.insertAdjacentHTML('afterbegin', '<div class="text-center" id="loadingIndicator"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    }
    
    fetch('get_package.php?id=' + pakId)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (!data) {
                throw new Error('No data received');
            }
            
            // Helper function to safely set form field values
            const setFieldValue = (id, value) => {
                const element = document.getElementById(id);
                if (element) {
                    element.value = value || '';
                }
            };

            // Fill form fields
            setFieldValue('edit_package_id', data.pak_id);
            setFieldValue('edit_jenis_paket', data.jenis_paket);
            setFieldValue('edit_currency', data.currency);
            setFieldValue('edit_program_pilihan', data.program_pilihan);
            setFieldValue('edit_tanggal_keberangkatan', data.tanggal_keberangkatan);
            setFieldValue('edit_base_price_quad', data.base_price_quad);
            setFieldValue('edit_base_price_triple', data.base_price_triple);
            setFieldValue('edit_base_price_double', data.base_price_double);
            setFieldValue('edit_hotel_medinah', data.hotel_medinah);
            setFieldValue('edit_hotel_makkah', data.hotel_makkah);
            
            try {
                // Fill HCN data
                const hcnData = JSON.parse(data.hcn || '{}');
                setFieldValue('edit_hcn_medinah', hcnData.medinah);
                setFieldValue('edit_hcn_makkah', hcnData.makkah);
                setFieldValue('edit_hcn_issued_date', hcnData.issued_date);
                setFieldValue('edit_hcn_expiry_date', hcnData.expiry_date);
            } catch (e) {
                console.error('Error parsing HCN data:', e);
            }
            
            try {
                // Process room numbers
                const medinahRooms = JSON.parse(data.hotel_medinah_rooms || '{}');
                const makkahRooms = JSON.parse(data.hotel_makkah_rooms || '{}');
                
                setFieldValue('edit_medinah_quad_rooms', medinahRooms.quad?.join(','));
                setFieldValue('edit_medinah_triple_rooms', medinahRooms.triple?.join(','));
                setFieldValue('edit_medinah_double_rooms', medinahRooms.double?.join(','));
                setFieldValue('edit_makkah_quad_rooms', makkahRooms.quad?.join(','));
                setFieldValue('edit_makkah_triple_rooms', makkahRooms.triple?.join(','));
                setFieldValue('edit_makkah_double_rooms', makkahRooms.double?.join(','));
            } catch (e) {
                console.error('Error parsing room data:', e);
            }
            
            // Process additional hotels with room types and HCN
            const editHotelsContainer = document.getElementById('editHotelsContainer');
            if (editHotelsContainer) {
                editHotelsContainer.innerHTML = '';
                
                try {
                    const additionalHotels = JSON.parse(data.additional_hotels || '[]');
                    const additionalHotelsRooms = JSON.parse(data.additional_hotels_rooms || '[]');
                    const additionalHcns = (JSON.parse(data.hcn || '{}')).additional || [];
                    
                    additionalHotels.forEach((hotel, index) => {
                        const hotelDiv = document.createElement('div');
                        hotelDiv.className = 'mb-3 hotel-entry';
                        hotelDiv.innerHTML = `
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label">Hotel Name</label>
                                    <input type="text" class="form-control" name="additional_hotels[${index}][name]" value="${hotel || ''}">
                                    
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label class="form-label">Quad Room Numbers</label>
                                            <input type="text" class="form-control" name="additional_hotels[${index}][quad_rooms]" 
                                                value="${additionalHotelsRooms[index]?.quad?.join(',') || ''}" placeholder="e.g., 101,102">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Triple Room Numbers</label>
                                            <input type="text" class="form-control" name="additional_hotels[${index}][triple_rooms]" 
                                                value="${additionalHotelsRooms[index]?.triple?.join(',') || ''}" placeholder="e.g., 201,202">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Double Room Numbers</label>
                                            <input type="text" class="form-control" name="additional_hotels[${index}][double_rooms]" 
                                                value="${additionalHotelsRooms[index]?.double?.join(',') || ''}" placeholder="e.g., 301,302">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">HCN</label>
                                            <input type="text" class="form-control" name="additional_hotels[${index}][hcn]" 
                                                value="${additionalHcns[index] || ''}" placeholder="e.g., IST-TK2026-3284">
                                            <button type="button" class="btn btn-sm btn-danger mt-2 remove-hotel">Remove Hotel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        editHotelsContainer.appendChild(hotelDiv);
                        
                        // Add event listener for remove button
                        hotelDiv.querySelector('.remove-hotel')?.addEventListener('click', function() {
                            hotelDiv.remove();
                        });
                    });
                } catch (e) {
                    console.error('Error processing additional hotels:', e);
                }
            }
        })
        .catch(error => {
            console.error('Error loading package data:', error);
            alert('Error loading package data: ' + error.message);
        })
        .finally(() => {
            // Remove loading indicator and restore opacity
            const loadingIndicator = document.getElementById('loadingIndicator');
            if (loadingIndicator) {
                loadingIndicator.remove();
            }
            if (modalBody) {
                modalBody.style.opacity = '1';
            }
        });
}