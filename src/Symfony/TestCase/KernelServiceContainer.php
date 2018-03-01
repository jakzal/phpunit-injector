<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\Injector\Symfony\TestCase;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zalas\PHPUnit\Injector\Symfony\Compiler\ExposeServicesForTestsPass;

/**
 * Provides a Symfony implementation of the `ServiceContainerTestCase` with Symfony's `KernelTestCase` methods.
 *
 * Suitable for end users who'd like to take advantage of property service injection and keep using the `KernelTestCase`.
 */
trait KernelServiceContainer
{
    public function createContainer(): ContainerInterface
    {
        if (!$this instanceof KernelTestCase) {
            throw new \RuntimeException();
        }

        return static::bootKernel()->getContainer()->get($this->getTestServiceLocatorId());
    }

    private function getTestServiceLocatorId(): string
    {
        return ExposeServicesForTestsPass::DEFAULT_SERVICE_LOCATOR_ID;
    }
}
