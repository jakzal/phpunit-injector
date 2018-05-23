<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Symfony\TestCase;

use Psr\Container\ContainerInterface;

/**
 * Provides a `ServiceContainerTestCase` implementation with the container created by the Symfony Kernel.
 *
 * Relies on `ExposeServicesForTestsPass` compiler pass being registered.
 */
trait SymfonyContainer
{
    use SymfonyKernel;

    public function createContainer(): ContainerInterface
    {
        return static::bootKernel()->getContainer()->get(__CLASS__);
    }
}
