<?php

declare(strict_types=1);

namespace Tests\Feature\Logging;

use Carbon\Carbon;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Shared\Logging\JsonMonologFormatter;
use Tests\TestCase;

class JsonLoggingTest extends TestCase
{
    private TestHandler $handler;

    private Logger $logger;

    public function setUp(): void
    {
        parent::setUp();

        $this->handler = new TestHandler();

        $this->logger = new Logger('test');
        $this->logger->pushHandler($this->handler);

        $formatter = new JsonMonologFormatter();
        $formatter($this->logger);
    }

    public function test_json_formatter_produces_valid_json(): void
    {
        $this->logger->info('test message', ['key' => 'value']);

        $this->assertTrue($this->handler->hasInfoRecords());

        $formatted = $this->handler->getRecords()[0]->formatted;

        $decoded = json_decode($formatted, true);
        $this->assertNotNull($decoded, 'Formatted log record is not valid JSON');
    }

    public function test_json_formatter_contains_required_fields(): void
    {
        $this->logger->info('test message', ['key' => 'value']);

        $formatted = $this->handler->getRecords()[0]->formatted;
        $decoded = json_decode($formatted, true);

        $this->assertArrayHasKey('datetime', $decoded);
        $this->assertArrayHasKey('level_name', $decoded);
        $this->assertArrayHasKey('message', $decoded);
        $this->assertArrayHasKey('context', $decoded);
    }

    public function test_json_formatter_sets_correct_level_and_message(): void
    {
        $this->logger->info('hello from json logger', ['user_id' => 42]);

        $formatted = $this->handler->getRecords()[0]->formatted;
        $decoded = json_decode($formatted, true);

        $this->assertSame('INFO', $decoded['level_name']);
        $this->assertSame('hello from json logger', $decoded['message']);
        $this->assertSame(42, $decoded['context']['user_id']);
    }

    public function test_json_formatter_datetime_is_valid_iso8601(): void
    {
        $this->logger->debug('timestamp check');

        $formatted = $this->handler->getRecords()[0]->formatted;
        $decoded = json_decode($formatted, true);

        $this->assertNotEmpty($decoded['datetime']);
        $this->assertNotNull(
            Carbon::parse($decoded['datetime']),
            'datetime field is not valid ISO 8601'
        );
    }
}
