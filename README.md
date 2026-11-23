# Trabajo Práctico N.º 4 — Migración a PHP, SSI y SSR

## Descripción del proyecto

**AutoMarket** es una aplicación web para consultar un catálogo de vehículos, registrar usuarios, administrar publicaciones y guardar vehículos favoritos. El proyecto comenzó como una maqueta estática en HTML y fue refactorizado a PHP mediante Server Side Includes (SSI), incorporando modularización, renderizado dinámico del lado del servidor y configuración mediante variables de entorno.

El proyecto corresponde a la asignatura **Programación Web**, Unidad 4: Entorno Servidor y Primeros Pasos con PHP.

## Enlace al prototipo de Figma

> Agregar aquí el enlace público o compartido con permisos de lectura al prototipo original: `https://www.figma.com/...`

## Tecnologías utilizadas

- HTML5
- CSS3
- JavaScript
- PHP 8.x
- MySQL / MariaDB
- Apache
- XAMPP
- Git y GitHub

## Migración y modularización realizada

Las páginas principales fueron migradas de HTML a PHP y utilizan plantillas comunes para evitar código repetido:

- `includes/header.php`: `<head>`, metadatos, enlaces de estilos, encabezado y navegación principal.
- `includes/nav.php`: plantilla adicional de navegación disponible para la modularización.
- `includes/footer.php`: pie de página y carga del archivo JavaScript.
- `config/env.php`: lectura de las variables de configuración desde `.env`.
- `config/db.php`: configuración de la conexión a MySQL/MariaDB.

Las vistas cargan las plantillas con `require_once`, por ejemplo:

```php
$titulo_pagina = "Inicio - AutoMarket";
require_once __DIR__ . '/includes/header.php';

// Contenido de la página

require_once __DIR__ . '/includes/footer.php';
```

## Renderizado Server-Side

El título de cada página se define mediante la variable `$titulo_pagina` antes de incluir `header.php`. Esa plantilla utiliza el valor para completar dinámicamente la etiqueta `<title>` y el encabezado visible.

El menú determina el enlace activo comparando el nombre del archivo actual con `basename($_SERVER['PHP_SELF'])` y aplica la clase `nav-active` a la sección correspondiente.

## Variables de entorno

La configuración global se carga desde el archivo `.env` mediante `config/env.php`. Las variables disponibles son:

- `APP_NAME`: nombre de la aplicación.
- `APP_ENV`: entorno de ejecución, por ejemplo `local`.
- `APP_URL`: URL local del proyecto.
- `SUPPORT_EMAIL`: correo de soporte.
- `BRANCH_ADDRESS`: dirección de la sucursal.
- `AUTHOR_NAME`: nombre del autor.
- `CURRENT_YEAR`: año visible en el pie de página.

El archivo `.env` está excluido por `.gitignore` y se incluye `.env.example` como plantilla.

## Instalación y ejecución local

1. Instalá XAMPP y asegurate de tener iniciados Apache y MySQL.
2. Cloná el repositorio dentro de la carpeta `htdocs`, por ejemplo:

```text
C:\xampp\htdocs\Artaza
```

3. Importá `includes/schema.sql` desde phpMyAdmin para crear la base `automarket` y sus tablas.
4. Copiá `.env.example` como `.env` y completá los valores según tu entorno.
5. Revisá la configuración de la base de datos en `config/db.php`.
6. Abrí el proyecto en el navegador:

```text
http://localhost/Artaza/
```

Extensiones PHP recomendadas:

- `mysqli`
- `pdo_mysql`
- `fileinfo`

## Funcionalidades principales

- Catálogo y ficha técnica de vehículos.
- Registro e inicio de sesión.
- Contraseñas protegidas con `password_hash()` y `password_verify()`.
- Sesiones con regeneración del identificador al autenticar.
- Vehículos favoritos.
- Publicación de vehículos con carga de imágenes.
- Formulario de contacto.
- Panel CRUD para administradores.
- Cambio de modo claro/oscuro y navegación dinámica.

## Rutas principales

| Ruta | Descripción |
| --- | --- |
| `index.php` | Página de inicio |
| `catalogo.php` | Catálogo de vehículos |
| `registro.php` | Registro de usuarios |
| `inicio de sesion.php` | Inicio de sesión |
| `vender auto.php` | Publicación de vehículos |
| `favoritos.php` | Vehículos favoritos |
| `contacto.php` | Contacto |
| `admin/index.php` | Panel de administración |

## Evidencia de funcionamiento

### Servidor local

El proyecto se ejecuta desde XAMPP en:

```text
http://localhost/Artaza/
```

### Página de inicio

![Página de inicio de AutoMarket](docs/screenshots/inicio.png)

### Estructura modular

Las plantillas reutilizables se encuentran en:

```text
includes/
├── header.php
├── nav.php
└── footer.php
```

### Navegación dinámica

Cada vista define su `$titulo_pagina` y el encabezado reemplaza la etiqueta `<title>` del navegador. La función `pagina_activa()` compara el archivo actual con el enlace del menú y agrega la clase `nav-active` al elemento correspondiente.

### Variables de entorno

La plantilla incluida en el repositorio es `.env.example`. Antes de ejecutar el proyecto debe copiarse como `.env`, mientras `.gitignore` impide subir el archivo real con configuración local.

## Estructura relevante

```text
admin/       Panel y operaciones CRUD
config/      Conexión y variables de entorno
css/         Hojas de estilo
includes/    Plantillas header, nav y footer
docs/        Evidencias y documentación visual
js/          Funciones JavaScript
uploads/     Imágenes cargadas por los usuarios
```

## Seguridad

Las contraseñas no se almacenan en texto plano. Las consultas sensibles utilizan sentencias preparadas y las vistas administrativas verifican el rol de usuario. Para un despliegue público se recomienda HTTPS y configurar las opciones de sesión `Secure`, `HttpOnly` y `SameSite`.
