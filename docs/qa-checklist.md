# Checklist QA - PersonalGes

## 1) Pre-check local

- [ ] Verificar entorno y cache limpia:

```bash
cd /var/www/html/personalgesnew
php artisan optimize:clear
```

- [ ] Confirmar que base de datos y Redis estan disponibles.
- [ ] Confirmar que APP_ENV y APP_DEBUG son correctos para el entorno de prueba.

## 2) Validacion automatica

- [ ] Ejecutar tests backend:

```bash
cd /var/www/html/personalgesnew
composer test
```

- [ ] Si falla todo el conjunto, ejecutar por bloques para aislar:

```bash
php artisan test tests/Feature
php artisan test tests/Unit
```

- [ ] Verificar build frontend:

```bash
npm run build
```

## 3) Smoke test funcional (manual)

### Acceso y paneles

- [ ] Login correcto con usuario admin.
- [ ] Navegacion basica sin errores JS/500.
- [ ] Cambio entre panel app/admin correcto.

### Dashboard admin

- [ ] Widget de solicitudes pendientes visible cuando hay pendientes.
- [ ] Widget alternativo visible cuando no hay pendientes.
- [ ] Aparicion/desaparicion automatica con polling tras crear/gestionar solicitud.

### Calendarios

- [ ] Calendario personal carga sin 404 de assets.
- [ ] Calendario global carga sin 404 de assets.
- [ ] Colores de botones respetan color del panel (app/admin).
- [ ] Al hacer click en eventos se abre modal correcto.
- [ ] No aparece prefijo de hora "0:00" si se definio oculto.

### Formacion

- [ ] Recurso global de acciones formativas abre y lista correctamente.
- [ ] Tabs (todas/pendientes/pasadas) muestran badge con conteo.
- [ ] Filtro por usuario funciona como esperado.

## 4) Assets locales Guava Calendar

- [ ] Verificar parche local y assets publicados:

```bash
composer run check-guava-calendar-assets
```

## 5) Validacion previa a despliegue

- [ ] Limpiar caches y optimizar:

```bash
php artisan optimize:clear
php artisan optimize
```

- [ ] Revalidar pagina principal y dashboard tras optimize.

## 6) Criterio de salida

- [ ] Sin errores 500 en laravel.log durante smoke test.
- [ ] Sin errores 404 de calendar-styles/calendar-script.
- [ ] Sin errores de consola bloqueantes en flujos criticos.
- [ ] Aprobacion funcional de login, dashboard, calendarios y formacion.
