# GEINFTEC S.A.S. - Sitio web institucional

Sitio web moderno de una sola página para GEINFTEC S.A.S., empresa colombiana de ingeniería, construcción y desarrollo de software. Diseño "Deep Tech & Cyber" con animaciones, efectos visuales y panel de administración para gestionar el contenido dinámicamente.

## 🚀 Características

- **Diseño premium**: Paleta de colores oscuros con acentos cian y violeta, tipografía Inter, efectos 3D, parallax, malla digital en el hero.
- **Totalmente responsive**: Mobile-first, adaptable a todos los dispositivos.
- **Contenido dinámico**: Todos los textos, títulos y estadísticas se almacenan en una base de datos PostgreSQL y se pueden editar desde el panel de administración.
- **Panel de administración seguro**: Protegido con inicio de sesión (contraseña por defecto: `admin123`). Permite:
  - Editar cualquier texto del sitio (títulos, subtítulos, frases, estadísticas, copyright, etc.).
  - Subir imágenes (se guardan en la carpeta `uploads/`).
  - Actualizar estadísticas numéricas (años, proyectos, clientes, satisfacción).
- **Efectos visuales**: Contador animado, máquina de escribir en el hero, apariciones con Intersection Observer, barra de progreso de lectura, botón "volver arriba".

## 🛠️ Tecnologías utilizadas

- **Frontend**: HTML5, CSS3, JavaScript (vanilla), Canvas para la malla digital.
- **Backend**: PHP 8.2 con PDO para conexión a PostgreSQL.
- **Base de datos**: PostgreSQL (en Render).
- **Servidor web**: Apache (contenedor Docker).

## 📦 Instalación y configuración

### Requisitos previos

- PHP 8.2 o superior
- PostgreSQL (o MySQL, pero el código está adaptado a PostgreSQL)
- Servidor web (Apache o Nginx) o Docker

### Pasos para instalar localmente

1. **Clona el repositorio**:
   ```bash
   git clone https://github.com/tu-usuario/geinftec.git
   cd geinftec
Crea la base de datos en PostgreSQL:

sql
CREATE DATABASE geinftec_db;
Ejecuta el script SQL para crear la tabla e insertar los datos por defecto:

bash
psql -U tu_usuario -d geinftec_db -f sql/estructura.sql
Configura las variables de entorno (o edita config.php directamente):

DB_HOST: localhost

DB_PORT: 5432

DB_NAME: geinftec_db

DB_USER: tu_usuario

DB_PASS: tu_contraseña

Asegúrate de que la carpeta uploads/ tenga permisos de escritura:

bash
mkdir uploads
chmod 755 uploads
Inicia el servidor (puedes usar el servidor integrado de PHP para pruebas):

bash
php -S localhost:8000
O si usas Docker, construye la imagen y ejecuta el contenedor:

bash
docker build -t geinftec .
docker run -p 80:80 -e DB_HOST=... -e DB_USER=... -e DB_PASS=... -e DB_NAME=... geinftec
Accede al sitio en http://localhost:8000 (o la URL que corresponda).

Despliegue en Render
Conecta tu repositorio de GitHub a Render.

Crea un Web Service y selecciona el entorno Docker.

Define las variables de entorno (DB_HOST, DB_USER, DB_PASS, DB_NAME) con los valores de tu base de datos PostgreSQL en Render.

Render construirá automáticamente el contenedor usando el Dockerfile y desplegará el sitio.

🗄️ Relación con la base de datos
El sitio utiliza una única tabla llamada contenido con la siguiente estructura:

Campo	Tipo	Descripción
id	SERIAL (PK)	Identificador único
seccion	VARCHAR(50)	Agrupa los datos por sección (ej. 'hero')
clave	VARCHAR(50)	Identifica el dato (ej. 'titulo')
valor	TEXT	El contenido en sí (texto o URL de imagen)
Cada sección del sitio (hero, servicios, proyectos, etc.) tiene un conjunto de claves que se utilizan para mostrar el contenido. La función getContent($seccion, $clave, $default) obtiene el valor de la BD o devuelve el valor por defecto si no existe.

Funciones disponibles gracias a la BD
Edición de textos: Cambia cualquier título, subtítulo, frase o párrafo desde el panel de administración.

Actualización de estadísticas: Modifica los números que se muestran en la sección de estadísticas.

Subida de imágenes: Asocia imágenes a claves específicas (por ejemplo, para el logo o fondos) y se almacena la ruta en la BD.

Flexibilidad total: Puedes añadir nuevas claves para nuevas secciones sin tocar el código PHP (solo insertando nuevas filas en la tabla).

👤 Panel de administración
El panel de administración está disponible en /admin.php y requiere autenticación.

Usuario: (no se usa usuario, solo contraseña)

Contraseña por defecto: admin123

Puedes cambiar la contraseña generando un nuevo hash con password_hash('tu_nueva_contraseña', PASSWORD_DEFAULT) y reemplazando el valor de ADMIN_PASSWORD_HASH en config.php.

Capacidades del administrador
Ver todas las claves agrupadas por sección.

Editar cualquier valor mediante un formulario simple (campo de texto o textarea).

Subir imágenes para claves que contengan 'imagen', 'img' o 'foto' en su nombre.

Visualización previa de las imágenes ya subidas.

Actualización instantánea: Los cambios se reflejan automáticamente en el sitio público.

📝 Notas adicionales
Los estilos y scripts están completamente separados en style.css y script.js para facilitar el mantenimiento.

El sitio está optimizado para rendimiento: lazy loading de imágenes, efectos con Intersection Observer, y carga asíncrona de recursos.

El diseño original se ha mantenido intacto, recuperando todos los efectos visuales del prototipo inicial.

🤝 Contribuciones
Si deseas contribuir, por favor haz un fork del repositorio y envía un pull request con tus mejoras.

📄 Licencia
Este proyecto es propiedad de GEINFTEC S.A.S. y se distribuye bajo licencia privada.

Desarrollado con 💙 en Colombia.
