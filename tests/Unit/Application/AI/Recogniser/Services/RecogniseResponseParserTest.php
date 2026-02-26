<?php

declare(strict_types=1);

namespace Tests\Unit\Application\AI\Recogniser\Services;

use Application\AI\Recogniser\DTO\Responses\RecogniseAsyncResponse;
use Application\AI\Recogniser\Services\RecogniseResponseParser;
use Domain\Analys\Enums\Analys;
use Domain\Analys\Enums\Unit;
use Tests\Unit\TestCase;

class RecogniseResponseParserTest extends TestCase
{
    protected RecogniseResponseParser $parser;

    public function setUp(): void
    {
        parent::setUp();

        $this->parser = new RecogniseResponseParser();
    }

    /**
     * Test parsing successful OCR response with D3 analysis (real Yandex Vision format)
     */
    public function test_parse_with_d3_analysis_real_format(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                [
                    'boundingBox' => ['vertices' => []],
                    'lines' => [
                        [
                            'boundingBox' => ['vertices' => []],
                            'text' => 'D3',
                            'words' => [['text' => 'D3']],
                        ],
                        [
                            'boundingBox' => ['vertices' => []],
                            'text' => '45.5 г/л',
                            'words' => [['text' => '45.5'], ['text' => 'г/л']],
                        ],
                    ],
                    'languages' => [['languageCode' => 'ru']],
                ],
            ],
            'fullText' => "D3\n45.5 г/л\n",
        ]);

        $result = $this->parser->parse($response);

        $this->assertCount(1, $result);
        $this->assertSame(Analys::D3, $result[0]->analys_id);
        $this->assertSame('D3', $result[0]->name);
        $this->assertSame('45.5', $result[0]->data);
        $this->assertSame(Unit::GL, $result[0]->unit);
    }

    /**
     * Test parsing multiple analyses (real format from Yandex Vision)
     */
    public function test_parse_with_multiple_analyses_real_format(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                [
                    'lines' => [
                        ['text' => 'D3', 'words' => [['text' => 'D3']]],
                        ['text' => '45.5 г/л', 'words' => [['text' => '45.5'], ['text' => 'г/л']]],
                        ['text' => 'B6', 'words' => [['text' => 'B6']]],
                        ['text' => '12.3 г/л', 'words' => [['text' => '12.3'], ['text' => 'г/л']]],
                        ['text' => 'B9', 'words' => [['text' => 'B9']]],
                        ['text' => '8.5%', 'words' => [['text' => '8.5%']]],
                        ['text' => 'B12', 'words' => [['text' => 'B12']]],
                        ['text' => '150 г/л', 'words' => [['text' => '150'], ['text' => 'г/л']]],
                    ],
                ],
            ],
            'fullText' => "D3\n45.5 г/л\nB6\n12.3 г/л\nB9\n8.5%\nB12\n150 г/л\n",
        ]);

        $result = $this->parser->parse($response);

        $this->assertCount(4, $result);

        $this->assertSame(Analys::D3, $result[0]->analys_id);
        $this->assertSame('45.5', $result[0]->data);
        $this->assertSame(Unit::GL, $result[0]->unit);

        $this->assertSame(Analys::B6, $result[1]->analys_id);
        $this->assertSame('12.3', $result[1]->data);
        $this->assertSame(Unit::GL, $result[1]->unit);

        $this->assertSame(Analys::B9, $result[2]->analys_id);
        $this->assertSame('8.5', $result[2]->data);
        $this->assertSame(Unit::PERCENT, $result[2]->unit);

        $this->assertSame(Analys::B12, $result[3]->analys_id);
        $this->assertSame('150', $result[3]->data);
        $this->assertSame(Unit::GL, $result[3]->unit);
    }

    /**
     * Test parsing with unknown analysis name (should be skipped)
     */
    public function test_parse_with_unknown_analysis_name_skipped(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                [
                    'lines' => [
                        ['text' => 'ВЕС'],
                        ['text' => '70 КГ'],
                        ['text' => 'КОЛ-ВО'],
                        ['text' => '5 РАЗ'],
                        ['text' => 'D3'],
                        ['text' => '45.5 г/л'],
                    ],
                ],
            ],
            'fullText' => "ВЕС\n70 КГ\nКОЛ-ВО\n5 РАЗ\nD3\n45.5 г/л\n",
        ]);

        $result = $this->parser->parse($response);

        // Only D3 should be parsed, unknown names should be skipped
        $this->assertCount(1, $result);
        $this->assertSame(Analys::D3, $result[0]->analys_id);
        $this->assertSame(Unit::GL, $result[0]->unit);
    }

    /**
     * Test parsing with unknown unit (unit should be null)
     */
    public function test_parse_with_unknown_unit(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                [
                    'lines' => [
                        ['text' => 'D3'],
                        ['text' => '45.5 ЕД'],
                    ],
                ],
            ],
            'fullText' => "D3\n45.5 ЕД\n",
        ]);

        $result = $this->parser->parse($response);

        $this->assertCount(1, $result);
        $this->assertSame(Analys::D3, $result[0]->analys_id);
        $this->assertSame('45.5', $result[0]->data);
        $this->assertNull($result[0]->unit);
    }

    /**
     * Test parsing with empty blocks
     */
    public function test_parse_with_empty_blocks(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [],
            'fullText' => '',
        ]);

        $result = $this->parser->parse($response);

        $this->assertEmpty($result);
    }

    /**
     * Test parsing with null metadata
     */
    public function test_parse_with_null_metadata(): void
    {
        $response = new RecogniseAsyncResponse();
        $response->id = 'test-id';
        $response->description = 'Test';
        $response->createdAt = now();
        $response->createdBy = 'test';
        $response->modifiedAt = now();
        $response->done = true;
        $response->metadata = null;

        $result = $this->parser->parse($response);

        $this->assertEmpty($result);
    }

    /**
     * Test parsing with empty lines (only whitespace)
     */
    public function test_parse_with_empty_lines(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                [
                    'lines' => [
                        ['text' => ''],
                        ['text' => '   '],
                        ['text' => "\n\t"],
                    ],
                ],
            ],
            'fullText' => "   \n\t\n",
        ]);

        $result = $this->parser->parse($response);

        $this->assertEmpty($result);
    }

    /**
     * Test parsing with analysis name but no value (incomplete data)
     */
    public function test_parse_with_analysis_name_but_no_value(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                [
                    'lines' => [
                        ['text' => 'D3'],
                        ['text' => 'B6'],
                    ],
                ],
            ],
            'fullText' => "D3\nB6\n",
        ]);

        $result = $this->parser->parse($response);

        // No values, so result should be empty
        $this->assertEmpty($result);
    }

    /**
     * Test parsing with value but no analysis name
     */
    public function test_parse_with_value_but_no_analysis_name(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                [
                    'lines' => [
                        ['text' => '45.5 г/л'],
                        ['text' => '12.3 г/л'],
                    ],
                ],
            ],
            'fullText' => "45.5 г/л\n12.3 г/л\n",
        ]);

        $result = $this->parser->parse($response);

        // No analysis name, so result should be empty
        $this->assertEmpty($result);
    }

    /**
     * Test parsing with case insensitive analysis names
     */
    public function test_parse_with_case_insensitive_names(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                [
                    'lines' => [
                        ['text' => 'd3'],
                        ['text' => '45.5 г/л'],
                        ['text' => 'b6'],
                        ['text' => '12.3 г/л'],
                        ['text' => 'B9'],
                        ['text' => '8.5%'],
                    ],
                ],
            ],
            'fullText' => "d3\n45.5 г/л\nb6\n12.3 г/л\nB9\n8.5%\n",
        ]);

        $result = $this->parser->parse($response);

        $this->assertCount(3, $result);
        $this->assertSame(Analys::D3, $result[0]->analys_id);
        $this->assertSame(Unit::GL, $result[0]->unit);

        $this->assertSame(Analys::B6, $result[1]->analys_id);
        $this->assertSame(Unit::GL, $result[1]->unit);

        $this->assertSame(Analys::B9, $result[2]->analys_id);
        $this->assertSame(Unit::PERCENT, $result[2]->unit);
    }

    /**
     * Test parsing with lines without unit
     */
    public function test_parse_with_lines_without_unit(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                [
                    'lines' => [
                        ['text' => 'D3'],
                        ['text' => '45.5'],
                    ],
                ],
            ],
            'fullText' => "D3\n45.5\n",
        ]);

        $result = $this->parser->parse($response);

        $this->assertCount(1, $result);
        $this->assertSame(Analys::D3, $result[0]->analys_id);
        $this->assertSame('45.5', $result[0]->data);
        $this->assertNull($result[0]->unit);
    }

    /**
     * Test parsing with multiple blocks
     */
    public function test_parse_with_multiple_blocks(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                [
                    'lines' => [
                        ['text' => 'D3'],
                        ['text' => '45.5 г/л'],
                    ],
                ],
                [
                    'lines' => [
                        ['text' => 'B6'],
                        ['text' => '12.3 г/л'],
                    ],
                ],
            ],
            'fullText' => "D3\n45.5 г/л\nB6\n12.3 г/л\n",
        ]);

        $result = $this->parser->parse($response);

        $this->assertCount(2, $result);
        $this->assertSame(Analys::D3, $result[0]->analys_id);
        $this->assertSame(Unit::GL, $result[0]->unit);

        $this->assertSame(Analys::B6, $result[1]->analys_id);
        $this->assertSame(Unit::GL, $result[1]->unit);
    }

    /**
     * Test parsing with lines missing text key
     */
    public function test_parse_with_lines_missing_text_key(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                [
                    'lines' => [
                        ['not_text' => 'D3'],
                        ['text' => 'B6'],
                        ['text' => '12.3 г/л'],
                    ],
                ],
            ],
            'fullText' => "B6\n12.3 г/л\n",
        ]);

        $result = $this->parser->parse($response);

        $this->assertCount(1, $result);
        $this->assertSame(Analys::B6, $result[0]->analys_id);
        $this->assertSame(Unit::GL, $result[0]->unit);
    }

    /**
     * Test parsing with blocks missing lines key
     */
    public function test_parse_with_blocks_missing_lines_key(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                ['not_lines' => []],
                [
                    'lines' => [
                        ['text' => 'D3'],
                        ['text' => '45.5 г/л'],
                    ],
                ],
            ],
            'fullText' => "D3\n45.5 г/л\n",
        ]);

        $result = $this->parser->parse($response);

        $this->assertCount(1, $result);
        $this->assertSame(Analys::D3, $result[0]->analys_id);
        $this->assertSame(Unit::GL, $result[0]->unit);
    }

    /**
     * Test that RecognisedAnalysData toArray returns correct format
     */
    public function test_recognised_analys_data_to_array(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                [
                    'lines' => [
                        ['text' => 'D3'],
                        ['text' => '45.5 г/л'],
                    ],
                ],
            ],
            'fullText' => "D3\n45.5 г/л\n",
        ]);

        $result = $this->parser->parse($response);

        $array = $result[0]->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('analys_id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('data', $array);
        $this->assertArrayHasKey('unit', $array);

        $this->assertSame(Analys::D3->value, $array['analys_id']);
        $this->assertSame('D3', $array['name']);
        $this->assertSame('45.5', $array['data']);
        $this->assertSame(Unit::GL->value, $array['unit']);
    }

    /**
     * Test parsing with decimal values
     */
    public function test_parse_with_decimal_values(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                [
                    'lines' => [
                        ['text' => 'D3'],
                        ['text' => '45.67 г/л'],
                    ],
                ],
            ],
            'fullText' => "D3\n45.67 г/л\n",
        ]);

        $result = $this->parser->parse($response);

        $this->assertCount(1, $result);
        $this->assertSame('45.67', $result[0]->data);
        $this->assertSame(Unit::GL, $result[0]->unit);
    }

    /**
     * Test parsing with integer values
     */
    public function test_parse_with_integer_values(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                [
                    'lines' => [
                        ['text' => 'B12'],
                        ['text' => '150 г/л'],
                    ],
                ],
            ],
            'fullText' => "B12\n150 г/л\n",
        ]);

        $result = $this->parser->parse($response);

        $this->assertCount(1, $result);
        $this->assertSame('150', $result[0]->data);
        $this->assertSame(Unit::GL, $result[0]->unit);
    }

    /**
     * Test parsing with percentage values
     */
    public function test_parse_with_percentage_values(): void
    {
        $response = $this->createMockResponse([
            'blocks' => [
                [
                    'lines' => [
                        ['text' => 'B9'],
                        ['text' => '8.5%'],
                    ],
                ],
            ],
            'fullText' => "B9\n8.5%\n",
        ]);

        $result = $this->parser->parse($response);

        $this->assertCount(1, $result);
        $this->assertSame('8.5', $result[0]->data);
        $this->assertSame(Unit::PERCENT, $result[0]->unit);
    }

    /**
     * Create mock RecogniseAsyncResponse with real Yandex Vision OCR format
     *
     * @param array{blocks: list<array<string, mixed>>, fullText: string}|null $textAnnotation
     */
    protected function createMockResponse(?array $textAnnotation): RecogniseAsyncResponse
    {
        $response = new RecogniseAsyncResponse();
        $response->id = 'test-operation-id';
        $response->description = 'Test recognition';
        $response->createdAt = now();
        $response->createdBy = 'test-user';
        $response->modifiedAt = now();
        $response->done = true;
        $response->metadata = $textAnnotation !== null ? ['textAnnotation' => $textAnnotation] : null;

        return $response;
    }
}
