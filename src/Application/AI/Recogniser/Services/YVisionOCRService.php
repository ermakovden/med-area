<?php

declare(strict_types=1);

namespace Application\AI\Recogniser\Services;

use Domain\AI\Recognise\DTO\RecogniseRequestDTO;
use Application\AI\Recogniser\DTO\RecogniseURLParamsDTO;
use Application\AI\Recogniser\DTO\Requests\RecogniseAsyncRequestDTO;
use Application\AI\Recogniser\DTO\Responses\RecogniseAsyncResponse;
use Application\AI\Recogniser\Services\Contracts\RecogniseRequestServiceContract;
use Application\AI\Recogniser\Services\Contracts\RecogniserServiceContract;
use Domain\AI\Recognise\Enums\RecogniseStatus;
use Illuminate\Support\Facades\Http;
use Infrastructure\Jobs\AI\Recogniser\UpdateYVisionRecogniseRequestJob;
use Shared\Enums\AuthTokenType;
use Shared\Exceptions\ServerErrorException;
use Shared\Services\BaseExternalService;

/**
 * Yandex Vision OCR Service
 *
 * See: https://yandex.cloud/ru/docs/vision/
 */
class YVisionOCRService extends BaseExternalService implements RecogniserServiceContract
{
    protected string $module = 'ocr';

    public function __construct(
        protected readonly RecogniseRequestServiceContract $recogniseRequestService,
    ) {
        parent::__construct();

        $authToken = config('yc.' . $this->module . '.secret');
        $this->setAuthorization($authToken, AuthTokenType::API_KEY);

        $this->setURLParams(RecogniseURLParamsDTO::from([
            'endpoint' => config('yc.' . $this->module . '.endpoint'),
            'version' => config('yc.' . $this->module . '.version'),
            'module' => $this->module,
        ]));
    }

    public function recogniseAsync(RecogniseAsyncRequestDTO $request, RecogniseRequestDTO $recogniseRequestDTO): RecogniseRequestDTO
    {
        $this->setURLResourceParam('recognizeTextAsync');

        try {
            $response = Http::withHeaders($this->getBaseHeaders())
                ->withUrlParameters($this->urlParams->toArray())
                ->post(
                    $this->getURITemplate(),
                    $request->toArray(),
                );

            if ($response->failed()) {
                $response->throw();
            }

            $responseDTO = RecogniseAsyncResponse::from($response->json());

            if ($response->successful()) {
                $recogniseRequestDTO->operation_id = $responseDTO->id;
                $recogniseRequestDTO->status = RecogniseStatus::PROCESSED;

                $recogniseRequestId = $recogniseRequestDTO->toArray()['id'] ?? null;
                if (\is_int($recogniseRequestId)) {
                    $this->recogniseRequestService->updateById($recogniseRequestId, $recogniseRequestDTO);
                }

                // Queue for periodic recognition status checking
                // First attempt after 35 seconds
                UpdateYVisionRecogniseRequestJob::dispatch($recogniseRequestDTO)
                    ->delay(now()->plus(seconds: 35));
            }

            return $recogniseRequestDTO;

        } catch (\Throwable $e) {
            \Log::error('Error when try start recognise async', [
                'class' => YVisionOCRService::class,
                'method' => 'recogniseAsync',
                'message' => $e->getMessage(),
            ]);
            throw new ServerErrorException();
        }
    }

    public function getRecognition(RecogniseRequestDTO $recogniseRequest): RecogniseAsyncResponse
    {
        $this->setURLResourceParam('getRecognition');

        try {
            $response = Http::withHeaders($this->getBaseHeaders())
                ->withUrlParameters($this->urlParams->toArray())
                ->get(
                    $this->getURITemplate(),
                    [
                        'operationId' => $recogniseRequest->operation_id,
                    ],
                );

            if ($response->failed()) {
                $response->throw();
            }

            return RecogniseAsyncResponse::from($response->json('result'));

        } catch (\Throwable $e) {
            \Log::error('Error when try get recognition', [
                'class' => YVisionOCRService::class,
                'method' => 'getRecognition',
                'message' => $e->getMessage(),
            ]);
            throw new ServerErrorException();
        }
    }

    public function getURITemplate(): string
    {
        return '{+endpoint}/{module}/{version}/{resource}';
    }
}
