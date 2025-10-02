<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users"></i> Lista de Participantes
                </h5>
                <div>
                    <button class="btn btn-success btn-sm" id="generateSelectedBtn" disabled>
                        <i class="fas fa-certificate"></i> Generar Seleccionados
                    </button>
                    <a href="index.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-upload"></i> Subir más datos
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="participantsTable" class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Curso</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargan dinámicamente con AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para vista previa de certificado -->
<div class="modal fade" id="certificateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Certificado Generado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                    <h4>¡Certificado generado exitosamente!</h4>
                    <p id="certificateMessage"></p>
                    <div class="mt-3">
                        <a href="#" id="downloadLink" class="btn btn-primary" target="_blank">
                            <i class="fas fa-download"></i> Descargar Certificado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>