# Guía de capturas para manuales

## Estructura

- images/admin: capturas del manual de administrador
- images/usuario: capturas del manual de usuario

## Recomendaciones

1. Usar resolución homogénea.
2. Evitar datos personales reales en capturas.
3. Nombrar las imágenes con prefijo numérico para mantener orden.
4. Recortar solo lo necesario para centrar la acción.

## Convención de nombres

- admin: 01-login-admin.png, 02-dashboard-admin.png, etc.
- usuario: 01-login-usuario.png, 02-dashboard-usuario.png, etc.

## Generación automática de capturas

1. Exportar credenciales para el script:

```bash
export CAPTURE_BASE_URL="http://127.0.0.1:8000"
export CAPTURE_USER_EMAIL="tu_usuario@dominio"
export CAPTURE_USER_PASSWORD="tu_password"
export CAPTURE_ADMIN_EMAIL="tu_admin@dominio"        # opcional, si no se define usa CAPTURE_USER_EMAIL
export CAPTURE_ADMIN_PASSWORD="tu_password_admin"     # opcional, si no se define usa CAPTURE_USER_PASSWORD
```

Antes de lanzar el script, levanta la app en local:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Si el puerto `8000` está ocupado, usa otro libre, por ejemplo:

```bash
php artisan serve --host=127.0.0.1 --port=8001
export CAPTURE_BASE_URL="http://127.0.0.1:8001"
```

2. Ejecutar el comando:

```bash
npm run manual:capture
```

3. Revisar capturas generadas en:

- `docs/images/usuario`
- `docs/images/admin`

Notas:

- El script intenta abrir modales de aprobar/rechazar y detalle de evento si existen en pantalla.
- Si faltan credenciales, solo generará capturas públicas (pantalla de login).
