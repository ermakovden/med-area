<?php

declare(strict_types=1);

namespace Application\AI\Recogniser\DTO\Responses;

use Domain\Analys\Enums\Analys;
use Domain\Analys\Enums\Unit;
use Shared\DTO\BaseDTO;

/**
 * DTO for recognised analysis data
 */
class RecognisedAnalysData extends BaseDTO
{
    /** @var Analys $analys_id */
    public Analys $analys_id;

    /** @var string $name */
    public string $name;

    /** @var string $data */
    public string $data;

    /** @var Unit|null $unit */
    public ?Unit $unit;

    /**
     * Convert to array
     *
     * @return array{analys_id: int, name: string, data: string, unit: string|null}
     */
    public function toArray(): array
    {
        return [
            'analys_id' => $this->analys_id->value,
            'name' => $this->name,
            'data' => $this->data,
            'unit' => $this->unit?->value,
        ];
    }
}
