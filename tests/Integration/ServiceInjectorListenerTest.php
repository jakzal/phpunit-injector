<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zalas\PHPUnit\DependencyInjection\TestCase\ServiceContainerTestCase;
use Zalas\PHPUnit\DependencyInjection\Tests\Integration\Fixtures\Service1;
use Zalas\PHPUnit\DependencyInjection\Tests\Integration\Fixtures\Service2;

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

    public function test_it_injects_services_into_the_test_case()
    {
        $this->assertInstanceOf(Service1::class, $this->service1);
        $this->assertInstanceOf(Service2::class, $this->service2);
        $this->assertNull($this->service2NotInjected);
    }

    public function createContainer(array $requiredServices): ContainerInterface
    {
        return new class implements ContainerInterface
        {
            public function get($id)
            {
                if (Service1::class === $id) {
                    return new Service1();
                }

                if ('foo.service2' === $id) {
                    return new Service2();
                }

                throw new class extends \Exception implements NotFoundExceptionInterface
                {
                };
            }

            public function has($id)
            {
                return in_array($id, [Service1::class, 'foo.service2']);
            }
        };
    }
}
