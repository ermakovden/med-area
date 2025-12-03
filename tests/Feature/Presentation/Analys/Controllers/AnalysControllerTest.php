<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\Analys\Controllers;

use Tests\TestCase;

class AnalysControllerTest extends TestCase
{
    public function test_index_sucess(): void
    {
        // Send API Request
        $response = $this->get(route('api.analysis.index'));

        // Check asserts
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                ],
            ],
        ]);
    }
}
