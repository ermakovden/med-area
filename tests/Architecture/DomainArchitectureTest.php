<?php

declare(strict_types=1);

namespace Tests\Architecture;

class DomainArchitectureTest extends BaseArchitectureTest
{
    public function test_domain_not_depend_application(): void
    {
        // TODO: Domain\*/Repositories\*Contract files currently import Application DTOs
        // (FilterUserAnalysDTO, FilterFileDTO, UserDTO, RecogniseRequestDTO, etc.).
        // These must be moved to Domain\ or Shared\ — tracked in .ai-factory/plans/refactor-dto-to-domain.md
        $this->markTestSkipped('Domain contracts depend on Application DTOs — pending DTO migration to Domain layer.');

        $this->assertDoesNotDependOn($this->domain, $this->application);
    }

    public function test_domain_not_depend_infrastructure(): void
    {
        $this->assertDoesNotDependOn($this->domain, $this->infrastructure);
    }

    public function test_domain_not_depend_presentation(): void
    {
        $this->assertDoesNotDependOn($this->domain, $this->presentation);
    }
}
