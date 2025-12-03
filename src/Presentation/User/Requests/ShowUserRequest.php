<?php

declare(strict_types=1);

namespace Presentation\User\Requests;

use Shared\Requests\BaseRequest;

class ShowUserRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->id === $this->getUserId();
    }

    public function getUserId(): string
    {
        return $this->route('id');
    }
}
