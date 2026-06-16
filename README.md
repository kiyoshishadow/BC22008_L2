# API Slim para doctores y hospitales

## Estructura del proyecto

- `public/index.php` - punto de entrada de Slim
- `src/Config/db.php` - configuración de conexión PDO
- `.env` - variables de entorno locales
- `.gitignore` - ignora `.env`, `vendor/` y archivos de editor

## Pasos para ejecutar localmente con XAMPP

1. Asegúrate de que XAMPP esté corriendo Apache y MySQL.
2. Copia esta carpeta `APIBC22008` dentro de `C:\xampp\htdocs\` o configura un virtual host.
3. Usa PHP integrado de XAMPP para ejecutar Composer:
   ```powershell
   cd C:\xampp\htdocs\APIBC22008
   & "C:\xampp\php\php.exe" composer.phar install
   ```
4. Accede a la API en el navegador o con herramientas como Postman:
   - `http://localhost/APIBC22008/public/doctores`
   - `http://localhost/APIBC22008/public/hospitales`
   - `http://localhost/APIBC22008/public/hospitales/1`

> Si necesitas usar `index.php`, también funcionan estas rutas:
> - `http://localhost/APIBC22008/public/index.php/doctores`
> - `http://localhost/APIBC22008/public/index.php/hospitales`
> - `http://localhost/APIBC22008/public/index.php/hospitales/1`

## Acceso desde un teléfono físico

Si pruebas la app desde un móvil conectado a la misma red Wi-Fi, no uses `10.0.2.2`.

Tu PC tiene la IP `192.168.1.15`. En el cliente móvil la URL base debe ser:

- `http://192.168.1.15/APIBC22008/public/`

Si tu aplicación Android no funciona con rutas limpias, usa:

- `http://192.168.1.15/APIBC22008/public/index.php/`

Prueba primero en el navegador del teléfono:

- `http://192.168.1.15/APIBC22008/public/doctores`
- o `http://192.168.1.15/APIBC22008/public/index.php/doctores`

Asegúrate de que el teléfono y la PC estén en la misma red Wi-Fi y que el firewall permita conexiones al servidor Apache.

## Rutas disponibles

- `POST /doctores` - insertar doctor
- `GET /doctores` - obtener todos los doctores
- `POST /hospitales` - insertar hospital
- `GET /hospitales` - obtener todos los hospitales
- `GET /hospitales/{id}` - obtener un hospital por ID

## Variables de entorno

Configura en `.env`:

```
DB_HOST=127.0.0.1
DB_NAME=bc22008_parcial3
DB_USER=root
DB_PASS=
```

## Exportar la base de datos

En phpMyAdmin, selecciona `bc22008_parcial3` y exporta la estructura/datos en SQL.

## Subir a la nube

1. Sube la base de datos a un servicio como Filess.io o FreeSQLDatabase.com.
2. Crea un repositorio en GitHub y sube el código, dejando `.env` en `.gitignore`.
3. En Render.com crea un Web Service y define variables de entorno con los datos de la base de datos online.
