<?php

declare(strict_types=1);

namespace Application\Analys\DTO;

use Carbon\Carbon;
use Domain\Analys\Enums\Analys;
use Shared\DTO\BaseDTO;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;

class AnalysDTO extends BaseDTO
{
    public Analys|int|Optional $id;

    public string|Optional $name;

    public string|Optional $description;

    #[WithCast(DateTimeInterfaceCast::class)]
    public Carbon|Optional|null $created_at;

    #[WithCast(DateTimeInterfaceCast::class)]
    public Carbon|Optional|null $updated_at;
}
