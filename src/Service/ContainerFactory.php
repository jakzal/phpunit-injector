<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Service;

use Psr\Container\ContainerInterface;

/**
 * Creates a service container.
 */
interface ContainerFactory
{
    /**
     * @return ContainerInterface
     */
    public function create(): ContainerInterface;
}
