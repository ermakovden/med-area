<?php

declare(strict_types=1);

namespace Presentation\Analys\Requests\Recogniser;

use Application\AI\Recogniser\DTO\RecogniseRequestDTO;
use Application\AI\Recogniser\DTO\Requests\RecogniseAsyncRequestDTO;
use Application\S3\DTO\FileDTO;
use Domain\AI\Recognise\Enums\RecogniseStatus;
use Domain\AI\Recognise\Enums\YC\OCRModel;
use Domain\File\Models\File;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Shared\Enums\LanguageCode;
use Shared\Requests\BaseRequest;
use OpenApi\Attributes as OA;

#[OA\RequestBody(
    request: 'StartRecogniseRequest',
    description: 'Start recognise of file',
    content: new OA\JsonContent(
        required: [
            'file_id',
            'model',
        ],
        properties: [
            new OA\Property(property: 'file_id', description: 'File id for recognise', type: 'string', format: 'uuid'),
            new OA\Property(
                property: 'language_codes',
                description: 'List of language codes.',
                type: 'array',
                items: new OA\Items(
                    type: 'string',
                    enum: LanguageCode::class,
                )
            ),
            new OA\Property(property: 'model', description: 'Model of recogniser', type: 'string', enum: OCRModel::class),
        ],
    )
)]
class StartRecogniseRequest extends BaseRequest
{
    protected ?FileDTO $file = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function getDTO(): RecogniseAsyncRequestDTO
    {
        $dto = RecogniseAsyncRequestDTO::from($this->validated());

        if (empty($dto->languageCodes) || $dto->emptyValue('languageCodes')) {
            $dto->languageCodes = [LanguageCode::EN];
        }

        $file = $this->getFileDTO();
        $fileKeyParts = explode('.', $file->key);
        $dto->mimeType = end($fileKeyParts);

        return $dto;
    }

    public function getRecogniseRequestDTO(): RecogniseRequestDTO
    {
        $file = $this->getFileDTO();

        return RecogniseRequestDTO::from([
            'user_id' => auth()->user()?->id,
            'file_id' => $file->id,
            'operation_id' => null,
            'response' => null,
            'status' => RecogniseStatus::CREATED,
        ]);
    }

    public function getFileDTO(): FileDTO
    {
        if (! $this->file) {
            $fileModel = File::query()->findOrFail($this->string('file_id'));
            /** @var File $fileModel */

            // Check that file model size > 20mb (20971520 bytes)
            if ($fileModel->size > 20971520) {
                throw new InvalidArgumentException('File size > 20mb');
            }

            return FileDTO::from($fileModel);
        }

        return $this->file;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file_id' => ['required', 'string'],

            'language_codes' => ['array'],
            'language_codes.*' => ['string', Rule::enum(LanguageCode::class)],

            'model' => ['required', Rule::enum(OCRModel::class)],
        ];
    }
}
