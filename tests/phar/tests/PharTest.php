<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase;

class PharTest extends TestCase implements ServiceContainerTestCase
{
    /**
     * @var stdClass
     * @inject foo.service
     */
    private $service;

    public function test_it_injects_services_into_test_cases()
    {
        $this->assertInstanceOf(stdClass::class, $this->service);
    }

    public function createContainer(): ContainerInterface
    {
        return new class implements ContainerInterface {
            public function get($id)
            {
                if ('foo.service' === $id) {
                    return new stdClass();
                }

                throw new class extends \Exception implements NotFoundExceptionInterface {
                };
            }

            public function has($id)
            {
                return \in_array($id, ['foo.service'], true);
            }
        };
    }
}
