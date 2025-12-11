<?php

declare(strict_types=1);

namespace Presentation\File\Controllers;

use Application\S3\Services\Contracts\S3ServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Presentation\BaseController;
use Presentation\File\Requests\UploadFilesRequest;
use Presentation\File\Resources\FileResourceCollection;
use Shared\Enums\Storage as EnumsStorage;

class FileController extends BaseController
{
    public function __construct(
        protected readonly S3ServiceContract $s3Service,
    ) {}

    public function upload(UploadFilesRequest $request): JsonResponse
    {
        $dto = $request->getDTO();

        $models = [];

        if ($dto->isNotEmptyValue('files')) {
            /** @phpstan-ignore-next-line */
            foreach ($dto->files as $file) {
                $file->storage = EnumsStorage::S3;
                $models[] = $this->s3Service->upload($file);
            }
        }

        return Response::json(FileResourceCollection::make($models), 201);
    }
}
