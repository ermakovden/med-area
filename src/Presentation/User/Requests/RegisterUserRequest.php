<?php

declare(strict_types=1);

namespace Presentation\User\Requests;

use Application\User\DTO\UserDTO;
use OpenApi\Attributes as OA;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Shared\Requests\BaseRequest;

#[OA\RequestBody(
    request: 'RegisterUserRequest',
    description: 'Registration Request',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'nickname', description: 'Nickname of User', format: 'string'),
            new OA\Property(property: 'email', description: 'Email of User', format: 'email'),
            new OA\Property(property: 'password', description: 'Password of User', format: 'string'),
            new OA\Property(property: 'password_confirmation', description: 'Repeat password to confirm', format: 'string'),
        ]
    )
)]
class RegisterUserRequest extends BaseRequest
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
            'nickname' => ['required', 'string', 'unique:users,nickname'],
            'email' => ['required', 'string', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'password_confirmation' => ['required', 'string'],
        ];
    }
}
