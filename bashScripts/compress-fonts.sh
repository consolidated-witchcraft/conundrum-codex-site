#!/usr/bin/env bash

set -euo pipefail

INPUT_DIR="${1:-.}"

shopt -s nullglob

for ttf in "$INPUT_DIR"/*.ttf; do
  base="${ttf%.ttf}"
  output="${base}.woff2"

  if [[ -f "$output" ]]; then
    echo "Skipping (already exists): $output"
    continue
  fi

  echo "Converting: $ttf -> $output"
  woff2_compress "$ttf"
done

echo "All done."
