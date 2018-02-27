<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Symfony\TestCase;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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

        return static::bootKernel()->getContainer();
    }
}
