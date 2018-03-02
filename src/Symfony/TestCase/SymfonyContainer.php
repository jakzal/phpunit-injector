<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Symfony\TestCase;

use Psr\Container\ContainerInterface;
use Zalas\Injector\PHPUnit\Symfony\Compiler\ExposeServicesForTestsPass;

/**
 * Provides a `ServiceContainerTestCase` implementation with the container created by the Symfony Kernel.
 */
trait SymfonyContainer
{
    use SymfonyKernel;

    public function createContainer(): ContainerInterface
    {
        return static::bootKernel()->getContainer()->get($this->getTestServiceLocatorId());
    }

    protected function getTestServiceLocatorId(): string
    {
        return ExposeServicesForTestsPass::DEFAULT_SERVICE_LOCATOR_ID;
    }
}
