<?php

declare(strict_types=1);

namespace Presentation\User\Requests;

use OpenApi\Attributes as OA;
use Shared\DTO\BaseDTO;
use Shared\Requests\BaseRequest;

#[OA\RequestBody(
    request: 'SendEmailVerificationRequest',
    description: 'Send Email Verification Request',
)]
class SendEmailVerificationRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function getDTO(): BaseDTO
    {
        return BaseDTO::from([]);
    }
}
