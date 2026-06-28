# Despliegue en Nuevo Servidor (Laravel + Filament)

## Objetivo

Guia operativa para desplegar PersonalGes en un servidor nuevo, incluyendo:

- App Laravel/Filament
- Login contra Directorio Activo (LDAP)
- Correo corporativo
- Migracion de datos desde version antigua (`app:migrate-old-data`)
- Ejecucion programada de chequeo de reconocimientos (`app:check-reconocimientos`)
- Cola con Redis/Horizon

## 1) Requisitos previos (infra y accesos)

- Acceso SSH al servidor (usuario con sudo).
- Dominio y certificado TLS (si aplica en produccion).
- Base de datos nueva creada (MySQL/MariaDB) + credenciales.
- Redis disponible para cache/sesiones/colas.
- Puertos que deben quedar abiertos hacia la app o hacia los servicios externos necesarios:
    - `80` y `443` para acceso web.
    - `25` para salida SMTP si el servidor envía correo por ese puerto.
    - `587` o `465` si el correo corporativo usa submission TLS/SSL.
    - `389` o `636` para LDAP/LDAPS, segun configuracion.
    - `3306` para la base de datos antigua si el acceso no es local.
    - `6379` para Redis si se conecta a un Redis remoto.
- Credenciales LDAP corporativas:
    - `LDAP_HOST`, `LDAP_PORT`, `LDAP_BASE_DN`, `LDAP_USERNAME`, `LDAP_PASSWORD`
    - Definir si usa SSL/TLS (`LDAP_SSL`, `LDAP_TLS`).
- Credenciales de correo corporativo:
    - `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`
    - `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`
- Acceso a base de datos antigua para migracion de datos.

## 2) Instalacion base del servidor

Ejemplo (Ubuntu/Debian), ajustar versiones segun entorno:

```bash
sudo apt update
sudo apt install -y nginx git unzip curl supervisor redis-server \
  php8.4 php8.4-fpm php8.4-cli php8.4-mysql php8.4-xml php8.4-mbstring \
  php8.4-curl php8.4-zip php8.4-bcmath php8.4-intl php8.4-ldap php8.4-redis
```

Instalar Composer y Node (LTS) si no estan presentes.

## 3) Despliegue de codigo

```bash
cd /var/www/html
sudo git clone <repo-url> personalgesnew
cd personalgesnew
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

Permisos recomendados:

```bash
sudo chown -R www-data:www-data /var/www/html/personalgesnew
sudo find /var/www/html/personalgesnew -type f -exec chmod 644 {} \;
sudo find /var/www/html/personalgesnew -type d -exec chmod 755 {} \;
sudo chmod -R ug+rwx storage bootstrap/cache
```

## 4) Configuracion de entorno (.env)

Partir de `.env.example` y completar:

- App:
    - `APP_ENV=production`
    - `APP_DEBUG=false`
    - `APP_URL=https://tu-dominio`
- DB nueva:
    - `DB_CONNECTION=mysql`
    - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- Redis / colas:
    - `CACHE_STORE=redis`
    - `SESSION_DRIVER=redis`
    - `QUEUE_CONNECTION=redis`
    - `REDIS_HOST`, `REDIS_PORT`, `REDIS_PASSWORD`
- LDAP (Directorio Activo):
    - `LDAP_CONNECTION=default`
    - `LDAP_HOST`, `LDAP_PORT`, `LDAP_BASE_DN`
    - `LDAP_USERNAME`, `LDAP_PASSWORD`
    - `LDAP_SSL`/`LDAP_TLS`
- Correo corporativo:
    - `MAIL_MAILER` (`smtp` o `sendmail`)
    - `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`
    - `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`

## 5) Importante para base de datos antigua (old_db)

La conexion `old_db` ya queda parametrizada en `config/database.php` mediante variables de entorno.

Antes de produccion hay que completar en `.env` los valores de la base antigua:

- `OLD_DB_HOST`
- `OLD_DB_PORT`
- `OLD_DB_DATABASE`
- `OLD_DB_USERNAME`
- `OLD_DB_PASSWORD`

