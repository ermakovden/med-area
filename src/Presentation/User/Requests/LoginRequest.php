<?php

declare(strict_types=1);

namespace Presentation\User\Requests;

use Application\User\DTO\UserDTO;
use OpenApi\Attributes as OA;
use Illuminate\Validation\ValidationException;
use Shared\Requests\BaseRequest;

class LoginRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function getDTO(): UserDTO
    {
        if (! $validated = $this->validated()) {
            throw new ValidationException("Validation failed");
        }

        return UserDTO::from($validated);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nickname' => [
                'required',
                'string',
            ],
            'password' => [
                'required',
                'string',
            ],
        ];
    }
}
