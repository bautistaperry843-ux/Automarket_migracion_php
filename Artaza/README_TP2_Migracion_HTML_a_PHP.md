# TP2 - Migración de HTML a PHP

Aplicación web para consultar un catálogo de vehículos, registrar usuarios y administrar publicaciones. El proyecto está desarrollado en PHP y utiliza MySQL/MariaDB.

## Funcionalidades

- Registro de cuentas con validación de correo único y contraseñas almacenadas con `password_hash()` usando `PASSWORD_BCRYPT`.
- Inicio de sesión con consultas preparadas y verificación mediante `password_verify()`.
- Persistencia de autenticación con `$_SESSION`; al iniciar sesión se regenera el ID de sesión y se guardan el ID, nombre y rol del usuario.
- Cierre de sesión que limpia las variables, elimina la cookie de sesión y destruye la sesión.
- Control de acceso reutilizable en `includes/auth_guard.php`. Las vistas de administración requieren el rol `admin`.
- Panel de administración para crear, editar y eliminar vehículos.
- Catálogo, favoritos, formulario de publicación de vehículos y formulario de contacto.
- Carga de imágenes de vehículos en `uploads/`.

## Tecnologías

- PHP
- MySQL o MariaDB
- Apache
- HTML, CSS y JavaScript

Para ejecutar el proyecto localmente se recomienda XAMPP con las extensiones PHP `mysqli`, `pdo_mysql` y `fileinfo` habilitadas.

## Instalación local

1. Cloná o copiá el proyecto en la carpeta `htdocs` de XAMPP, por ejemplo `C:\xampp\htdocs\Artaza`.
2. Iniciá Apache y MySQL desde el panel de XAMPP.
3. Abrí phpMyAdmin e importá `includes/schema.sql` para crear la base `automarket` y sus tablas.
4. Revisá `config/db.php` y configurá el host, usuario, contraseña y nombre de la base de datos según tu instalación.
5. Abrí `http://localhost/Artaza/` en el navegador.

El proyecto usa enlaces relativos a la raíz del sitio; mantené la carpeta del proyecto como `/Artaza` o actualizá las rutas si elegís otro nombre.

## Crear una cuenta administradora

El registro público crea cuentas con el rol `usuario`. Para habilitar una cuenta de administración:

1. Registrá la cuenta desde `registro.php`.
2. En phpMyAdmin, ejecutá la siguiente consulta reemplazando el correo por el de esa cuenta:

```sql
UPDATE usuarios
SET rol = 'admin'
WHERE email = 'admin@ejemplo.com';
```

3. Cerrá sesión e iniciá sesión nuevamente. Las cuentas con rol `admin` se redirigen al panel de administración.

## Rutas principales

| Ruta | Descripción |
| --- | --- |
| `index.php` | Inicio |
| `catalogo.php` | Catálogo de vehículos |
| `registro.php` | Registro de usuario |
| `inicio de sesion.php` | Inicio de sesión |
| `logout.php` | Cierre de sesión |
| `vender auto.php` | Publicación de vehículos para usuarios autenticados |
| `admin/index.php` | Panel de administración |
| `admin/crear.php` | Alta de vehículos |
| `admin/editar.php` | Edición de vehículos |
| `admin/eliminar.php` | Eliminación de vehículos |

## Seguridad de sesión

Las contraseñas no se almacenan en texto plano. El acceso a datos se realiza con consultas preparadas y el panel comprueba el rol antes de mostrar funciones administrativas. Al autenticar se regenera el ID de sesión para reducir el riesgo de fijación de sesión.

`logout.php` invalida la sesión y vence la cookie. Para un despliegue público, serví el sitio exclusivamente por HTTPS y configurá antes de iniciar la sesión las opciones de cookie `Secure`, `HttpOnly` y `SameSite` en PHP. Esos flags no se establecen explícitamente desde el código actual.

## Estructura relevante

```text
admin/       Panel y operaciones CRUD
config/      Conexión a MySQL
css/         Estilos
includes/    Encabezado, esquema SQL y guardián de autenticación
js/          Funciones del navegador
uploads/     Imágenes subidas de vehículos
```

## Vista previa

![Página de inicio de AutoMarket](docs/screenshots/inicio.png)
