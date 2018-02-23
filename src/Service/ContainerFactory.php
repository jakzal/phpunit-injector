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
     * @param RequiredService[] $requiredServices
     * @return ContainerInterface
     */
    public function create(array $requiredServices = []): ContainerInterface;
}
