<?php

declare(strict_types=1);

namespace Tests\Architecture;

class DomainArchitectureTest extends BaseArchitectureTest
{
    public function test_domain_by_application(): void
    {
        $this->assertDependOn($this->domain, $this->application);
    }

    public function test_domain_by_infrastructure(): void
    {
        $this->assertDependOn($this->domain, $this->infrastructure);
    }

    public function test_domain_by_presentation(): void
    {
        $this->assertDependOn($this->domain, $this->presentation);
    }

    public function test_domain_not_by_shared(): void
    {
        $this->assertDoesNotDependOn($this->domain, $this->shared);
    }
}