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
for file in "${DASHBOARDS_DIR}"/*.ndjson; do
    echo "  → $(basename "$file")"
    curl -s -X POST "${KIBANA_URL}/api/saved_objects/_import?overwrite=true" \
        -H "kbn-xsrf: true" \
        -F "file=@${file}"
    echo ""
done

echo "Kibana setup complete."
