<?php

declare(strict_types=1);

namespace Application\AI\Recogniser\Services;

use Application\AI\Recogniser\DTO\RecogniseURLParamsDTO;
use Application\AI\Recogniser\DTO\Requests\RecogniseAsyncRequestDTO;
use Application\AI\Recogniser\DTO\Responses\RecogniseAsyncResponse;
use Application\AI\Recogniser\Services\Contracts\RecogniserServiceContract;
use Illuminate\Support\Facades\Http;
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
    public function __construct()
    {
        parent::__construct();

        $authToken = config('yc.ocr.secret');
        $this->setAuthorization($authToken, AuthTokenType::API_KEY);

        $this->setURLParams(RecogniseURLParamsDTO::from([
            'endpoint' => config('yc.ocr.endpoint'),
            'version' => config('yc.ocr.version'),
            'module' => 'ocr',
        ]));
    }

    public function recogniseAsync(RecogniseAsyncRequestDTO $request): RecogniseAsyncResponse
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

            \Log::info($response->json());
            return RecogniseAsyncResponse::from($response->json());

        } catch (\Exception $e) {
            \Log::error('Error when try start recognise async', [
                'class' => YVisionOCRService::class,
                'method' => 'recogniseAsync',
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
