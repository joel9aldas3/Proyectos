<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-upload"></i> Subir Archivo CSV
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Formato requerido:</strong> El archivo CSV debe contener las columnas: 
                    <em>Nombre, Email, Curso, Fecha</em>
                </div>
                
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="csvFile" class="form-label">Seleccionar archivo CSV:</label>
                        <input type="file" class="form-control" id="csvFile" name="csv_file" accept=".csv" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-cloud-upload-alt"></i> Subir y Procesar
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
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Participantes</h6>
                        <h3 id="totalParticipants">0</h3>
                    </div>
                    <div>
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Certificados Generados</h6>
                        <h3 id="totalCertificates">0</h3>
                    </div>
                    <div>
                        <i class="fas fa-certificate fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Acciones Rápidas</h5>
            </div>
            <div class="card-body">
                <a href="index.php?action=participants" class="btn btn-outline-primary">
                    <i class="fas fa-list"></i> Ver Participantes
                </a>
                <button class="btn btn-outline-success" onclick="generateAllCertificates()">
                    <i class="fas fa-certificate"></i> Generar Todos los Certificados
                </button>
            </div>
        </div>
    </div>
</div>