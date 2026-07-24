# Sistema MVC de motocicletas LINCE

Prototipo en PHP, MySQL y patrón MVC para administrar motocicletas LINCE en un entorno XAMPP.

> **Aviso:** este repositorio contiene únicamente datos ficticios de demostración. No agregue información operativa, credenciales institucionales ni datos reales mientras el repositorio sea público.

## Instalación con Git y Bash

En **Git Bash**, WSL o una terminal Linux:

```bash
git clone https://github.com/rrene7/lince-motos-mvc.git
cd lince-motos-mvc
bash install.sh
```

El instalador detecta estas ubicaciones comunes:

- Git Bash en Windows: `C:\xampp\htdocs`
- WSL con XAMPP de Windows: `/mnt/c/xampp/htdocs`
- XAMPP para Linux: `/opt/lampp/htdocs`

Después, inicie **Apache** y **MySQL** desde XAMPP y abra:

```text
http://localhost/lince_mvc/
```

### MySQL con contraseña

```bash
bash install.sh --db-user root --db-pass "SU_CLAVE"
```

### Ruta personalizada

```bash
bash install.sh --target /ruta/a/xampp/htdocs/lince_mvc
```

### Reiniciar la base de demostración

> Este comando elimina los datos existentes del prototipo.

```bash
bash install.sh --reset-db
```

### Ver todas las opciones

```bash
bash install.sh --help
```

## Acceso inicial

- Correo: `admin@lince.local`
- Contraseña: `Admin1234`

Cambie esta contraseña antes de una prueba real.

## Instalación manual

1. Copie la carpeta en `C:\xampp\htdocs\lince_mvc`.
2. Inicie Apache y MySQL.
3. Importe `database/lince_motos.sql` desde phpMyAdmin.
4. Abra `http://localhost/lince_mvc/`.

## Configuración

El instalador crea automáticamente este archivo local, excluido de Git:

```text
app/config/config.local.php
```

También puede copiar `app/config/config.local.example.php` y editar las credenciales.

## Funciones incluidas

- Inicio y cierre de sesión.
- Panel de disponibilidad operativa.
- Registro, edición, consulta y eliminación de motocicletas.
- Estados operativos.
- Historial de mantenimiento.
- Próximo mantenimiento por kilometraje o fecha.
- Alertas básicas.
- Protección CSRF y consultas PDO preparadas.

## Si las rutas no funcionan

1. Verifique que Apache tenga habilitado `mod_rewrite`.
2. En `C:\xampp\apache\conf\httpd.conf`, confirme:

```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

3. En el bloque de `htdocs`, use `AllowOverride All`.
4. Reinicie Apache.

Como alternativa temporal:

```text
http://localhost/lince_mvc/index.php?url=motos
```

## Próximas fases

- QR real por motocicleta.
- Usuarios y permisos por rol.
- Lecturas de odómetro con fotografía.
- Solicitudes e inventario de repuestos.
- Órdenes de trabajo completas.
- Auditoría de cambios.
