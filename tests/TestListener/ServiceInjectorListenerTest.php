<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Tests\TestListener;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestListener;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase;
use Zalas\Injector\PHPUnit\TestListener\ServiceInjectorListener;
use Zalas\Injector\PHPUnit\Tests\TestListener\Fixtures\Service1;
use Zalas\Injector\PHPUnit\Tests\TestListener\Fixtures\Service2;

class ServiceInjectorListenerTest extends TestCase implements ServiceContainerTestCase
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

    /**
     * @var Service2
     */
    private $service2NotInjected;

    public function test_it_is_a_phpunit_listener()
    {
        $this->assertInstanceOf(TestListener::class, new ServiceInjectorListener());
    }

    public function test_it_ignores_non_standard_test_cases()
    {
        $test = $this->prophesize(Test::class)->reveal();

        $listener = new ServiceInjectorListener();

        $this->assertNull($listener->startTest($test));
    }

    public function test_it_injects_services_into_test_cases()
    {
        $listener = new ServiceInjectorListener();

        $listener->startTest($this);

        $this->assertInstanceOf(Service1::class, $this->service1);
        $this->assertInstanceOf(Service2::class, $this->service2);
        $this->assertNull($this->service2NotInjected);
    }

    public function createContainer(): ContainerInterface
    {
        return new class implements ContainerInterface {
            public function get($id)
            {
                if (Service1::class === $id) {
                    return new Service1();
                }

                if ('foo.service2' === $id) {
                    return new Service2();
                }

                throw new class extends \Exception implements NotFoundExceptionInterface {
                };
            }

            public function has($id)
            {
                return \in_array($id, [Service1::class, 'foo.service2'], true);
            }
        };
    }
}
