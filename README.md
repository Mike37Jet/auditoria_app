# 🏦 Validador de Archivos Batch de Balance

[![PHP](https://img.shields.io/badge/PHP-7.4+-blue.svg)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Aplicación web PHP profesional para validar archivos batch de balance según especificaciones estrictas de formato y contenido. Desarrollada para el **Taller 5 - Auditoría de Sistemas**.

## ✨ Características

- ✅ Validación completa de formato de nombre de archivo
- ✅ Verificación de estructura y contenido del archivo
- ✅ Validación de suma de subtotales por grupos
- ✅ Interfaz moderna y responsiva con Bootstrap 5
- ✅ Mensajes de error claros y específicos
- ✅ Feedback visual intuitivo
- ✅ Compatible con archivos de balance tipos 1, 2, 3 y 4

## 🚀 Instalación en Laragon

### Requisitos Previos
- **Laragon** instalado ([Descargar aquí](https://laragon.org/download/))
- PHP 7.4 o superior (incluido en Laragon)
- Navegador web moderno

### Pasos de Instalación

1. **Copiar el proyecto a Laragon**
   ```
   Copiar la carpeta "taller 5_auditoria" a:
   C:\laragon\www\
   ```

2. **Iniciar Laragon**
   - Abrir Laragon
   - Click en "Start All" (Apache y MySQL se iniciarán)

3. **Acceder a la aplicación**
   - Abrir navegador web
   - Ir a: `http://localhost/taller%205_auditoria/`
   - O: `http://taller5auditoria.test/` (si Laragon configuro virtual host automaticamente)

## Formato del Archivo

### Nombre del Archivo
El archivo debe seguir el formato: **TVWXYBZDDMMAAAA.txt**

- **T**: Letra fija (siempre 'T')
- **VWXY**: Código personalizado de 4 caracteres alfanuméricos
- **B**: Letra fija (siempre 'B')
- **Z**: Código de balance (debe ser 1, 2, 3 o 4)
- **DDMMAAAA**: Fecha (día, mes, año)

**Ejemplos validos:**
- `TABCDB121102025.txt` - Codigo TABCD, Balance 1, Fecha 21/10/2025
- `TXYZ1B315102025.txt` - Codigo TXYZ1, Balance 3, Fecha 15/10/2025

### Contenido del Archivo

#### Primera Línea (Header)
Debe contener exactamente 4 valores separados por **tabulador**:

```
TVWXY[TAB]DD/MM/YYYY[TAB]NumeroFilas[TAB]TotalMonetario
```

**Ejemplo:**
```
TABCD	21/10/2025	10	15000.00
```

#### Líneas de Datos
- Separadas por **tabulador** (Tab, no espacios)
- Primera columna: Grupo (1-5)
- Ultima columna: Monto
- No debe haber lineas vacias al final

**Ejemplo:**
```
1	Activo Corriente	Efectivo	5000.00
2	Pasivo Corriente	Proveedores	3000.00
```

## Validaciones Implementadas

### 1. Nombre del Archivo
- OK Formato TVWXYBZDDMMAAAA.txt
- OK Letra 'T' al inicio
- OK Letra 'B' antes del codigo de balance
- OK Codigo de balance entre 1-4
- OK Extension .txt

### 2. Primera Linea
- OK Exactamente 4 valores separados por tabulador
- OK Codigo TVWXY coincide con nombre del archivo
- OK Fecha en formato DD/MM/YYYY valida
- OK Numero de filas es numerico
- OK Total monetario es numerico

### 3. Filas Utiles
- OK Numero de lineas no vacias coincide con el declarado
- OK No hay lineas vacias al final del archivo

### 4. Separadores
- OK Todas las lineas usan tabulador como separador

### 5. Suma de Subtotales
- OK Subtotales de grupos 1-5 suman el total declarado
- OK Muestra desglose por grupo

## Archivos de Ejemplo

En la carpeta `ejemplos/` encontraras:

### Archivos Validos
- **TABCDB121102025.txt** - Archivo valido con 10 filas
- **TXYZ1B315102025.txt** - Archivo valido con 5 filas

### Archivos con Errores (para pruebas)
- **ERROR_NombreIncorrecto.txt** - Nombre no cumple formato
- **TABCDB221102025.txt** - Linea vacia al final
- **TABCDB321102025.txt** - Separador incorrecto (punto y coma en lugar de tabulador)
- **TABCDB421102025.txt** - Suma de subtotales incorrecta
- **TQWERB132132025.txt** - Fecha invalida (32/13/2025)

## Pruebas

### Probar Archivo Valido
1. Ir a `http://localhost/taller%205_auditoria/`
2. Seleccionar `ejemplos/TABCDB121102025.txt`
3. Click en "Validar Archivo"
4. Debe mostrar: **"Archivo Valido"**

### Probar Archivos con Errores
Repetir el proceso con cada archivo de error para verificar que:
- Se detecta el error especifico
- Se muestra mensaje claro del problema
- El archivo es rechazado

## Estructura del Proyecto

```
taller 5_auditoria/
│
├── index.php          # Interfaz principal
├── validar.php        # Logica de validacion
├── styles.css         # Estilos de la aplicacion
├── README.md          # Este archivo
│
└── ejemplos/          # Archivos de prueba
    ├── TABCDB121102025.txt (valido)
    ├── TXYZ1B315102025.txt (valido)
    ├── ERROR_NombreIncorrecto.txt
    ├── TABCDB221102025.txt
    ├── TABCDB321102025.txt
    ├── TABCDB421102025.txt
    └── TQWERB132132025.txt
```

## Caracteristicas

- Interfaz moderna y responsiva
- Compatible con moviles
- Validaciones exhaustivas
- Detalles completos de validacion
- Mensajes de error especificos
- Desglose de subtotales por grupo

## Tecnologias

- **PHP** 7.4+
- **HTML5**
- **CSS3**
- **JavaScript** (Vanilla)

## Soporte

Para dudas o problemas:
1. Verificar que Laragon este iniciado
2. Revisar que el archivo tenga formato correcto
3. Consultar ejemplos en la carpeta `ejemplos/`

## Notas Importantes

- El separador **DEBE** ser tabulador (Tab), no espacios
- Las fechas deben ser validas (no puede ser 32/13/2025)
- El codigo de balance solo acepta 1, 2, 3 o 4
- No debe haber lineas vacias al final del archivo
- La suma de subtotales debe coincidir exactamente con el total declarado

---

**Taller 5 - Auditoria de Sistemas | 2025**
