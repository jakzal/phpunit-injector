<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\Symfony\Compiler\Fixtures {
    use Psr\Container\ContainerInterface;
    use Zalas\PHPUnit\DependencyInjection\TestCase\ServiceContainerTestCase;

    class TestCase2 implements ServiceContainerTestCase
    {
        private $service2;

        private $notClassDefinition = Service1::class;

        public function createContainer(): ContainerInterface
        {
        }
    }
}