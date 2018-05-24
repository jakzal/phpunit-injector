<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Symfony\TestCase;

use Psr\Container\ContainerInterface;

/**
 * Provides a `ServiceContainerTestCase` implementation with the test container from the Symfony FrameworkBundle.
 *
 * `framework.test` needs to be set to `true` in order for the `test.service_container` to be available.
 */
trait SymfonyTestContainer
{
    use SymfonyKernel;

    public function createContainer(): ContainerInterface
    {
        return static::bootKernel()->getContainer()->get('test.service_container');
    }
}
