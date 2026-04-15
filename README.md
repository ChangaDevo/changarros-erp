# Changarrito OS

ERP + Portal de Clientes para Changarrito Estudio Creativo.
Sistema centralizado para gestionar clientes, proyectos, documentos, pagos y publicaciones en redes sociales.

---

## Stack tecnologico

| Capa | Tecnologia |
|---|---|
| Backend | Laravel 12 (PHP 8.2+) |
| Base de datos | MySQL 8 |
| Frontend | Bootstrap 5 + NobleUI + Vite |
| IA | Claude API (Anthropic) |
| Social Media | Meta Graph API v21.0 |

---

## Modulos

- **Expediente Digital** — Clientes y proyectos
- **Documentos** — Flujo Borrador → Enviado → Sellado (inmutable)
- **Entregas / Avances** — Con aprobacion del cliente
- **Pagos** — Referencia CoDi y QR
- **Cotizaciones** — Enlace publico para aprobacion sin login
- **Bitacora** — Audit log de todas las acciones con IP
- **Publicaciones** — Calendario de redes sociales con IA y publicacion automatica en Meta
- **Tokens de Meta** — Gestion de Page Access Tokens por cliente

---

## Instalacion en un equipo nuevo

### 1. Requisitos previos

- PHP 8.2 o superior
- Composer
- Node.js 18+ y npm
- MySQL 8
- XAMPP (o servidor Apache/Nginx equivalente)

### 2. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/changarros-erp.git changarrO
```

Colocarlo en la carpeta del servidor web. En XAMPP:

```
C:/xampp/htdocs/changarrO
```

### 3. Instalar dependencias

```bash
cd changarrO
composer install
npm install
npm run build
```

### 4. Configurar el entorno

```bash
cp .env.example .env
php artisan key:generate
```

Editar el archivo `.env` con los datos del equipo:

```env
APP_URL=http://localhost/changarrO/public

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=changarrito_os
DB_USERNAME=root
DB_PASSWORD=

# Claude AI (para analisis de imagenes en publicaciones)
ANTHROPIC_API_KEY=sk-ant-api03-...
ANTHROPIC_MODEL=claude-sonnet-4-6
```

### 5. Crear la base de datos

Crear la base de datos en MySQL antes de migrar:

```sql
CREATE DATABASE changarrito_os CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Ejecutar migraciones

```bash
php artisan migrate --seed
```

### 7. Enlace de almacenamiento

```bash
php artisan storage:link
```

Esto crea el enlace simbolico para que los archivos subidos sean accesibles desde el navegador.

### 8. Acceder al sistema

| Portal | URL |
|---|---|
| Admin | http://localhost/changarrO/public/admin/login |
| Cliente | http://localhost/changarrO/public/portal/login |

**Credenciales de prueba:**

| Rol | Email | Password |
|---|---|---|
| Admin | admin@changarrito.com | password |
| Cliente | cliente@demo.com | password |

---

## Publicacion automatica en redes sociales

### Configurar tokens de Meta

1. Ir a **Admin → Tokens de Meta**
2. Agregar el **Page Access Token** de cada pagina de Facebook/Instagram
   - Obtenerlo desde Graph API Explorer en `GET /me/accounts`
   - Copiar el `access_token` del objeto de la pagina (NO el User Token del panel derecho)
   - Para tokens permanentes: intercambiar por Long-Lived Token primero
3. Para Instagram: usar el boton **"Detectar"** para obtener el `ig_account_id` automaticamente

### Activar el scheduler

El sistema revisa cada hora las publicaciones aprobadas con fecha vencida y las publica en Meta.

**En produccion**, agregar al cron del servidor:

```bash
* * * * * php /ruta/absoluta/al/proyecto/artisan schedule:run >> /dev/null 2>&1
```

**Verificar que el scheduler este registrado:**

```bash
php artisan schedule:list
```

**Ejecutar manualmente:**

```bash
# Publicar ahora
php artisan publicaciones:publicar

# Simulacion sin publicar realmente
php artisan publicaciones:publicar --dry-run
```

**Log de ejecuciones:**

```
storage/logs/publicaciones.log
```

### Notas importantes sobre Meta

- La app de Meta debe estar en **Live Mode** con `pages_manage_posts` aprobado para publicar en paginas de clientes
- En **Development Mode**: solo funciona con paginas cuyos admins esten registrados como Testers de la app
- En **localhost**: se publica solo el texto (Meta requiere URLs publicas para imagenes/videos)
- Los tokens son guardados **encriptados** en la base de datos

---

## Analisis con IA (Claude)

Al crear una publicacion, subir una imagen o video y presionar **"Analizar con IA"** para:

- Generar el copy del post optimizado para la red social seleccionada
- Sugerir la hora optima de publicacion para audiencia latinoamericana
- Definir la audiencia sugerida para publicidad pagada
- Clasificar el tipo de contenido detectado

Todos los campos se autocompletar y pueden editarse antes de guardar.

---

## Estructura del proyecto

```
app/
  Console/Commands/
    PublicarAprobadas.php        # Comando del scheduler
  Http/Controllers/
    Admin/
      PublicacionController.php  # CRUD + endpoint IA
      MetaTokenController.php    # Gestion de tokens
    Portal/
      PublicacionController.php  # Ver + aprobar/rechazar
  Models/
    Publicacion.php
    MetaToken.php
  Services/
    MetaPublicacionService.php   # Logica Graph API

resources/views/
  admin/
    publicaciones/index.blade.php  # Calendario admin con IA
    meta-tokens/index.blade.php    # Gestion de tokens
  portal/
    publicaciones/index.blade.php  # Calendario cliente

routes/
  web.php       # Todas las rutas HTTP
  console.php   # Definicion del scheduler
```

---

## Tablas de base de datos

| Tabla | Descripcion |
|---|---|
| users | Usuarios admin y clientes |
| clientes | Empresas cliente |
| proyectos | Proyectos por cliente |
| documentos | Documentos con flujo de aprobacion |
| entregas | Avances con archivos adjuntos |
| archivos_entrega | Archivos de cada entrega |
| pagos | Pagos con QR/CoDi |
| cotizaciones | Cotizaciones con enlace publico |
| cotizacion_items | Lineas de cada cotizacion |
| actividad_log | Audit log del sistema |
| publicaciones | Posts de redes sociales |
| meta_tokens | Page Access Tokens de Meta (encriptados) |

---

## Seguridad

- `access_token` de Meta guardados encriptados en BD
- API key de Anthropic solo en `.env`, nunca en el codigo
- Tenant isolation: clientes solo ven sus propios datos
- Documentos sellados son inmutables tras aprobacion
- Audit log de todas las acciones con IP y timestamp
