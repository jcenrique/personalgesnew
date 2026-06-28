#!/usr/bin/env sh
set -eu

PROVIDER_FILE="vendor/guava/calendar/src/CalendarServiceProvider.php"
LOCAL_DIR="public/vendor/event-calendar"
LOCAL_CSS="base_path('public/vendor/event-calendar/event-calendar.min.css')"
LOCAL_JS="base_path('public/vendor/event-calendar/event-calendar.min.js')"
CDN_CSS="https://cdn.jsdelivr.net/npm/@event-calendar/build@5.5.1/dist/event-calendar.min.css"
CDN_JS="https://cdn.jsdelivr.net/npm/@event-calendar/build@5.5.1/dist/event-calendar.min.js"

if [ ! -f "$PROVIDER_FILE" ]; then
  echo "[fix-guava-calendar-local-assets] No se encuentra $PROVIDER_FILE, se omite."
  exit 0
fi

mkdir -p "$LOCAL_DIR"

if command -v curl >/dev/null 2>&1; then
  curl -fsSL "$CDN_CSS" -o "$LOCAL_DIR/event-calendar.min.css"
  curl -fsSL "$CDN_JS" -o "$LOCAL_DIR/event-calendar.min.js"
else
  echo "[fix-guava-calendar-local-assets] curl no está disponible."
  exit 1
fi

# Sustituye de forma segura toda la línea de assets para evitar dobles reemplazos.
sed -i "s|Css::make('calendar-styles'.*|Css::make('calendar-styles', $LOCAL_CSS),|" "$PROVIDER_FILE"
sed -i "s|Js::make('calendar-script'.*|Js::make('calendar-script', $LOCAL_JS),|" "$PROVIDER_FILE"

echo "[fix-guava-calendar-local-assets] Assets locales preparados y provider parcheado."
