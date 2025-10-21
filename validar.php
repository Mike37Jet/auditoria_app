<?php
/**
 * Validador de Archivos Batch de Balance
 * Taller 5 - Auditoría de Sistemas
 */

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
        
        // Convertir saltos de línea Windows a Unix si es necesario
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
            
            // Separar por tabulador
            $valores = explode("\t", $primeraLinea);
            
            if (count($valores) !== 4) {
                $errores[] = "ERROR: La primera linea debe contener exactamente 4 valores separados por tabulador (encontrados: " . count($valores) . ")";
            } else {
                $tvwxy = trim($valores[0]);
                $fechaCorte = trim($valores[1]);
                $numFilasDeclarado = trim($valores[2]);
                $totalMonetarioDeclarado = trim($valores[3]);
                
                // Validar TVWXY (debe coincidir con las primeras 5 letras del nombre)
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
                    $lineaConError = $index + 2; // +2 porque empezamos desde línea 2 (índice 0 es línea 2)
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
                
                // Asumiendo que el grupo está en la primera columna y el monto en alguna columna
                // Necesitamos definir estructura. Asumiremos: Grupo[TAB]...valores...[TAB]Monto
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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Resultado de Validacion</h1>
        </header>

        <div class="result-section <?php echo $archivoValido ? 'success' : 'error'; ?>">
            <?php if ($archivoValido): ?>
                <div class="result-icon success-icon">OK</div>
                <h2 class="result-title">Archivo Valido</h2>
                <p class="result-message">El archivo cumple con todos los requisitos de formato y contenido.</p>
            <?php else: ?>
                <div class="result-icon error-icon">X</div>
                <h2 class="result-title">Archivo Rechazado</h2>
                <p class="result-message">Se encontraron los siguientes errores:</p>
                
                <div class="error-list">
                    <?php foreach ($errores as $error): ?>
                        <div class="error-item"><?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($detalles)): ?>
            <div class="details-section">
                <h3>Detalles de la Validacion</h3>
                <?php foreach ($detalles as $detalle): ?>
                    <div class="detail-item">
                        <h4><?php echo htmlspecialchars($detalle['titulo']); ?></h4>
                        <p><?php echo $detalle['descripcion']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="actions">
            <a href="index.php" class="btn-back">Validar otro archivo</a>
        </div>

        <footer>
            <p>Taller 5 - Auditoria de Sistemas | 2025</p>
        </footer>
    </div>
</body>
</html>
