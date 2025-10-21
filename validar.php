<?php
// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Variables para resultados
$errores = [];
$advertencias = [];
$detalles = [];
$archivoValido = false;

// Verificar si se subió un archivo
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['archivo'])) {
    header('Location: index.php');
    exit();
}

$archivo = $_FILES['archivo'];

// Verificar errores de subida
if ($archivo['error'] !== UPLOAD_ERR_OK) {
    $errores[] = "Error al subir el archivo. Código de error: " . $archivo['error'];
} else {
    $nombreArchivo = $archivo['name'];
    $rutaTemporal = $archivo['tmp_name'];
    
    // ==========================================
    // VALIDACIÓN 1: NOMBRE DEL ARCHIVO
    // ==========================================
    $detalles[] = [
        'titulo' => 'Validacion de Nombre de Archivo',
        'descripcion' => "Archivo: <strong>$nombreArchivo</strong>"
    ];
    
    $patron = '/^T[A-Z0-9]{4}B[1-4]\d{8}\.txt$/i';
    
    if (!preg_match($patron, $nombreArchivo)) {
        $errores[] = "ERROR: El nombre del archivo no cumple con el formato TVWXYBZDDMMAAAA.txt";
        
        if (!preg_match('/^T/', $nombreArchivo)) {
            $errores[] = "   - La primera letra debe ser 'T'";
        }
        if (!preg_match('/B[1-4]/', $nombreArchivo)) {
            $errores[] = "   - Debe contener 'B' seguida del código de balance (1, 2, 3 o 4)";
        }
        if (!preg_match('/\.txt$/i', $nombreArchivo)) {
            $errores[] = "   - La extensión debe ser .txt";
        }
    } else {
        preg_match('/B([1-4])/', $nombreArchivo, $matches);
        $codigoBalance = $matches[1];
        $detalles[] = [
            'titulo' => 'OK - Nombre valido',
            'descripcion' => "Código de balance: <strong>$codigoBalance</strong>"
        ];
    }
    
    // ==========================================
    // LEER CONTENIDO DEL ARCHIVO
    // ==========================================
    if (empty($errores)) {
        $contenido = file_get_contents($rutaTemporal);
        $lineas = explode("\n", $contenido);
        
        if (strpos($contenido, "\r\n") !== false) {
            $lineas = explode("\r\n", $contenido);
        }
        
        // ==========================================
        // VALIDACIÓN 2: PRIMERA LÍNEA
        // ==========================================
        if (empty($lineas)) {
            $errores[] = "ERROR: El archivo esta vacio";
        } else {
            $primeraLinea = $lineas[0];
            $detalles[] = [
                'titulo' => 'Primera Línea del Archivo',
                'descripcion' => "<code>" . htmlspecialchars($primeraLinea) . "</code>"
            ];
            
            $valores = explode("\t", $primeraLinea);
            
            if (count($valores) !== 4) {
                $errores[] = "ERROR: La primera linea debe contener exactamente 4 valores separados por tabulador (encontrados: " . count($valores) . ")";
            } else {
                $tvwxy = trim($valores[0]);
                $fechaCorte = trim($valores[1]);
                $numFilasDeclarado = trim($valores[2]);
                $totalMonetarioDeclarado = trim($valores[3]);
                
                // Validar TVWXY
                $tvwxyEsperado = substr($nombreArchivo, 0, 5);
                if ($tvwxy !== $tvwxyEsperado) {
                    $errores[] = "ERROR: El codigo TVWXY ('$tvwxy') no coincide con el nombre del archivo ('$tvwxyEsperado')";
                }
                
                // Validar formato de fecha DD/MM/YYYY
                if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fechaCorte)) {
                    $errores[] = "ERROR: La fecha de corte debe estar en formato DD/MM/YYYY (encontrado: '$fechaCorte')";
                } else {
                    // Validar que sea una fecha válida
                    $partesFecha = explode('/', $fechaCorte);
                    if (!checkdate($partesFecha[1], $partesFecha[0], $partesFecha[2])) {
                        $errores[] = "ERROR: La fecha '$fechaCorte' no es valida";
                    }
                }
                
                // Validar que numFilas sea numérico
                if (!is_numeric($numFilasDeclarado) || $numFilasDeclarado < 0) {
                    $errores[] = "ERROR: El numero de filas debe ser un numero entero positivo (encontrado: '$numFilasDeclarado')";
                } else {
                    $numFilasDeclarado = intval($numFilasDeclarado);
                }
                
                // Validar que total monetario sea numérico
                if (!is_numeric($totalMonetarioDeclarado)) {
                    $errores[] = "ERROR: El total monetario debe ser un numero (encontrado: '$totalMonetarioDeclarado')";
                } else {
                    $totalMonetarioDeclarado = floatval($totalMonetarioDeclarado);
                }
                
                if (empty($errores)) {
                    $detalles[] = [
                        'titulo' => 'OK - Primera linea valida',
                        'descripcion' => "
                            <strong>TVWXY:</strong> $tvwxy<br>
                            <strong>Fecha de corte:</strong> $fechaCorte<br>
                            <strong>Número de filas:</strong> $numFilasDeclarado<br>
                            <strong>Total monetario:</strong> " . number_format($totalMonetarioDeclarado, 2, ',', '.')
                    ];
                }
            }
        }
        
        // ==========================================
        // VALIDACIÓN 3: FILAS ÚTILES Y LÍNEAS VACÍAS
        // ==========================================
        if (empty($errores)) {
            // Eliminar la primera línea para contar solo filas útiles
            $lineasUtiles = array_slice($lineas, 1);
            
            // Verificar si la última línea está vacía
            $ultimaLinea = end($lineasUtiles);
            if (trim($ultimaLinea) === '') {
                $errores[] = "ERROR: La ultima linea del archivo esta vacia. El numero de filas utiles no es correcto.";
            }
            
            // Contar líneas no vacías
            $filasNoVacias = array_filter($lineasUtiles, function($linea) {
                return trim($linea) !== '';
            });
            
            $numFilasReales = count($filasNoVacias);
            
            $detalles[] = [
                'titulo' => 'Conteo de Filas Utiles',
                'descripcion' => "
                    <strong>Declarado:</strong> $numFilasDeclarado<br>
                    <strong>Real:</strong> $numFilasReales
                "
            ];
            
            if ($numFilasReales !== $numFilasDeclarado) {
                $errores[] = "ERROR: El numero de filas utiles ($numFilasReales) no coincide con el declarado ($numFilasDeclarado)";
            }
        }
        
        // ==========================================
        // VALIDACIÓN 4: SEPARADOR (TABULADOR)
        // ==========================================
        if (empty($errores)) {
            $lineaConError = null;
            foreach ($filasNoVacias as $index => $linea) {
                if (strpos($linea, "\t") === false && trim($linea) !== '') {
                    $lineaConError = $index + 2;
                    break;
                }
            }
            
            if ($lineaConError !== null) {
                $errores[] = "ERROR: La linea $lineaConError no usa tabulador como separador";
            } else {
                $detalles[] = [
                    'titulo' => 'OK - Separadores validos',
                    'descripcion' => 'Todas las lineas usan tabulador correctamente'
                ];
            }
        }
        
        // ==========================================
        // VALIDACIÓN 5: SUMA DE SUBTOTALES
        // ==========================================
        if (empty($errores)) {
            $subtotalesGrupos = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
            
            foreach ($filasNoVacias as $linea) {
                $columnas = explode("\t", $linea);
                
                if (count($columnas) >= 2) {
                    $grupo = intval(trim($columnas[0]));
                    
                    // El monto está en la última columna
                    $monto = floatval(trim($columnas[count($columnas) - 1]));
                    
                    if ($grupo >= 1 && $grupo <= 5) {
                        $subtotalesGrupos[$grupo] += $monto;
                    }
                }
            }
            
            $sumaSubtotales = array_sum($subtotalesGrupos);
            
            $detallesSubtotales = "<strong>Subtotales por grupo:</strong><br>";
            foreach ($subtotalesGrupos as $grupo => $subtotal) {
                $detallesSubtotales .= "Grupo $grupo: " . number_format($subtotal, 2, ',', '.') . "<br>";
            }
            $detallesSubtotales .= "<br><strong>Suma total:</strong> " . number_format($sumaSubtotales, 2, ',', '.');
            $detallesSubtotales .= "<br><strong>Total declarado:</strong> " . number_format($totalMonetarioDeclarado, 2, ',', '.');
            
            $detalles[] = [
                'titulo' => 'Validacion de Subtotales',
                'descripcion' => $detallesSubtotales
            ];
            
            // Comparar con tolerancia para decimales
            $diferencia = abs($sumaSubtotales - $totalMonetarioDeclarado);
            if ($diferencia > 0.01) {
                $errores[] = "ERROR: La suma de subtotales (" . number_format($sumaSubtotales, 2, ',', '.') . 
                           ") no coincide con el total declarado (" . number_format($totalMonetarioDeclarado, 2, ',', '.') . ")";
                $errores[] = "   - Diferencia: " . number_format($diferencia, 2, ',', '.');
            } else {
                $detalles[] = [
                    'titulo' => 'OK - Suma de subtotales correcta',
                    'descripcion' => 'Los subtotales coinciden con el total declarado'
                ];
            }
        }
    }
}

