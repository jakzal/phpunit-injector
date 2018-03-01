<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\Injector\Tests\Integration\Fixtures;

use Psr\Container\ContainerInterface;
use Zalas\PHPUnit\Injector\TestCase\ServiceContainerTestCase;

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

    public function createContainer(): ContainerInterface
    {
    }
}
