<?php foreach ($packages as $package): ?>
    <div class="package-header">
        <h4><?= htmlspecialchars($package['jenis_paket']) ?> - <?= htmlspecialchars($package['program_pilihan']) ?></h4>
        <p>
            <strong>Tanggal Keberangkatan:</strong> <?= date('d/m/Y', strtotime($package['tanggal_keberangkatan'])) ?><br>
            <strong>Hotel Madinah:</strong> <?= htmlspecialchars($package['hotel_medinah']) ?><br>
            <strong>Hotel Makkah:</strong> <?= htmlspecialchars($package['hotel_makkah']) ?>
        </p>
    </div>

    <table class="table table-striped package-table">
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Buku Kuning</th>
                <th>Foto</th>
                <th>Fotocopy KTP</th>
                <th>Fotocopy Ijazah</th>
                <th>Fotocopy KK</th>
                <th>Fotocopy Buku Nikah</th>
                <th>Fotocopy Akta Kelahiran</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Get jamaah for this package
            $stmt = $conn->prepare("SELECT * FROM data_jamaah WHERE pak_id = ?");
            $stmt->execute([$package['pak_id']]);
            $jamaahs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $counter = 1;
            foreach ($jamaahs as $jamaah): 
            ?>
            <tr>
                <td><?= $counter++ ?></td>
                <td><?= htmlspecialchars($jamaah['nik']) ?></td>
                <td><?= htmlspecialchars($jamaah['nama']) ?></td>
                <td><?= htmlspecialchars($jamaah['jenis_kelamin']) ?></td>
                <td>
                    <form class="kelengkapan-form" enctype="multipart/form-data">
                        <input type="hidden" name="nik" value="<?= $jamaah['nik'] ?>">
                        <div class="upload-status">
                            <?php if ($jamaah['bk_kuning']): ?>
                                <span class="text-success">Uploaded <?= date('d/m/Y H:i', strtotime($jamaah['bk_kuning'])) ?></span>
                            <?php endif; ?>
                        </div>
                        <input type="file" class="form-control" name="bk_kuning" accept=".pdf,.jpg,.jpeg,.png">
                </td>
                <td>
                    <div class="upload-status">
                        <?php if ($jamaah['foto']): ?>
                            <span class="text-success">Uploaded <?= date('d/m/Y H:i', strtotime($jamaah['foto'])) ?></span>
                        <?php endif; ?>
                    </div>
                    <input type="file" class="form-control" name="foto" accept=".jpg,.jpeg,.png">
                </td>
                <td>
                    <div class="upload-status">
                        <?php if ($jamaah['fc_ktp_path']): ?>
                            <span class="text-success">Uploaded <?= date('d/m/Y H:i', strtotime($jamaah['fc_ktp_path'])) ?></span>
                        <?php endif; ?>
                    </div>
                    <input type="file" class="form-control" name="fc_ktp" accept=".pdf,.jpg,.jpeg,.png">
                </td>
                <td>
                    <div class="upload-status">
                        <?php if ($jamaah['fc_ijazah_path']): ?>
                            <span class="text-success">Uploaded <?= date('d/m/Y H:i', strtotime($jamaah['fc_ijazah_path'])) ?></span>
                        <?php endif; ?>
                    </div>
                    <input type="file" class="form-control" name="fc_ijazah" accept=".pdf,.jpg,.jpeg,.png">
                </td>
                <td>
                    <div class="upload-status">
                        <?php if ($jamaah['fc_kk_path']): ?>
                            <span class="text-success">Uploaded <?= date('d/m/Y H:i', strtotime($jamaah['fc_kk_path'])) ?></span>
                        <?php endif; ?>
                    </div>
                    <input type="file" class="form-control" name="fc_kk" accept=".pdf,.jpg,.jpeg,.png">
                </td>
                <td>
                    <div class="upload-status">
                        <?php if ($jamaah['fc_bk_nikah_path']): ?>
                            <span class="text-success">Uploaded <?= date('d/m/Y H:i', strtotime($jamaah['fc_bk_nikah_path'])) ?></span>
                        <?php endif; ?>
                    </div>
                    <input type="file" class="form-control" name="fc_bk_nikah" accept=".pdf,.jpg,.jpeg,.png">
                </td>
                <td>
                    <div class="upload-status">
                        <?php if ($jamaah['fc_akta_lahir_path']): ?>
                            <span class="text-success">Uploaded <?= date('d/m/Y H:i', strtotime($jamaah['fc_akta_lahir_path'])) ?></span>
                        <?php endif; ?>
                    </div>
                    <input type="file" class="form-control" name="fc_akta_lahir" accept=".pdf,.jpg,.jpeg,.png">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary" 
                            onclick="openDocumentModal('<?= $jamaah['nik'] ?>', '<?= $jamaah['nama'] ?>')">
                        Manage Documents
                    </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endforeach; ?>

