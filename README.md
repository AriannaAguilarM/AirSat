# 🌎 AirSat - Sistema Web para Monitoreo y Gestión de Lanzamientos CanSat

Este proyecto implementa un sistema web desarrollado en *CodeIgniter 4* que permite *visualizar en tiempo real los datos enviados por un CanSat, así como **gestionar los lanzamientos y almacenar los registros obtenidos durante cada uno*.  
El sistema se conecta a una base de datos local llamada airsat, que contiene las lecturas capturadas por el CanSat y la información de los lanzamientos.

---

## 🚀 Funcionalidades principales

### 🛰 1. Monitoreo en tiempo real
- Muestra *el último dato registrado* en la tabla Lecturas con todos sus parámetros ambientales y de movimiento.
- Actualización automática mediante *AJAX* sin recargar la página.
- Visualización en *gráficas interactivas (Chart.js)* que se actualizan constantemente:
  - Temperatura, humedad, presión, AQI, TVOC, eCO2, PM1, PM2.5, PM10.
  - Aceleraciones en los tres ejes (AX, AY, AZ).
  - Giroscopio en los tres ejes (GX, GY, GZ).

### 🚀 2. Gestión de lanzamientos
- Permite *iniciar* y *finalizar* lanzamientos desde la interfaz web.
- Al iniciar un lanzamiento se registra:
  - Fecha y hora de inicio.
  - Descripción del lanzamiento.
  - Lugar de captura.  
- Al finalizar el lanzamiento:
  - Se guarda la fecha y hora de finalización.
  - Se asocian automáticamente todas las lecturas obtenidas durante ese intervalo mediante la tabla Conexion.

### 📊 3. Histórico y exportación
- Consulta de todos los lanzamientos registrados.
- Visualización de las lecturas asociadas a cada lanzamiento.
- Posibilidad de *exportar un lanzamiento individual o varios* al mismo tiempo en formato *PDF*, utilizando la librería dompdf.

---

## 🧩 Estructura de la base de datos

La aplicación utiliza una base de datos MySQL local llamada airsat, con las siguientes tablas:

```sql
USE airsat;

CREATE TABLE Lecturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    temperatura FLOAT,
    humedad FLOAT,
    presion_atmosferica FLOAT,
    altura_absoluta FLOAT,
    altura_relativa FLOAT,
    AQI FLOAT,
    TVOC FLOAT,
    eCO2 FLOAT,
    PM1 FLOAT,
    PM2_5 FLOAT,
    PM10 FLOAT,
    AX FLOAT,
    AY FLOAT,
    AZ FLOAT,
    GX FLOAT,
    GY FLOAT,
    GZ FLOAT,
    fecha_hora DATETIME
);

CREATE TABLE Lanzamiento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_hora_inicio DATETIME,
    fecha_hora_final DATETIME,
    descripcion VARCHAR(255),
    lugar_captura VARCHAR(255)
);

CREATE TABLE Conexion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_lecturas INT,
    id_lanzamiento INT,
    FOREIGN KEY (id_lecturas) REFERENCES Lecturas(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (id_lanzamiento) REFERENCES Lanzamiento(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
