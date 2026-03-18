#!/usr/bin/env bash

set -euo pipefail
shopt -s nullglob nocaseglob

INPUT_DIR="$(realpath "${1:-.}")"

for jpg in "$INPUT_DIR"/*.jpg "$INPUT_DIR"/*.jpeg; do
  [ -e "$jpg" ] || continue

  output="${jpg%.*}.webp"

  if [[ -f "$output" ]]; then
    echo "Skipping (already exists): $output"
    continue
  fi

  echo "Converting: $jpg -> $output"
  cwebp "$jpg" -o "$output"
done

echo "Done."
