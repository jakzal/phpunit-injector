<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Zalas\Injector\Factory\DefaultExtractorFactory;
use Zalas\PHPUnit\DependencyInjection\Symfony\Compiler\Discovery\ClassFinder;
use Zalas\PHPUnit\DependencyInjection\Symfony\Compiler\Discovery\PropertyDiscovery;
use Zalas\PHPUnit\DependencyInjection\Symfony\Compiler\ExposeServicesForTestsPass;
use Zalas\PHPUnit\DependencyInjection\Tests\Integration\Fixtures\Service1;
use Zalas\PHPUnit\DependencyInjection\Tests\Integration\Fixtures\Service2;

class SymfonyTest extends TestCase
{
    public function test_it_registers_the_service_locator_with_discovered_services()
    {
        $container = $this->createContainerBuilder();
        $compilerPass = new ExposeServicesForTestsPass(
            'test.service_locator',
            new PropertyDiscovery(new ClassFinder(__DIR__ . '/Fixtures'), new DefaultExtractorFactory())
        );
        $container->addCompilerPass($compilerPass);
        $container->compile();

        $this->assertTrue($container->has('test.service_locator'), 'The test service locator is registered in the container.');
        $this->assertTrue($container->get('test.service_locator')->has(Service1::class), 'Service is registered by the type.');
        $this->assertTrue($container->get('test.service_locator')->has('foo.service2'), 'Service is registered by id.');
        $this->assertInstanceOf(Service1::class, $container->get('test.service_locator')->get(Service1::class));
        $this->assertInstanceOf(Service2::class, $container->get('test.service_locator')->get('foo.service2'));
    }

    private function createContainerBuilder(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->register('foo.service1', Service1::class);
        $container->register('foo.service2', Service2::class);
        $container->setAlias(Service1::class, new Alias('foo.service1'));
        $container->setAlias(Service2::class, new Alias('foo.service2'));

        return $container;
    }
}