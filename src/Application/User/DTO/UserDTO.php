<?php

declare(strict_types=1);

namespace Application\User\DTO;

use Illuminate\Support\Facades\Date;
use Shared\DTO\BaseDTO;
use Spatie\LaravelData\Optional;

class UserDTO extends BaseDTO
{
    public string|Optional $nickname;

    public string|Optional $email;

    public string|Optional $password;

    public Date|Optional|null $email_verified_at;
}
