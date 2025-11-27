<?php

declare(strict_types=1);

namespace Application\Analys\DTO;

use Carbon\Carbon;
use Shared\DTO\BaseDTO;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;

class UserAnalysDTO extends BaseDTO
{
    public int|Optional $id;

    public string|Optional $user_id;

    public int|Optional $analys_id;

    public string|Optional $analys_name;

    public float|Optional $data;

    #[WithCast(DateTimeInterfaceCast::class)]
    public Carbon|Optional|null $created_at;

    #[WithCast(DateTimeInterfaceCast::class)]
    public Carbon|Optional|null $updated_at;
}
