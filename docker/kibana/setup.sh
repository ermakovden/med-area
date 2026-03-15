#!/bin/bash

set -e

KIBANA_URL="${KIBANA_URL:-http://localhost:5601}"
DASHBOARDS_DIR="/docker/kibana/dashboards"
MAX_RETRIES=60
RETRY_INTERVAL=5

echo "Waiting for Kibana to be ready..."
for i in $(seq 1 $MAX_RETRIES); do
    if curl -s "${KIBANA_URL}/api/status" | grep -q '"level":"available"'; then
        echo "Kibana is ready."
        break
    fi
    if [ "$i" -eq "$MAX_RETRIES" ]; then
        echo "ERROR: Kibana did not become ready in time."
        exit 1
    fi
    echo "Attempt $i/$MAX_RETRIES — waiting ${RETRY_INTERVAL}s..."
    sleep $RETRY_INTERVAL
done

echo "Importing dashboards..."
# Each dashboard lives in its own subdirectory.
# Files are imported in a fixed order to satisfy Kibana's reference resolution:
#   1. index-pattern  — must exist before any visualization references it
#   2. viz-*          — visualizations that reference the index-pattern
#   3. dashboard      — references the visualizations above
for dir in "${DASHBOARDS_DIR}"/*/; do
    echo "  → $(basename "$dir")"
    for file in index-pattern.ndjson viz-*.ndjson dashboard.ndjson; do
        filepath="${dir}${file}"
        [ -f "$filepath" ] || continue
        curl -s -X POST "${KIBANA_URL}/api/saved_objects/_import?overwrite=true" \
            -H "kbn-xsrf: true" \
            -F "file=@${filepath}"
        echo ""
    done
done

echo "Kibana setup complete."
