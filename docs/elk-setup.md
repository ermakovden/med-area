[← Configuration](configuration.md) · [Back to README](../README.md) · [Kibana Dashboards →](kibana-dashboards.md)

# ELK Stack Setup

Centralized logging via **Elasticsearch + Logstash + Kibana + Filebeat**.

Log flow:
```
Laravel → storage/logs/laravel.log → Filebeat → Logstash → Elasticsearch → Kibana
```

---

## Starting the Stack

```bash
docker compose up -d elasticsearch kibana logstash filebeat
```

Or start everything at once:

```bash
make dev
```

Wait ~30 seconds for Elasticsearch to become healthy before Kibana and Logstash connect.

---

## URLs

| Service | URL |
|---------|-----|
| Kibana | http://localhost:5601 |
| Elasticsearch | http://localhost:9200 |

---

## Enabling JSON Logging

In `.env`, set:

```env
LOG_CHANNEL=stack
LOG_STACK=json
```

This switches Laravel to write logs as JSON (one object per line), which Filebeat parses natively.

---

## Importing the Dashboard

The dashboard is imported automatically via `docker/kibana/setup.sh` on first start.

To re-import manually:

```bash
# 1. Wait for Kibana to be ready
curl http://localhost:5601/api/status

# 2. Create index pattern
curl -X POST http://localhost:5601/api/saved_objects/index-pattern/medarea-logs \
  -H "kbn-xsrf: true" \
  -H "Content-Type: application/json" \
  -d '{"attributes": {"title": "medarea-logs-*", "timeFieldName": "@timestamp"}}'

# 3. Import dashboard
curl -X POST "http://localhost:5601/api/saved_objects/_import?overwrite=true" \
  -H "kbn-xsrf: true" \
  -F "file=@docker/kibana/dashboards/ocr-monitoring.ndjson"
```

---

## Verifying Logs Reach Elasticsearch

**Step 1** — generate a log entry:

```bash
docker compose exec php php artisan tinker --execute="logger()->info('elk test', ['source' => 'manual']);"
```

**Step 2** — check that Filebeat picked it up:

```bash
docker compose logs filebeat | grep "laravel"
```

**Step 3** — query Elasticsearch directly:

```bash
curl "http://localhost:9200/medarea-logs-*/_search?q=message:elk+test&pretty"
```

Expect a hit with `_source.message = "elk test"`.

---

## Running Elasticsearch Integration Tests

```bash
# Requires running ELK stack
make test-elastic
```

These tests are excluded from the standard CI pipeline (`@group elastic`).

---

## Troubleshooting

**Filebeat exits immediately:**
```bash
docker compose logs filebeat
```
Usually a permissions issue on `filebeat.yml` — the file must be owned by `root:filebeat` with `640` permissions (handled in `docker/filebeat/Dockerfile`).

**Logstash not receiving events:**
```bash
docker compose logs logstash
```
Check that Elasticsearch is healthy first: `curl http://localhost:9200/_cluster/health`.

**No data in Kibana:**
- Confirm index pattern is `medarea-logs-*` with time field `@timestamp`
- In Kibana → Discover, set time range to "Last 1 hour"
- Check that `LOG_STACK=json` is set in `.env` (plain text logs are not parsed by Filebeat)

## See Also

- [Kibana Dashboards](kibana-dashboards.md) — Dashboard structure, import order, and how to add new dashboards
- [Configuration](configuration.md) — Environment variables including `LOG_CHANNEL` and `LOG_STACK`
- [Getting Started](getting-started.md) — Docker setup and Makefile commands
