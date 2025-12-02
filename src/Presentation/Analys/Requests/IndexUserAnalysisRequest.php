<?php

declare(strict_types=1);

namespace Presentation\Analys\Requests;

use Application\Analys\DTO\Filters\FilterUserAnalysDTO;
use Domain\Analys\Enums\Analys;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;
use Shared\Requests\BaseRequest;
use Shared\Rules\UserIdMatchesAuth;
use Shared\Rules\UserIdMatchesUrlUserId;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class IndexUserAnalysisRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->getUserIdFromPath() === auth()->user()?->id;
    }

    public function getDTO(): FilterUserAnalysDTO
    {
        if (! $validated = $this->validated()) {
            throw new UnprocessableEntityHttpException();
        }

        return FilterUserAnalysDTO::from($validated);
    }

    public function getUserIdFromPath(): string
    {
        return $this->route('userId');
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
            'user_ids.*' => ['string', new UserIdMatchesAuth(), new UserIdMatchesUrlUserId()],

            // analys_id
            'analys_ids' => ['array'],
            'analys_ids.*' => ['int', Rule::enum(Analys::class)],
        ];
    }
}