Opcionales segun entorno:

- `OLD_DB_SOCKET`
- `OLD_DB_CHARSET`
- `OLD_DB_COLLATION`
- `OLD_DB_MYSQL_ATTR_SSL_CA`

Si la base antigua usa otra configuracion, ajustar tambien `OLD_DB_URL`.

Si no se rellenan esas variables, la migracion no tendra acceso a la base antigua.

## 6) Inicializacion Laravel

```bash
cd /var/www/html/personalgesnew
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force   # si aplica
php artisan storage:link
php artisan config:clear
php artisan optimize
```

## 7) Assets de calendario (Guava local)

Este proyecto ya tiene scripts para local assets del calendario:

```bash
composer run fix-guava-calendar-local-assets
php artisan filament:assets --ansi
composer run check-guava-calendar-assets
```

## 8) Migracion de datos desde version antigua

Comando incluido:

```bash
php artisan app:migrate-old-data
```

Recomendaciones:

- Ejecutar primero en staging con copia de BD.
- Hacer backup previo de la BD nueva y vieja.
- Registrar salida del comando para auditoria:

```bash
php artisan app:migrate-old-data | tee storage/logs/migrate-old-data.log
```

## 9) Scheduler (cron) y Horizon

### 9.1 Scheduler Laravel (obligatorio)

El comando `app:check-reconocimientos` ya esta programado en `routes/console.php` como `daily()`.
Para que se ejecute, hay que tener cron del scheduler:

```cron
* * * * * cd /var/www/html/personalgesnew && php artisan schedule:run >> /dev/null 2>&1
```

### 9.2 Horizon (colas)

`Horizon` SI es compatible y recomendado para procesar colas (notificaciones, jobs, etc.).
No reemplaza el scheduler: son piezas distintas.

Supervisor ejemplo (`/etc/supervisor/conf.d/personalges-horizon.conf`):

```ini
[program:personalges-horizon]
process_name=%(program_name)s
command=php /var/www/html/personalgesnew/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/html/personalgesnew/storage/logs/horizon.log
stopwaitsecs=3600
```

Aplicar:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start personalges-horizon
```

## 10) Nginx + PHP-FPM

- Configurar virtual host apuntando a `public/`.
- Verificar socket/version de PHP-FPM correcta.
- Reiniciar servicios:

```bash
sudo nginx -t
sudo systemctl reload nginx
sudo systemctl restart php8.4-fpm
```

## 11) Verificaciones post-despliegue (smoke test)

- Login local/LDAP.
- Acceso panel app/admin.
- Calendario personal/global sin 404 de assets.
- Envio de correo de prueba (notification o mailable de test).
- Cola funcionando (`php artisan horizon:status`).
- Scheduler funcionando (`php artisan schedule:list`).
- Chequeo reconocimientos manual:

```bash
php artisan app:check-reconocimientos
```

## 12) Operacion y monitoreo

- Revisar logs:
    - `storage/logs/laravel.log`
    - `storage/logs/horizon.log`
- Rotacion de logs (logrotate).
- Backups diarios de DB nueva + DB antigua mientras dure migracion.

## 13) Plan de rollback minimo

- Snapshot/backup antes de migrar datos.
- Si falla migracion:
    - restaurar DB nueva desde backup
    - corregir mapeos/comando
    - repetir en staging antes de reintentar en produccion

---

## Resumen rapido de ejecucion (orden recomendado)

```bash
# 1) instalar dependencias sistema + clonar repo
# 2) composer install / npm ci / npm run build
# 3) configurar .env (DB, Redis, LDAP, Mail)
#    y variables OLD_DB_* para la migracion antigua
# 4) php artisan migrate --force
# 5) composer run fix-guava-calendar-local-assets
# 6) php artisan filament:assets --ansi
# 7) composer run check-guava-calendar-assets
# 8) php artisan app:migrate-old-data
# 9) configurar cron schedule:run
# 10) levantar Horizon con Supervisor
# 11) smoke test final
```
