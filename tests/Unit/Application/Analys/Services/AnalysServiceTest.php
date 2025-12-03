<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Analys\Services;

use Application\Analys\Services\AnalysService;
use Domain\Analys\Enums\Analys as AnalysEnum;
use Domain\Analys\Models\Analys;
use Infrastructure\Repositories\Contracts\AnalysRepositoryContract;
use Tests\TestCase;

class AnalysServiceTest extends TestCase
{
    /**
     * Сontains the service being tested
     *
     * @var AnalysService
     */
    protected AnalysService $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new AnalysService(
            app(AnalysRepositoryContract::class),
        );
    }

    public function test_get_analysis_success(): void
    {
        // Result from method of service
        $result = $this->service->getAnalysis();

        // Check assert result
        $this->assertCount(count(AnalysEnum::cases()), $result);
        $this->assertInstanceOf(Analys::class, $result[0]);
    }
}
