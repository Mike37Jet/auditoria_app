<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validador de Archivos Batch de Balance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center mb-4">
                    <h1 class="display-5">Validador de Archivos Batch</h1>
                    <p class="text-muted">Sube tu archivo para validar formato y contenido</p>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form action="validar.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label for="archivo" class="form-label">
                                    <i class="bi bi-file-earmark-text"></i> Archivo .txt
                                </label>
                                <input type="file" name="archivo" id="archivo" class="form-control" accept=".txt" required>
                                <div class="form-text">Selecciona un archivo con formato TVWXYBZDDMMAAAA.txt</div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle"></i> Validar Archivo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-file-text"></i> Formato del Nombre</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>TVWXYBZDDMMAAAA.txt</strong></p>
                                <ul class="list-unstyled small">
                                    <li><i class="bi bi-dot"></i> <strong>T y B:</strong> Letras fijas</li>
                                    <li><i class="bi bi-dot"></i> <strong>VWXY:</strong> Código personalizado (4 caracteres)</li>
                                    <li><i class="bi bi-dot"></i> <strong>Z:</strong> Código de balance (1-4)</li>
                                    <li><i class="bi bi-dot"></i> <strong>DDMMAAAA:</strong> Fecha</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-list-ul"></i> Contenido del Archivo</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled small">
                                    <li><i class="bi bi-dot"></i> Primera línea: TVWXY [TAB] DD/MM/YYYY [TAB] NumFilas [TAB] Total</li>
                                    <li><i class="bi bi-dot"></i> Separador: Tabulador en todas las líneas</li>
                                    <li><i class="bi bi-dot"></i> Filas útiles deben coincidir</li>
                                    <li><i class="bi bi-dot"></i> Sin líneas vacías al final</li>
                                    <li><i class="bi bi-dot"></i> Suma de subtotales correcta</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <footer class="text-center mt-5 text-muted">
                    <small>Taller 5 - Auditoría de Sistemas | 2025</small>
                </footer>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
