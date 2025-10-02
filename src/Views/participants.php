<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-people-fill"></i> Lista de Participantes
                </h5>
                <div>
                    <button class="btn btn-success btn-sm" id="generateSelectedBtn" disabled>
                        <i class="bi bi-file-earmark-check-fill"></i> Generar Seleccionados
                    </button>
                    <a href="index.php" class="btn btn-primary btn-sm">
                        <i class="bi bi-cloud-upload-fill"></i> Subir más datos
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="participantsTable" class="table table-hover">
                        <thead>
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