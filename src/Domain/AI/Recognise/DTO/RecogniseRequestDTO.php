<?php

declare(strict_types=1);

namespace Domain\AI\Recognise\DTO;

use Carbon\Carbon;
use Domain\AI\Recognise\Enums\RecogniseStatus;
use Shared\DTO\BaseDTO;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;

class RecogniseRequestDTO extends BaseDTO
{
    public int|Optional $id;

    public string|Optional $user_id;

    public string|Optional $file_id;

    public string|Optional|null $operation_id;

    /**
     * @var array<string, string>|null|Optional
     */
    public array|Optional|null $response;

    public RecogniseStatus|Optional $status;

    public string|Optional|null $failed_reason;

    #[WithCast(DateTimeInterfaceCast::class)]
    public Carbon|Optional $created_at;

    #[WithCast(DateTimeInterfaceCast::class)]
    public Carbon|Optional $updated_at;
}
