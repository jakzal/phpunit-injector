<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zalas\PHPUnit\DependencyInjection\PhpDocumentor\ReflectionExtractor;
use Zalas\PHPUnit\DependencyInjection\Service\ContainerFactory;
use Zalas\PHPUnit\DependencyInjection\Service\Injector;
use Zalas\PHPUnit\DependencyInjection\Tests\Integration\Fixtures\Service1;
use Zalas\PHPUnit\DependencyInjection\Tests\Integration\Fixtures\Service2;
use Zalas\PHPUnit\DependencyInjection\Tests\Integration\Fixtures\Services;

class InjectorTest extends TestCase
{
    public function test_it_injects_services_into_class_properties_with_reflection_extractor()
    {
        $injector = new Injector(new ReflectionExtractor(), $this->createContainerFactory());

        $services = new Services();

        $injector->inject($services);

        $this->assertInstanceOf(Service1::class, $services->getService1());
        $this->assertInstanceOf(Service2::class, $services->getService2());
    }

    private function createContainerFactory(): ContainerFactory
    {
        return new class implements ContainerFactory
        {
            public function create(): ContainerInterface
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
        };
    }
}
