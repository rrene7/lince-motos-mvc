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

## Actualizar una instalación existente

Desde la carpeta clonada del repositorio:

```bash
git pull origin main
bash install.sh
```

El instalador conserva la información existente y aplica automáticamente las nuevas migraciones de MySQL, incluida la columna para fotografías.

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

## Funciones incluidas

- Inicio y cierre de sesión.
- Retorno automático al expediente después de iniciar sesión desde un QR.
- Panel de disponibilidad operativa.
- Registro, edición, consulta y eliminación de motocicletas.
- Fotografía de la motocicleta desde archivo o cámara del celular.
- Miniaturas en el listado de la flota.
- Código QR real para cada expediente.
- Descarga, copia e impresión del QR.
- Estados operativos.
- Historial de mantenimiento.
- Próximo mantenimiento por kilometraje o fecha.
- Alertas básicas.
- Protección CSRF, validación de fotografías y consultas PDO preparadas.

## Uso del QR desde un celular

El QR contiene la dirección del expediente que se está viendo. Si abre el sistema como `http://localhost/...`, el celular no podrá llegar al servidor porque `localhost` se refiere al propio teléfono.

Para una prueba dentro de la misma red:

1. Consulte la dirección IP del equipo donde funciona XAMPP, por ejemplo `192.168.1.25`.
2. Abra el sistema en el navegador usando `http://192.168.1.25/lince_mvc/`.
3. Entre al expediente y descargue o imprima el QR generado.
4. El celular debe estar conectado a la misma red y el firewall debe permitir acceso a Apache.

El instalador descarga una copia local de `qrcodejs` para que los QR puedan seguir generándose sin conexión externa. Si la descarga falla, la pantalla intenta usar temporalmente la versión alojada en CDN.

## Fotografías

Las imágenes se guardan en:

```text
public/uploads/motos
```

Se permiten JPG, PNG y WEBP con un máximo de 5 MB. El directorio bloquea archivos ejecutables y no guarda las fotografías dentro de Git.

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

- Usuarios y permisos completos por rol.
- Registro rápido de odómetro con fotografía.
- Solicitudes e inventario de repuestos.
- Órdenes de trabajo completas.
- Auditoría de cambios.
