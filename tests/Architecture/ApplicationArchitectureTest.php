<?php

declare(strict_types=1);

namespace Tests\Architecture;

class ApplicationArchitectureTest extends BaseArchitectureTest
{
    public function test_application_depend_infrastructure(): void
    {
        // Application services dispatch Infrastructure Jobs and use Infrastructure Notifications directly.
        // Repository contracts were moved to Domain, but Jobs/Notifications coupling remains.
        // TODO: decouple via Domain Events — tracked in .ai-factory/plans/refactor-application-events.md
        $this->assertDependOn($this->application, $this->infrastructure);
    }

    public function test_application_does_not_depend_presentation(): void
    {
        $this->assertDoesNotDependOn($this->application, $this->presentation);
    }

    public function test_application_depend_domain(): void
    {
        $this->assertDependOn($this->application, $this->domain);
    }
}
