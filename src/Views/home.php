<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-cloud-upload-fill"></i> Subir Archivo CSV
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong><i class="bi bi-info-circle-fill"></i> Formato requerido:</strong> 
                    El archivo CSV debe contener las columnas: 
                    <em>Nombre, Email, Curso, Fecha</em>
                </div>
                
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="csvFile" class="form-label">
                            <i class="bi bi-file-earmark-spreadsheet-fill"></i> Seleccionar archivo CSV:
                        </label>
                        <input type="file" class="form-control" id="csvFile" name="csv_file" accept=".csv" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload-fill"></i> Subir y Procesar
                    </button>
                </form>
                
                <div id="uploadResults" class="mt-3" style="display: none;">
                    <div class="alert" id="resultAlert"></div>
                    <div id="errorsList"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card text-white shadow" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Total Participantes</h6>
                        <h2 class="mb-0" id="totalParticipants">0</h2>
                    </div>
                    <div>
                        <i class="bi bi-people-fill" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-white shadow" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Certificados Generados</h6>
                        <h2 class="mb-0" id="totalCertificates">0</h2>
                    </div>
                    <div>
                        <i class="bi bi-award-fill" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightning-charge-fill"></i> Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <a href="index.php?action=participants" class="btn btn-primary">
                    <i class="bi bi-list-ul"></i> Ver Participantes
                </a>
            </div>
        </div>
    </div>
</div>