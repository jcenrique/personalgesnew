#!/usr/bin/env sh
set -eu

OLD='tableWrapperContentSelector:".fi-ta-content"'
NEW='tableWrapperContentSelector:".fi-ta-content-ctn"'

patch_file() {
  file="$1"

  if [ ! -f "$file" ]; then
    return 0
  fi

  if grep -q "$NEW" "$file"; then
    return 0
  fi

  if grep -q "$OLD" "$file"; then
    sed -i 's/tableWrapperContentSelector:".fi-ta-content"/tableWrapperContentSelector:".fi-ta-content-ctn"/g' "$file"
  fi
}

patch_file "vendor/asmit/resized-column/resources/dist/js/resized-column.js"
patch_file "public/js/asmit/resized-column/resized-column.js"
