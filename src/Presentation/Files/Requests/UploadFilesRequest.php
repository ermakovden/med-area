<?php

declare(strict_types=1);

namespace Presentation\Files\Requests;

use Application\S3\DTO\FileDTO;
use Application\S3\DTO\Requests\CreateFilesRequestDTO;
use Illuminate\Http\UploadedFile;
use OpenApi\Attributes as OA;
use Shared\Requests\BaseRequest;
use Shared\Rules\UserIdMatchesAuth;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

#[OA\Schema(
    title: 'UploadFilesRequest',
    description: 'Upload files request.',
    properties: [
        new OA\Property(property: 'files', description: 'List of files.', format: 'array', items: new OA\Items(
            new OA\MediaType(
                schema: new OA\Schema(
                    title: 'file',
                    description: 'Content for upload. Max size: 5MB',
                    format: 'file',
                )
            )
        )),
    ]
)]
class UploadFilesRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function getDTO(): CreateFilesRequestDTO
    {
        if (! $validated = $this->validated()) {
            throw new UnprocessableEntityHttpException();
        }

        $dto = CreateFilesRequestDTO::from([]);
        $dto->files = [];

        foreach ($validated['files'] as $file) {
            /** @var UploadedFile $file */

            $dto->files[] = FileDTO::from([
                'user_id' => $this->user()?->id,
                'bucket' => config('filesystems.disks.s3.bucket'),
                'endpoint' => config('filesystems.disks.s3.endpoint'),
                'size' => $file->getSize(),
                'content' => $file,
                'key' => microtime(),
            ]);
        }

        return $dto;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'string', new UserIdMatchesAuth()],

            'files' => ['required', 'array'],
            'files.*' => ['required', 'file', 'max:15000'],
        ];
    }
}
