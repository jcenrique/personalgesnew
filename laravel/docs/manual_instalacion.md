# Manual de Instalación — Aplicación SofTren

**Idioma:** Español (España)
**Nivel:** Intermedio

## Requisitos previos
- Docker y Docker Compose instalados.
- Git instalado.
- Navegador moderno (Chrome, Firefox, Edge).
- Acceso al repositorio y al código fuente.

## Preparación del entorno local
1. Abrir una terminal en la carpeta raíz del proyecto:

```bash
cd /workspaces/codespaces-blank/laravel
```

2. Copiar el fichero de entorno si no existe:

```bash
cp .env.example .env
```

3. Generar la clave de aplicación:

```bash
./vendor/bin/sail artisan key:generate
```

4. Ajustar las variables de entorno en `.env` si es necesario:
- `APP_URL=http://127.0.0.1`
- `DB_CONNECTION=mysql`
- `DB_HOST=mysql`
- `DB_PORT=3306`
- `DB_DATABASE=laravel`
- `DB_USERNAME=sail`
- `DB_PASSWORD=password`
- `SESSION_DRIVER=database`

## Instalación de dependencias
1. Instalar dependencias de PHP con Composer:

```bash
./vendor/bin/sail composer install
```

2. Instalar dependencias de JavaScript:

```bash
./vendor/bin/sail npm install
```

3. Compilar activos con Vite en modo desarrollo:

```bash
./vendor/bin/sail npm run dev
```

O para producción:

```bash
./vendor/bin/sail npm run build
```

## Preparar la base de datos
1. Levantar los contenedores y la base de datos:

```bash
./vendor/bin/sail up -d
```

2. Ejecutar migraciones y sembrar datos:

```bash
./vendor/bin/sail artisan migrate --seed
```

3. Crear el enlace simbólico de `storage` si es necesario:

```bash
./vendor/bin/sail artisan storage:link
```

## Configuración de autenticación
La aplicación utiliza autenticación LDAP en `config/auth.php` con sincronización de usuarios en la base de datos.

- Si se usa LDAP, comprueba la configuración en `config/ldap.php` y en el archivo `.env`.
- Para pruebas locales sin LDAP activo, utiliza usuarios sincronizados en la tabla `users`.

## Acceso a la aplicación
- Panel de usuario: `http://127.0.0.1/` (ruta raíz).
- Página de login: `http://127.0.0.1/login`
- Panel de administración Filament: `http://127.0.0.1/admin`

## Generar capturas de pantalla de la aplicación
El proyecto incluye un script para tomar capturas de pantalla automáticas desde Playwright.

1. Arrancar los servicios y asegurarse de que la aplicación esté disponible.
2. Ejecutar el script:

```bash
cd /workspaces/codespaces-blank/laravel
node docs/screenshots-capture-more.cjs
```

3. Las capturas se guardarán en `docs/screenshots/`.

## Notas adicionales
- Para limpiar cachés del proyecto:

```bash
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan view:clear
```

- Si hay problemas con sesiones o formularios, revisa `SESSION_DRIVER` en `.env` y la tabla `sessions` en la base de datos.

---

_Archivo generado automáticamente: guía de instalación local básica._
