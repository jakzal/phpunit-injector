<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\Injector\Tests\Symfony\Compiler\Fixtures;

use Psr\Container\ContainerInterface;
use Zalas\PHPUnit\Injector\TestCase\ServiceContainerTestCase;

class TestCase1 implements ServiceContainerTestCase
{
    private $service1;
    private $service2;

    public function createContainer(): ContainerInterface
    {
    }
}
