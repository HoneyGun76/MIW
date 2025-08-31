<!-- Document Management Modal -->
<div class="modal fade" id="documentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Management - <span id="jamaahName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="documentForm" enctype="multipart/form-data">
                    <input type="hidden" id="jamaahNik" name="nik">
                    
                    <div class="mb-3">
                        <label for="bk_kuning" class="form-label">Buku Kuning</label>
                        <input type="file" class="form-control" id="bk_kuning" name="bk_kuning" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept=".jpg,.jpeg,.png">
                    </div>
                    
                    <div class="mb-3">
                        <label for="fc_ktp" class="form-label">Fotocopy KTP</label>
                        <input type="file" class="form-control" id="fc_ktp" name="fc_ktp" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    
                    <div class="mb-3">
                        <label for="fc_ijazah" class="form-label">Fotocopy Ijazah</label>
                        <input type="file" class="form-control" id="fc_ijazah" name="fc_ijazah" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    
                    <div class="mb-3">
                        <label for="fc_kk" class="form-label">Fotocopy KK</label>
                        <input type="file" class="form-control" id="fc_kk" name="fc_kk" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    
                    <div class="mb-3">
                        <label for="fc_bk_nikah" class="form-label">Fotocopy Buku Nikah</label>
                        <input type="file" class="form-control" id="fc_bk_nikah" name="fc_bk_nikah" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    
                    <div class="mb-3">
                        <label for="fc_akta_lahir" class="form-label">Fotocopy Akta Kelahiran</label>
                        <input type="file" class="form-control" id="fc_akta_lahir" name="fc_akta_lahir" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="uploadDocuments()">Upload Documents</button>
            </div>
        </div>
    </div>
</div>

<script>
function openDocumentModal(nik, nama) {
    document.getElementById('jamaahNik').value = nik;
    document.getElementById('jamaahName').textContent = nama;
    new bootstrap.Modal(document.getElementById('documentModal')).show();
}

function uploadDocuments() {
    const form = document.getElementById('documentForm');
    const formData = new FormData(form);

    fetch('handle_document_upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Documents uploaded successfully!');
            location.reload(); // Refresh the page to see updates
        } else {
            alert('Error uploading documents: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while uploading documents');
    });
}
</script>
