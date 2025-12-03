<?php

declare(strict_types=1);

namespace Presentation\Analys\Controllers;

use Application\Analys\Services\Contracts\AnalysServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Presentation\BaseController;
use OpenApi\Attributes as OA;
use Presentation\Analys\Resources\AnalysResourceCollection;

class AnalysController extends BaseController
{
    public function __construct(
        protected readonly AnalysServiceContract $analysService,
    ) {}

    #[OA\Get(
        path: '/api/analysis',
        operationId: 'apiAnalysisIndex',
        description: 'Get all analysis.',
        tags: ['analys', 'api'],
    )]
    #[OA\Response(
        response: 200,
        description: 'List analysis data.',
        content: new OA\JsonContent(ref: AnalysResourceCollection::class)
    )]
    public function index(): JsonResponse
    {
        $analysis = $this->analysService->getAnalysis();

        return Response::json(new AnalysResourceCollection($analysis));
    }
}