// Determinar si el archivo es válido
$archivoValido = empty($errores);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de Validación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="text-center mb-4">Resultado de Validación</h1>

                <?php if ($archivoValido): ?>
                    <div class="alert alert-success text-center">
                        <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                        <h2 class="mt-2">Archivo Válido</h2>
                        <p>El archivo cumple con todos los requisitos de formato y contenido.</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <div class="text-center">
                            <i class="bi bi-x-circle-fill fs-1 text-danger"></i>
                            <h2 class="mt-2">Archivo Rechazado</h2>
                            <p>Se encontraron los siguientes errores:</p>
                        </div>
                        
                        <ul class="list-unstyled mt-3">
                            <?php foreach ($errores as $error): ?>
                                <li class="mb-1"><i class="bi bi-exclamation-circle text-warning"></i> <?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($detalles)): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Detalles de la Validación</h3>
                        </div>
                        <div class="card-body">
                            <?php foreach ($detalles as $detalle): ?>
                                <div class="mb-3">
                                    <h5 class="text-primary"><?php echo htmlspecialchars($detalle['titulo']); ?></h5>
                                    <div class="text-muted"><?php echo $detalle['descripcion']; ?></div>
                                </div>
                                <?php if ($detalle !== end($detalles)): ?>
                                    <hr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Validar otro archivo
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
