<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\TestCase;

use Psr\Container\ContainerInterface;
use Zalas\PHPUnit\DependencyInjection\Service\RequiredService;

/**
 * Should be implemented by test cases that require services to be injected into their properties.
 */
interface ServiceContainerTestCase
{
    /**
     * @param RequiredService[] $requiredServices
     * @return ContainerInterface
     */
    public function createContainer(array $requiredServices): ContainerInterface;
}
