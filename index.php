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

                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
