# GEINFTEC S.A.S. - Sitio web institucional

Sitio web moderno de una sola página para GEINFTEC S.A.S., empresa colombiana de ingeniería, construcción y desarrollo de software. Diseño "Deep Tech & Cyber" con animaciones, efectos visuales, panel de administración y conexión a base de datos en la nube (Supabase).

## 🚀 Características

- **Diseño premium**: Paleta de colores oscuros con acentos cian y violeta, tipografía Inter, efectos 3D, parallax, malla digital en el hero.
- **Totalmente responsive**: Mobile-first, adaptable a todos los dispositivos.
- **Contenido dinámico**: Todos los textos, títulos, estadísticas e imágenes se almacenan en una base de datos PostgreSQL (Supabase) y se pueden editar desde el panel de administración.
- **Panel de administración seguro**: Protegido con inicio de sesión (contraseña configurable mediante variable de entorno).
- **Subida de imágenes**: Desde el panel puedes subir imágenes para proyectos, equipo y cualquier otra sección.
- **Efectos visuales**: Contador animado, máquina de escribir en el hero, apariciones con Intersection Observer, barra de progreso de lectura, botón "volver arriba".

## 🛠️ Tecnologías utilizadas

- **Frontend**: HTML5, CSS3, JavaScript (vanilla), Canvas para la malla digital.
- **Backend**: PHP 8.2 con extensión `pgsql` (pg_connect).
- **Base de datos**: PostgreSQL (Supabase).
- **Servidor web**: Apache (contenedor Docker en Render).
- **Control de versiones**: Git + GitHub.

## 📦 Estructura de archivos
geinftec/
├── index.php # Página principal (todas las secciones)
├── admin.php # Panel de administración (protegido)
├── login.php # Formulario de inicio de sesión
├── logout.php # Cierre de sesión
├── config.php # Conexión a la base de datos (Supabase)
├── funciones.php # Funciones para consultar y actualizar contenido
├── style.css # Todos los estilos y efectos visuales
├── script.js # Animaciones, canvas, contadores, etc.
├── Dockerfile # Configuración para contenedor Apache + PHP
├── .htaccess # Configuración de Apache (DirectoryIndex)
├── uploads/ # Carpeta donde se guardan las imágenes subidas
└── README.md # Este archivo

text

## 🗄️ Base de datos (Supabase)

La base de datos tiene una única tabla llamada `contenido` que almacena todo el contenido editable:

| Campo   | Tipo         | Descripción |
|---------|--------------|-------------|
| id      | SERIAL (PK)  | Identificador único |
| seccion | VARCHAR(50)  | Agrupa los datos por sección (ej. 'hero', 'servicios', 'estadisticas') |
| clave   | VARCHAR(50)  | Nombre del campo (ej. 'titulo', 'subtitulo', 'frases', 'anos') |
| valor   | TEXT         | El contenido en sí (texto, número o ruta de imagen) |

### Funciones principales de la BD

- **Edición de textos**: Cambia cualquier título, subtítulo, frase o párrafo desde el panel de administración.
- **Actualización de estadísticas**: Modifica los números de años, proyectos, clientes y satisfacción.
- **Subida de imágenes**: Asocia imágenes a claves que contengan "img" (por ejemplo, `proyectos_img1`, `equipo_img2`). Las imágenes se guardan en la carpeta `uploads/` y la ruta se almacena en la BD.

## 🔐 Panel de administración

### Acceso
- URL: `/login.php`
- Contraseña por defecto: `admin123` (se recomienda cambiarla mediante variable de entorno).

### Capacidades del administrador
- **Editar cualquier texto** del sitio (títulos, subtítulos, frases, copyright, etc.).
- **Subir imágenes** para proyectos, equipo y otras secciones.
- **Actualizar estadísticas** (años, proyectos, clientes, satisfacción).
- **Ver previsualización** de imágenes ya subidas.
- **Cambios instantáneos**: Los cambios se reflejan al instante en la página pública.

### Cómo cambiar la contraseña del admin
1. Opción recomendada: definir una variable de entorno `ADMIN_PASSWORD` en Render con el valor deseado.
2. Alternativa: editar el archivo `login.php` y reemplazar la línea `$admin_pass = getenv('ADMIN_PASSWORD') ?: 'admin123';` por `$admin_pass = 'tu_nueva_contraseña';`.

## 📦 Instalación y configuración

### Requisitos previos
- PHP 8.2 o superior con extensión `pgsql` habilitada.
- Servidor web (Apache, Nginx o Docker).
- Cuenta en Supabase (gratuita) con una base de datos PostgreSQL creada.

### Pasos para instalar localmente

1. **Clona el repositorio**:
   ```bash
   git clone https://github.com/tu-usuario/geinftec.git
   cd geinftec
Crea la base de datos en Supabase:

Ve a Supabase y crea un nuevo proyecto.

En el Editor SQL, ejecuta el script sql/estructura.sql (incluido en el repositorio) para crear la tabla e insertar los datos por defecto.

Obtén la URI de conexión de Supabase:

En tu proyecto de Supabase, ve a Connect > Session pooler > URI.

Copia la cadena completa (ej. postgresql://postgres.[ref]:[password]@aws-0-[region].pooler.supabase.com:5432/postgres).

Configura la conexión en config.php:

Abre config.php y reemplaza la URI por la tuya (o usa variables de entorno).

Si usas variables de entorno, define SUPABASE_URI en tu servidor.

Asegura permisos en la carpeta uploads/:

bash
mkdir uploads
chmod 755 uploads
Inicia el servidor:

bash
php -S localhost:8000
O con Docker (ver más abajo).

Despliegue en Render (recomendado)
Conecta tu repositorio de GitHub a Render.

Crea un Web Service y selecciona el entorno Docker.

Define las variables de entorno necesarias (mínimo SUPABASE_URI y opcionalmente ADMIN_PASSWORD).

Render construirá automáticamente el contenedor usando el Dockerfile y desplegará el sitio.

Variables de entorno en Render
Variable	Obligatoria	Descripción
SUPABASE_URI	Sí	Cadena de conexión completa a Supabase.
ADMIN_PASSWORD	No	Contraseña para el panel de administración (por defecto admin123).
Nota: Puedes eliminar variables antiguas como DB_HOST, DB_USER, DB_PASS, DB_NAME si ya no las usas.

🐳 Uso con Docker
Si prefieres usar Docker localmente, asegúrate de tener instalado Docker y ejecuta:

bash
# Construir la imagen
docker build -t geinftec .

# Ejecutar el contenedor (mapeando el puerto 80)
docker run -p 80:80 -e SUPABASE_URI="postgresql://..." -e ADMIN_PASSWORD="tuclave" geinftec
Luego accede a http://localhost.

📝 Notas adicionales
Los estilos y scripts están separados en style.css y script.js para facilitar el mantenimiento.

El sitio está optimizado para rendimiento: lazy loading de imágenes, efectos con Intersection Observer, y carga asíncrona de recursos.

La malla digital del hero se genera con Canvas y se actualiza en tiempo real.

El formulario de newsletter y el de contacto tienen validación frontend (puedes extender para enviar correos).

🤝 Contribuciones
Si deseas contribuir, por favor haz un fork del repositorio y envía un pull request con tus mejoras.

📄 Licencia
Este proyecto es propiedad de GEINFTEC S.A.S. y se distribuye bajo licencia privada.

Desarrollado con 💙 en Colombia.