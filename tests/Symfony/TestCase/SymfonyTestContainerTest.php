<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Tests\Symfony\TestCase;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use Zalas\Injector\PHPUnit\Symfony\TestCase\SymfonyTestContainer;
use Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase;
use Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\Service1;
use Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\Service2;

class SymfonyTestContainerTest extends TestCase implements ServiceContainerTestCase
{
    use SymfonyTestContainer;

    protected function setUp()
    {
        if (!\class_exists(TestContainer::class)) {
            $this->markTestSkipped('SymfonyTestContainer requires Symfony >= 4.1.');
        }
    }

    /**
     * @env KERNEL_CLASS=Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\FrameworkBundle\TestKernel
     */
    public function test_it_initializes_the_container_by_booting_the_symfony_kernel()
    {
        $container = $this->createContainer();

        $this->assertInstanceOf(TestContainer::class, $container, 'Full container is not exposed.');
        $this->assertTrue($container->has(Service1::class), 'The private service is available in tests.');
        $this->assertTrue($container->has('foo.service2'), 'The private service is available in tests.');
        $this->assertInstanceOf(Service1::class, $container->get(Service1::class));
        $this->assertInstanceOf(Service2::class, $container->get('foo.service2'));
    }

    /**
     * @env KERNEL_CLASS=Zalas\Injector\PHPUnit\Tests\Symfony\TestCase\Fixtures\FrameworkBundle\AnotherTestKernel
     */
    public function test_it_ignores_missing_services_when_registering_the_service_locator()
    {
        $container = $this->createContainer();

        $this->assertInstanceOf(TestContainer::class, $container, 'Full container is not exposed.');
        $this->assertTrue($container->has(Service1::class), 'The private service is available in tests.');
        $this->assertFalse($container->has('foo.service2'), 'The private service is not available in tests.');
        $this->assertInstanceOf(Service1::class, $container->get(Service1::class));
    }
}
