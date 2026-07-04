# GEINFTEC S.A.S. - Sitio web institucional

Sitio web moderno de una sola página para **GEINFTEC S.A.S.**, empresa colombiana de ingeniería, construcción y desarrollo de software. Diseño "Deep Tech & Cyber" con animaciones, efectos visuales y un panel de administración completo para gestionar el contenido dinámicamente.

---

## 🚀 Características principales

- **Diseño premium**: Paleta de colores oscuros con acentos cian y violeta, tipografía **Inter**, efectos 3D, parallax, malla digital en el hero.
- **Totalmente responsive**: Mobile-first, adaptable a todos los dispositivos.
- **Contenido dinámico**: Todos los textos, títulos, estadísticas e imágenes se almacenan en **Supabase** (PostgreSQL + Storage).
- **Panel de administración seguro**: Protegido con login, CSRF, rate limiting y sesiones seguras.
- **Gestión de imágenes**: Subida de imágenes a **Supabase Storage** con persistencia y URLs públicas.
- **Efectos visuales**: Contador animado, máquina de escribir en el hero, apariciones con Intersection Observer, barra de progreso de lectura, botón "volver arriba".
- **Seguridad avanzada**: CSRF, rate limiting, cabeceras de seguridad, validación de archivos, sesiones con expiración.

---

## 🛠️ Tecnologías utilizadas

| Área | Tecnología |
|------|------------|
| **Frontend** | HTML5, CSS3, JavaScript (vanilla), Canvas |
| **Backend** | PHP 8.2 |
| **Base de datos** | PostgreSQL (Supabase) |
| **Almacenamiento** | Supabase Storage |
| **Servidor web** | Apache (contenedor Docker) |
| **Hosting** | Render.com |
| **Seguridad** | CSRF, rate limiting, cabeceras HTTP |

---

## 📦 Estructura del proyecto
/
├── index.php # Página principal (pública)
├── admin.php # Panel de administración
├── login.php # Página de inicio de sesión
├── logout.php # Cierre de sesión
├── config.php # Configuración (BD, Storage, seguridad)
├── funciones.php # Funciones auxiliares (CRUD, seguridad, subida)
├── style.css # Estilos completos del sitio
├── script.js # JavaScript (animaciones, efectos, formularios)
├── Dockerfile # Configuración para contenedor en Render
├── .htaccess # Configuración de Apache
└── README.md # Este archivo

text

---

## 🗄️ Base de datos (Supabase)

### Tabla: `contenido`
Almacena todo el contenido editable del sitio.

| Campo   | Tipo         | Descripción |
|---------|--------------|-------------|
| id      | SERIAL (PK)  | Identificador único |
| seccion | VARCHAR(50)  | Agrupa los datos por sección (ej. 'hero', 'servicios') |
| clave   | VARCHAR(50)  | Nombre del campo (ej. 'titulo', 'subtitulo', 'frases') |
| valor   | TEXT         | El contenido en sí (texto, número o URL de imagen) |

### Tabla: `login_attempts`
Controla los intentos de inicio de sesión para rate limiting.

| Campo       | Tipo         | Descripción |
|-------------|--------------|-------------|
| ip          | VARCHAR(45)  | Dirección IP del usuario |
| attempts    | INTEGER      | Número de intentos fallidos |
| last_attempt| TIMESTAMP    | Fecha y hora del último intento |

---

## 🔐 Seguridad implementada

| Medida | Descripción |
|--------|-------------|
| **CSRF** | Tokens en todos los formularios POST del panel de administración. |
| **Rate limiting** | 5 intentos fallidos de login = bloqueo de 15 minutos por IP. |
| **Sesiones seguras** | Cookies con `HttpOnly`, `Secure`, `SameSite=Strict`, expiración automática. |
| **Validación de archivos** | Tipo MIME real, tamaño máximo (5 MB), sanitización de nombres. |
| **Cabeceras HTTP** | `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, `Content-Security-Policy`. |
| **Contraseña admin** | Almacenada en variable de entorno (`ADMIN_PASSWORD`), no en código. |
| **Regeneración de sesión** | Después de login exitoso. |

---

## ⚙️ Configuración y variables de entorno

Define estas variables en tu plataforma de hosting (Render, por ejemplo):

| Variable | Descripción |
|----------|-------------|
| `SUPABASE_URI` | Cadena de conexión completa a PostgreSQL (ej. `postgresql://...`) |
| `SUPABASE_URL` | URL de tu proyecto Supabase (ej. `https://xxxxx.supabase.co`) |
| `SUPABASE_ANON_KEY` | Clave anónima pública de Supabase |
| `ADMIN_PASSWORD` | Contraseña para acceder al panel de administración |

---

## 🚀 Despliegue en Render

### Requisitos previos
- Cuenta en [Render.com](https://render.com)
- Repositorio en GitHub con el código del proyecto
- Cuenta en [Supabase](https://supabase.com) con base de datos y bucket de Storage creados

### Pasos

1. **Crear la base de datos en Supabase**
   - Crea un proyecto en Supabase.
   - Ejecuta el SQL para crear las tablas `contenido` y `login_attempts` (puedes usar el SQL Editor).

2. **Crear el bucket de Storage**
   - En Supabase, ve a **Storage** y crea un bucket público (ej. `geinftec`).
   - Configura las políticas para permitir `INSERT` y `UPDATE` (opcionalmente elimina `SELECT` para mayor privacidad).

3. **Configurar el Web Service en Render**
   - Conecta tu repositorio de GitHub.
   - Selecciona **Docker** como entorno.
   - Define las variables de entorno (las 4 mencionadas arriba).
   - El `Dockerfile` incluido instalará las extensiones necesarias y configurará Apache.

4. **Desplegar**
   - Haz clic en **"Deploy"**.
   - Render construirá el contenedor y publicará el sitio.

### Comandos útiles para desarrollo local

```bash
# Clonar el repositorio
git clone https://github.com/tu-usuario/geinftec.git
cd geinftec

# Ejecutar con el servidor integrado de PHP (para pruebas)
php -S localhost:8000

# O con Docker (requiere construir la imagen)
docker build -t geinftec .
docker run -p 80:80 -e SUPABASE_URI=... -e SUPABASE_URL=... -e SUPABASE_ANON_KEY=... -e ADMIN_PASSWORD=... geinftec
🖥️ Uso del panel de administración
Accede a https://tudominio.com/login.php

Introduce la contraseña definida en ADMIN_PASSWORD (por defecto: admin123 si no está definida).

Una vez dentro, podrás:

Editar cualquier texto, título o estadística del sitio.

Subir imágenes (se guardan en Supabase Storage y se almacena la URL en la BD).

Ver previsualización de las imágenes ya subidas.

Actualizar cualquier campo con un solo clic.

📝 Notas adicionales
Las imágenes subidas al panel se almacenan en Supabase Storage, lo que garantiza persistencia incluso al reiniciar el contenedor de Render.

El sitio es completamente responsive y se adapta a móviles, tablets y escritorio.

Todos los textos del sitio son editables desde el panel de administración sin necesidad de tocar código.

La página de login incluye un enlace para volver al sitio público.

🤝 Contribuciones
Si deseas contribuir, por favor haz un fork del repositorio y envía un pull request con tus mejoras.

📄 Licencia
Este proyecto es propiedad de GEINFTEC S.A.S. y se distribuye bajo licencia privada.

Desarrollado con 💙 en Colombia.