<?php

declare(strict_types=1);

namespace Presentation\User\Requests;

use OpenApi\Attributes as OA;
use Shared\Requests\BaseRequest;

#[OA\RequestBody(
    request: 'ShowUserRequest',
    description: 'Get user data by id',
)]
class ShowUserRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->id === $this->getUserId();
    }

    public function getUserId(): string
    {
        return $this->string('id')->toString();
    }
}
