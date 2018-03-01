<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\Symfony\Compiler;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Zalas\Injector\Service\Property;
use Zalas\PHPUnit\DependencyInjection\Symfony\Compiler\Discovery\PropertyDiscovery;
use Zalas\PHPUnit\DependencyInjection\Symfony\Compiler\ExposeServicesForTestsPass;
use Zalas\PHPUnit\DependencyInjection\Tests\Symfony\Compiler\Fixtures\Service1;
use Zalas\PHPUnit\DependencyInjection\Tests\Symfony\Compiler\Fixtures\Service2;
use Zalas\PHPUnit\DependencyInjection\Tests\Symfony\Compiler\Fixtures\TestCase1;

class ExposeServicesForTestsPassTest extends TestCase
{
    const SERVICE_LOCATOR_ID = 'app.test.service_locator';

    /**
     * @var ExposeServicesForTestsPass
     */
    private $pass;

    /**
     * @var PropertyDiscovery|ObjectProphecy
     */
    private $discovery;

    protected function setUp()
    {
        $this->discovery = $this->prophesize(PropertyDiscovery::class);
        $this->pass = new ExposeServicesForTestsPass(self::SERVICE_LOCATOR_ID, $this->discovery->reveal());
    }

    public function test_it_is_a_compiler_pass()
    {
        $this->assertInstanceOf(CompilerPassInterface::class, $this->pass);
    }

    public function test_it_registers_a_service_locator_for_services_used_in_tests()
    {
        $this->discovery->run()->willReturn([
            new Property(TestCase1::class, 'service1', Service1::class),
            new Property(TestCase1::class, 'service2', Service2::class),
        ]);

        $container = new ContainerBuilder();

        $this->pass->process($container);

        $this->assertTrue($container->hasDefinition(self::SERVICE_LOCATOR_ID), 'The service locator is registered as a service.');
        $this->assertSame(ServiceLocator::class, $container->getDefinition(self::SERVICE_LOCATOR_ID)->getClass());
        $this->assertFalse($container->getDefinition(self::SERVICE_LOCATOR_ID)->isPrivate(), 'The service locator is registered as a public service.');
        $this->assertTrue($container->getDefinition(self::SERVICE_LOCATOR_ID)->isPublic(), 'The service locator is registered as a public service.');
        $this->assertTrue($container->getDefinition(self::SERVICE_LOCATOR_ID)->hasTag('container.service_locator'), 'The service locator is tagged.');
        $this->assertEquals([Service1::class => new Reference(Service1::class), Service2::class => new Reference(Service2::class)], $container->getDefinition(self::SERVICE_LOCATOR_ID)->getArgument(0));
    }

    public function test_it_registers_an_empty_service_locator_if_no_services_were_discovered()
    {
        $this->discovery->run()->willReturn([]);

        $container = new ContainerBuilder();

        $this->pass->process($container);

        $this->assertTrue($container->hasDefinition(self::SERVICE_LOCATOR_ID), 'The service locator is registered as a service.');
        $this->assertEquals([], $container->getDefinition(self::SERVICE_LOCATOR_ID)->getArgument(0), 'No services were registered on the service locator.');
    }
}
