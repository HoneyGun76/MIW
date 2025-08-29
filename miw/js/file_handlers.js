// Function to handle file preview/download
function handleFile(filePath, type, action = 'preview') {
    if (!filePath) return;
    
    // Extract filename from path and clean it
    const filename = filePath.split(/[\\/]/).pop();
    const url = `file_handler.php?file=${encodeURIComponent(filename)}&type=${type}&action=${action}`;
    
    if (action === 'preview') {
        // For PDFs and images, show in modal
        const extension = filename.split('.').pop().toLowerCase();
        if (['pdf', 'jpg', 'jpeg', 'png'].includes(extension)) {
            const modal = document.getElementById('filePreviewModal');
            const modalBody = modal.querySelector('.modal-body');
            const bootstrap = window.bootstrap;
            
            modalBody.innerHTML = extension === 'pdf' ?
                `<embed src="${url}" type="application/pdf" width="100%" height="600px">` :
                `<img src="${url}" class="img-fluid" alt="Preview">`;
            
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        } else {
            // For other file types, download directly
            window.location.href = url + '&action=download';
        }
    } else {
        // Direct download
        window.location.href = url;
    }
}

// Function to add file action buttons
function addFileActionButtons(container, filePath, type) {
    if (!filePath) {
        container.innerHTML = '<span class="text-muted">No file uploaded</span>';
        return;
    }
    
    const previewBtn = document.createElement('button');
    previewBtn.className = 'btn btn-sm btn-outline-primary me-2';
    previewBtn.innerHTML = '<i class="bi bi-eye"></i> Preview';
    previewBtn.onclick = () => handleFile(filePath, type, 'preview');
    
    const downloadBtn = document.createElement('button');
    downloadBtn.className = 'btn btn-sm btn-outline-success';
    downloadBtn.innerHTML = '<i class="bi bi-download"></i> Download';
    downloadBtn.onclick = () => handleFile(filePath, type, 'download');
    
    container.innerHTML = '';
    container.appendChild(previewBtn);
    container.appendChild(downloadBtn);
}
