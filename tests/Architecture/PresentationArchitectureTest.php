<?php

declare(strict_types=1);

namespace Tests\Architecture;

class PresentationArchitectureTest extends BaseArchitectureTest
{
    public function test_presentation_not_by_domain(): void
    {
        $this->assertDoesNotDependOn($this->presentation, $this->domain);
    }

    public function test_presentation_not_by_application(): void
    {
        $this->assertDoesNotDependOn($this->presentation, $this->application);
    }

    public function test_presentation_not_by_infrastructure(): void
    {
        $this->assertDoesNotDependOn($this->presentation, $this->infrastructure);
    }

    public function test_presentation_not_by_shared(): void
    {
        $this->assertDoesNotDependOn($this->domain, $this->shared);
    }
}