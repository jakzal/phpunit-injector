<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\Integration\Fixtures;

use Psr\Container\ContainerInterface;
use Zalas\PHPUnit\DependencyInjection\TestCase\ServiceContainerTestCase;

class TestCase1 implements ServiceContainerTestCase
{
    /**
     * @var Service1
     * @inject
     */
    private $service1;

    /**
     * @var Service2
     * @inject foo.service2
     */
    private $service2;

    public function createContainer(array $requiredServices): ContainerInterface
    {
    }
}
