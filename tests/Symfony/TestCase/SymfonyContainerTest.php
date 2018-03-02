<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\Injector\Tests\Symfony\TestCase;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Zalas\PHPUnit\Injector\Symfony\TestCase\SymfonyContainer;
use Zalas\PHPUnit\Injector\TestCase\ServiceContainerTestCase;
use Zalas\PHPUnit\Injector\Tests\Symfony\TestCase\Fixtures\Service1;
use Zalas\PHPUnit\Injector\Tests\Symfony\TestCase\Fixtures\Service2;
use Zalas\PHPUnit\Injector\Tests\Symfony\TestCase\Fixtures\TestKernel;

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

    public function test_it_initializes_the_container_by_booting_the_symfony_kernel()
    {
        $container = $this->createContainer();

        $this->assertInstanceOf(ServiceLocator::class, $container, 'Full container is not exposed.');
        $this->assertTrue($container->has(Service1::class), 'The private service is available in tests.');
        $this->assertTrue($container->has('foo.service2'), 'The private service is available in tests.');
        $this->assertInstanceOf(Service1::class, $container->get(Service1::class));
        $this->assertInstanceOf(Service2::class, $container->get('foo.service2'));
    }

    protected static function getKernelClass()
    {
        return TestKernel::class;
    }
}
