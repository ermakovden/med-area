<?php

declare(strict_types=1);

namespace Tests\Architecture;

class ApplicationArchitectureTest extends BaseArchitectureTest
{
    public function test_application_not_depend_infrastructure(): void
    {
        $this->assertDoesNotDependOn($this->application, $this->infrastructure);
    }

    public function test_application_not_depend_presentation(): void
    {
        $this->assertDoesNotDependOn($this->application, $this->presentation);
    }

    public function test_application_not_depend_domain(): void
    {
        $this->assertDoesNotDependOn($this->application, $this->domain);
    }
}
