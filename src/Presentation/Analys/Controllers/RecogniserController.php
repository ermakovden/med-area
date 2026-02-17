<?php

declare(strict_types=1);

namespace Presentation\Analys\Controllers;

use Application\AI\Recogniser\Services\Contracts\RecogniseRequestServiceContract;
use Application\AI\Recogniser\Services\Contracts\RecogniserServiceContract;
use Application\S3\Services\Contracts\S3ServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Presentation\Analys\Requests\Recogniser\StartRecogniseRequest;
use Presentation\Analys\Resources\RecogniseRequestResource;
use OpenApi\Attributes as OA;
use Presentation\BaseController;

class RecogniserController extends BaseController
{
    public function __construct(
        protected readonly RecogniseRequestServiceContract $recogniseRequestService,
        protected readonly RecogniserServiceContract $recogniserService,
        protected readonly S3ServiceContract $s3Service,
    ) {}

    #[OA\Post(
        path: '/api/analysis/recogniser',
        operationId: 'apiAnalysisRecogniser',
        description: 'Start recognise of file.',
        tags: ['recognise', 'api'],
        requestBody: new OA\RequestBody(ref: StartRecogniseRequest::class),
    )]
    #[OA\Response(
        response: 200,
        description: 'Proccess of recognise file started.',
        content: new OA\JsonContent(ref: RecogniseRequestResource::class)
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized.',
    )]
    #[OA\Response(
        response: 403,
        description: 'Forbidden.',
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation error.',
    )]
    public function store(StartRecogniseRequest $request): JsonResponse
    {
        // Get DTOs from request
        $asyncRequestDTO = $request->getDTO();
        $recogniseRequestDTO = $request->getRecogniseRequestDTO();

        // Init model RecogniseRequest
        $recogniseRequestDTO = $this->recogniseRequestService->create($recogniseRequestDTO);

        // Get file content from s3 service
        $file = $request->getFileDTO();
        $asyncRequestDTO->content = base64_encode($this->s3Service->getFileFromStorage($file->key, $file->storage));

        // Start recognise of RecogniseRequest file content
        $responseRecognise = $this->recogniserService->recogniseAsync($asyncRequestDTO, $recogniseRequestDTO);

        return Response::json(new RecogniseRequestResource($responseRecognise));
    }
}
