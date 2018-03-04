<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Tests\Symfony\TestCase;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Zalas\Injector\PHPUnit\Symfony\TestCase\SymfonyContainer;
use Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase;
use Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\Service1;
use Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\Service2;

class SymfonyContainerTest extends TestCase implements ServiceContainerTestCase
{
    use SymfonyContainer;

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

    /**
     * @env KERNEL_CLASS=Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\TestKernel
     */
    public function test_it_initializes_the_container_by_booting_the_symfony_kernel()
    {
        $container = $this->createContainer();

        $this->assertInstanceOf(ServiceLocator::class, $container, 'Full container is not exposed.');
        $this->assertTrue($container->has(Service1::class), 'The private service is available in tests.');
        $this->assertTrue($container->has('foo.service2'), 'The private service is available in tests.');
        $this->assertInstanceOf(Service1::class, $container->get(Service1::class));
        $this->assertInstanceOf(Service2::class, $container->get('foo.service2'));
    }

    /**
     * @env KERNEL_CLASS=Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\AnotherTestKernel
     */
    public function test_it_ignores_missing_services_when_registering_the_service_locator()
    {
        $container = $this->createContainer();

        $this->assertInstanceOf(ServiceLocator::class, $container, 'Full container is not exposed.');
        $this->assertTrue($container->has(Service1::class), 'The private service is available in tests.');
        $this->assertFalse($container->has('foo.service2'), 'The private service is available in tests.');
        $this->assertInstanceOf(Service1::class, $container->get(Service1::class));
    }
}
