<?php

declare(strict_types=1);

namespace Tests\Architecture;

class PresentationArchitectureTest extends BaseArchitectureTest
{
    public function test_presentation_depend_domain(): void
    {
        $this->assertDependOn($this->presentation, $this->domain);
    }

    public function test_presentation_depend_application(): void
    {
        $this->assertDependOn($this->presentation, $this->application);
    }

    public function test_presentation_depend_infrastructure(): void
    {
        $this->assertDependOn($this->presentation, $this->infrastructure);
    }

    public function test_presentation_depend_shared(): void
    {
        $this->assertDependOn($this->domain, $this->shared);
    }
}