<!-- Document Management Modal -->
<div class="modal fade" id="documentModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Management - <span id="jamaahName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="documentForm" enctype="multipart/form-data">
                    <input type="hidden" id="jamaahNik" name="nik">
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <h6>Buku Kuning</h6>
                            <div class="document-actions mb-2">
                                <div class="preview-actions"></div>
                            </div>
                            <input type="file" class="form-control" name="bk_kuning" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="col-md-4">
                            <h6>Foto</h6>
                            <div class="document-actions mb-2">
                                <div class="preview-actions"></div>
                            </div>
                            <input type="file" class="form-control" name="foto" accept=".jpg,.jpeg,.png">
                        </div>
                        <div class="col-md-4">
                            <h6>KTP</h6>
                            <div class="document-actions mb-2">
                                <div class="preview-actions"></div>
                            </div>
                            <input type="file" class="form-control" name="fc_ktp" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <h6>Ijazah</h6>
                            <div class="document-actions mb-2">
                                <div class="preview-actions"></div>
                            </div>
                            <input type="file" class="form-control" name="fc_ijazah" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="col-md-4">
                            <h6>Kartu Keluarga</h6>
                            <div class="document-actions mb-2">
                                <div class="preview-actions"></div>
                            </div>
                            <input type="file" class="form-control" name="fc_kk" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="col-md-4">
                            <h6>Buku Nikah</h6>
                            <div class="document-actions mb-2">
                                <div class="preview-actions"></div>
                            </div>
                            <input type="file" class="form-control" name="fc_bk_nikah" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Akta Kelahiran</h6>
                            <div class="document-actions mb-2">
                                <div class="preview-actions"></div>
                            </div>
                            <input type="file" class="form-control" name="fc_akta_lahir" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
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

<!-- Include file preview modal -->
<?php include 'includes/file_preview_modal.php'; ?>

<script>
function openDocumentModal(nik, nama) {
    document.getElementById('jamaahNik').value = nik;
    document.getElementById('jamaahName').textContent = nama;
    
    // Fetch existing documents
    fetch(`handle_document_upload.php?action=get_documents&nik=${nik}`)
        .then(response => response.json())
        .then(data => {
            // Update preview actions for each document type
            Object.entries(data).forEach(([docType, path]) => {
                if (path) {
                    const docTypeFormatted = docType.replace('fc_', '').replace('_', ' ');
                    const previewSection = document.querySelector(`[name="${docType}"]`).previousElementSibling;
                    previewSection.innerHTML = `
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="handleFile('${path}', 'documents', 'preview')">
                            <i class="bi bi-eye"></i> Preview
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="handleFile('${path}', 'documents', 'download')">
                            <i class="bi bi-download"></i> Download
                        </button>
                    `;
                }
            });
        });
    
    const modal = new bootstrap.Modal(document.getElementById('documentModal'));
    modal.show();
}

function uploadDocuments() {
    const form = document.getElementById('documentForm');
    const formData = new FormData(form);
    formData.append('action', 'upload');
    
    fetch('handle_document_upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Documents uploaded successfully');
            location.reload();
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