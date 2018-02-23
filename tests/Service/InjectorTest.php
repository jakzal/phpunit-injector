<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\Service;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zalas\PHPUnit\DependencyInjection\Service\Exception\FailedToInjectServiceException;
use Zalas\PHPUnit\DependencyInjection\Service\Exception\MissingServiceException;
use Zalas\PHPUnit\DependencyInjection\Service\Extractor;
use Zalas\PHPUnit\DependencyInjection\Service\Injector;
use Zalas\PHPUnit\DependencyInjection\Service\ServiceProperty;
use Zalas\PHPUnit\DependencyInjection\Tests\Service\Fixtures\Service1;
use Zalas\PHPUnit\DependencyInjection\Tests\Service\Fixtures\Service2;
use Zalas\PHPUnit\DependencyInjection\Tests\Service\Fixtures\Services;

class InjectorTest extends TestCase
{
    /**
     * @var Injector
     */
    private $injector;

    /**
     * @var ContainerInterface|ObjectProphecy
     */
    private $container;

    /**
     * @var Extractor|ObjectProphecy
     */
    private $extractor;

    /**
     * @var Service1
     */
    private $service1;

    /**
     * @var Service2
     */
    private $service2;

    /**
     * @var Services
     */
    private $services;

    protected function setUp()
    {
        $this->service1 = new Service1();
        $this->service2 = new Service2();
        $this->services = new Services();
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->extractor = $this->prophesize(Extractor::class);

        $this->container->get(Service1::class)->willReturn($this->service1);
        $this->container->get('service2')->willReturn($this->service2);

        $this->extractor->extract(Services::class)->willReturn([
            new ServiceProperty(Services::class, 'service1', Service1::class),
            new ServiceProperty(Services::class, 'service2', 'service2'),
        ]);

        $this->injector = new Injector($this->extractor->reveal(), $this->container->reveal());
    }

    public function test_it_injects_services_into_class_properties()
    {
        $this->injector->inject($this->services);

        $this->assertSame($this->service1, $this->services->getService1());
        $this->assertSame($this->service2, $this->services->getService2());
    }

    public function test_it_throws_an_exception_if_container_fails_in_any_way()
    {
        $this->expectException(FailedToInjectServiceException::class);
        $this->expectExceptionCode(0);

        $this->container->get(Service1::class)->willThrow(new class extends \Exception implements ContainerExceptionInterface {});

        $this->injector->inject($this->services);
    }

    public function test_it_throws_an_exception_if_service_is_not_found()
    {
        $this->expectException(MissingServiceException::class);
        $this->expectExceptionCode(0);

        $this->container->get(Service1::class)->willThrow(new class extends \Exception implements NotFoundExceptionInterface {});

        $this->injector->inject($this->services);
    }
}
