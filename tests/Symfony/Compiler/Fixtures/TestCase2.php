<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Tests\Symfony\Compiler\Fixtures {
    use Psr\Container\ContainerInterface;
    use Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase;

    class TestCase2 implements ServiceContainerTestCase
    {
        private $service2;

        private $notClassDefinition = Service1::class;

        public function createContainer(): ContainerInterface
        {
        }
    }
}
