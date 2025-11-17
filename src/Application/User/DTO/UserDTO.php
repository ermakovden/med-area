<?php

declare(strict_types=1);

namespace Application\User\DTO;

use Carbon\Carbon;
use Shared\DTO\BaseDTO;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Optional;

class UserDTO extends BaseDTO
{
    public string|Optional $id;

    public string|Optional $nickname;

    public string|Optional $email;

    public string|Optional $password;

    #[WithCast(DateTimeInterfaceCast::class)]
    public Carbon|Optional|null $email_verified_at;

    #[WithCast(DateTimeInterfaceCast::class)]
    public Carbon|Optional|null $created_at;

    #[WithCast(DateTimeInterfaceCast::class)]
    public Carbon|Optional|null $updated_at;
}
