<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\Injector\TestCase;

use Psr\Container\ContainerInterface;

/**
 * Should be implemented by test cases that require services to be injected into their properties.
 */
interface ServiceContainerTestCase
{
    /**
     * @return ContainerInterface
     */
    public function createContainer(): ContainerInterface;
}
