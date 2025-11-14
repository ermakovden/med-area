<?php

declare(strict_types=1);

namespace Tests\Architecture;

class ApplicationArchitectureTest extends BaseArchitectureTest
{
    public function test_application_depend_infrastructure(): void
    {
        $this->assertDependOn($this->application, $this->infrastructure);
    }

    public function test_application_depend_presentation(): void
    {
        $this->assertDependOn($this->application, $this->presentation);
    }

    public function test_application_depend_domain(): void
    {
        $this->assertDependOn($this->application, $this->domain);
    }
}
