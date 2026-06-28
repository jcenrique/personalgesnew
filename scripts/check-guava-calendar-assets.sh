#!/usr/bin/env sh
set -eu

PROVIDER_FILE="vendor/guava/calendar/src/CalendarServiceProvider.php"
EXPECTED_CSS="Css::make('calendar-styles', base_path('public/vendor/event-calendar/event-calendar.min.css'))"
EXPECTED_JS="Js::make('calendar-script', base_path('public/vendor/event-calendar/event-calendar.min.js'))"

LOCAL_CSS_FILE="public/vendor/event-calendar/event-calendar.min.css"
LOCAL_JS_FILE="public/vendor/event-calendar/event-calendar.min.js"
PUBLISHED_CSS_FILE="public/css/guava/calendar/calendar-styles.css"
PUBLISHED_JS_FILE="public/js/guava/calendar/calendar-script.js"

FAIL=0

check_file() {
  FILE_PATH="$1"

  if [ -f "$FILE_PATH" ]; then
    echo "[OK] Existe $FILE_PATH"
  else
    echo "[ERROR] Falta $FILE_PATH"
    FAIL=1
  fi
}

if [ ! -f "$PROVIDER_FILE" ]; then
  echo "[ERROR] No existe $PROVIDER_FILE"
  exit 1
fi

if grep -Fq "$EXPECTED_CSS" "$PROVIDER_FILE"; then
  echo "[OK] Provider usa asset CSS local"
else
  echo "[ERROR] Provider no apunta al CSS local esperado"
  FAIL=1
fi

if grep -Fq "$EXPECTED_JS" "$PROVIDER_FILE"; then
  echo "[OK] Provider usa asset JS local"
else
  echo "[ERROR] Provider no apunta al JS local esperado"
  FAIL=1
fi

check_file "$LOCAL_CSS_FILE"
check_file "$LOCAL_JS_FILE"
check_file "$PUBLISHED_CSS_FILE"
check_file "$PUBLISHED_JS_FILE"

if [ "$FAIL" -ne 0 ]; then
  echo "[FAIL] Mini chequeo de Guava Calendar KO"
  exit 1
fi

echo "[PASS] Mini chequeo de Guava Calendar OK"
