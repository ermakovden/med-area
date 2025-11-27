<?php

declare(strict_types=1);

namespace Application\Analys\DTO\Requests;

use Application\Analys\DTO\UserAnalysDTO;
use Shared\DTO\BaseDTO;
use Spatie\LaravelData\Optional;

class CreateUserAnalysisRequestDTO extends BaseDTO
{
    public string|Optional|null $user_id;

    /** @var array<UserAnalysDTO> */
    public array $analysis;
}
