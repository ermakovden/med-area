#!/bin/bash

set -e

KIBANA_URL="${KIBANA_URL:-http://kibana:5601}"
MAX_RETRIES=30
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

echo "Creating index pattern medarea-logs-*..."
curl -s -X POST "${KIBANA_URL}/api/saved_objects/index-pattern/medarea-logs" \
    -H "kbn-xsrf: true" \
    -H "Content-Type: application/json" \
    -d '{
        "attributes": {
            "title": "medarea-logs-*",
            "timeFieldName": "@timestamp"
        }
    }' | grep -q '"id"'

echo "Index pattern created."

echo "Importing OCR monitoring dashboard..."
curl -s -X POST "${KIBANA_URL}/api/saved_objects/_import?overwrite=true" \
    -H "kbn-xsrf: true" \
    -F "file=@/docker/kibana/dashboards/ocr-monitoring.ndjson"

echo "Dashboard imported."
echo "Kibana setup complete."
