<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validador de Archivos Batch de Balance</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Validador de Archivos Batch de Balance</h1>
            <p class="subtitle">Sube tu archivo para validar formato y contenido</p>
        </header>

        <div class="upload-section">
            <form action="validar.php" method="POST" enctype="multipart/form-data">
                <div class="file-input-wrapper">
                    <label for="archivo" class="file-label">
                        <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span class="file-text">Seleccionar archivo .txt</span>
                        <span class="file-name" id="fileName"></span>
                    </label>
                    <input type="file" name="archivo" id="archivo" accept=".txt" required>
                </div>

                <button type="submit" class="btn-validate">
                    Validar Archivo
                </button>
            </form>
        </div>

        <div class="info-section">
            <h3>Formato esperado del archivo</h3>
            <ul>
                <li><strong>Nombre:</strong> TVWXYBZDDMMAAAA.txt</li>
                <li><strong>T y B:</strong> Letras fijas</li>
                <li><strong>VWXY:</strong> Código personalizado (4 caracteres)</li>
                <li><strong>Z:</strong> Código de balance (1, 2, 3 o 4)</li>
                <li><strong>DDMMAAAA:</strong> Fecha (día, mes, año)</li>
            </ul>
            
            <h3>Contenido del archivo</h3>
            <ul>
                <li><strong>Primera línea:</strong> TVWXY [TAB] DD/MM/YYYY [TAB] NumFilas [TAB] TotalMonetario</li>
                <li><strong>Separador:</strong> Tabulador (Tab) en todas las líneas</li>
                <li><strong>Filas útiles:</strong> Deben coincidir con el número declarado</li>
                <li><strong>Sin líneas vacías al final</strong></li>
                <li><strong>Suma de subtotales:</strong> Grupos 1-5 deben sumar el total declarado</li>
            </ul>
        </div>

        <footer>
            <p>Taller 5 - Auditoria de Sistemas | 2025</p>
        </footer>
    </div>

    <script>
        // Mostrar nombre del archivo seleccionado
        document.getElementById('archivo').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || '';
            const fileNameSpan = document.getElementById('fileName');
            const fileText = document.querySelector('.file-text');
            
            if (fileName) {
                fileNameSpan.textContent = fileName;
                fileText.textContent = 'Archivo seleccionado:';
            } else {
                fileNameSpan.textContent = '';
                fileText.textContent = 'Seleccionar archivo .txt';
            }
        });
    </script>
</body>
</html>
