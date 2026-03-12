<?php

declare(strict_types=1);

namespace Presentation\File\Requests;

use Domain\File\DTO\Filters\FilterFileDTO;
use OpenApi\Attributes as OA;
use Shared\Requests\BaseRequest;
use Shared\Rules\UserIdMatchesAuth;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class DeleteFilesRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function getDTO(): FilterFileDTO
    {
        if (! $validated = $this->validated()) {
            throw new UnprocessableEntityHttpException();
        }

        return FilterFileDTO::from($validated);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // user_id
            'user_ids' => ['array'],
            'user_ids.*' => ['string', new UserIdMatchesAuth()],

            // id
            'ids' => ['array'],
            'ids.*' => ['string'],
        ];
    }
}
