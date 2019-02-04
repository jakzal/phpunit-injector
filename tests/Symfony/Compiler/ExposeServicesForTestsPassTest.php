<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Tests\Symfony\Compiler;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Config\Resource\ReflectionClassResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Zalas\Injector\PHPUnit\Symfony\Compiler\Discovery\PropertyDiscovery;
use Zalas\Injector\PHPUnit\Symfony\Compiler\ExposeServicesForTestsPass;
use Zalas\Injector\PHPUnit\Tests\Symfony\Compiler\Fixtures\Service1;
use Zalas\Injector\PHPUnit\Tests\Symfony\Compiler\Fixtures\Service2;
use Zalas\Injector\PHPUnit\Tests\Symfony\Compiler\Fixtures\TestCase1;
use Zalas\Injector\PHPUnit\Tests\Symfony\Compiler\Fixtures\TestCase2;
use Zalas\Injector\Service\Property;
use Zalas\PHPUnit\Doubles\TestCase\TestDoubles;

class ExposeServicesForTestsPassTest extends TestCase
{
    use TestDoubles;

    /**
     * @var ExposeServicesForTestsPass
     */
    private $pass;

    /**
     * @var PropertyDiscovery|ObjectProphecy
     */
    private $discovery;

    protected function setUp(): void
    {
        $this->pass = new ExposeServicesForTestsPass($this->discovery->reveal());
    }

    public function test_it_is_a_compiler_pass()
    {
        $this->assertInstanceOf(CompilerPassInterface::class, $this->pass);
    }

    public function test_it_registers_a_service_locator_for_each_test_case_requiring_service_injection()
    {
        $this->discovery->run()->willReturn([
            new Property(TestCase1::class, 'service1', Service1::class),
            new Property(TestCase1::class, 'service2', Service2::class),
            new Property(TestCase2::class, 'service2', Service2::class),
        ]);

        $container = new ContainerBuilder();

        $this->pass->process($container);

        $this->assertTrue($container->hasDefinition(TestCase1::class), 'The first test case service locator is registered as a service.');
        $this->assertSame(ServiceLocator::class, $container->getDefinition(TestCase1::class)->getClass());
        $this->assertSame(ServiceLocator::class, $container->getDefinition(TestCase2::class)->getClass());
        $this->assertFalse($container->getDefinition(TestCase1::class)->isPrivate(), 'The first test case service locator is registered as a public service.');
        $this->assertTrue($container->getDefinition(TestCase1::class)->isPublic(), 'The first test case service locator is registered as a public service.');
        $this->assertTrue($container->getDefinition(TestCase1::class)->hasTag('container.service_locator'), 'The first case service locator is tagged.');
        $this->assertEquals([Service1::class => new Reference(Service1::class, ContainerInterface::IGNORE_ON_INVALID_REFERENCE), Service2::class => new Reference(Service2::class, ContainerInterface::IGNORE_ON_INVALID_REFERENCE)], $container->getDefinition(TestCase1::class)->getArgument(0));
        $this->assertTrue($container->hasDefinition(TestCase2::class), 'The second test case service locator is registered as a service.');
        $this->assertFalse($container->getDefinition(TestCase2::class)->isPrivate(), 'The second test case service locator is registered as a public service.');
        $this->assertTrue($container->getDefinition(TestCase2::class)->isPublic(), 'The second test case service locator is registered as a public service.');
        $this->assertTrue($container->getDefinition(TestCase2::class)->hasTag('container.service_locator'), 'The second test case service locator is tagged.');
        $this->assertEquals([Service2::class => new Reference(Service2::class, ContainerInterface::IGNORE_ON_INVALID_REFERENCE)], $container->getDefinition(TestCase2::class, ContainerInterface::IGNORE_ON_INVALID_REFERENCE)->getArgument(0));
    }

    public function test_it_only_registers_a_service_locator_if_any_services_were_discovered()
    {
        $this->discovery->run()->willReturn([]);

        $container = new ContainerBuilder();

        $this->pass->process($container);

        $this->assertfalse($container->hasDefinition(TestCase1::class), 'The first test case service locator is not registered as a service.');
        $this->assertfalse($container->hasDefinition(TestCase2::class), 'The second test case service locator is not registered as a service.');
    }

    public function test_it_registers_test_cases_as_container_resources()
    {
        $this->discovery->run()->willReturn([
            new Property(TestCase1::class, 'service1', Service1::class),
            new Property(TestCase1::class, 'service2', Service2::class),
            new Property(TestCase2::class, 'service2', Service2::class),
        ]);

        $container = new ContainerBuilder();
        $this->pass->process($container);

        $resources = $container->getResources();

        $this->assertCount(2, $resources);
        $this->assertContainsOnlyInstancesOf(ReflectionClassResource::class, $resources);
        $this->assertRegExp('#'.\preg_quote(TestCase1::class, '#').'#', (string) $resources[0]);
        $this->assertRegExp('#'.\preg_quote(TestCase2::class, '#').'#', (string) $resources[1]);
    }
}
