<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\Injector\Tests\Symfony\TestCase;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Zalas\PHPUnit\Injector\Symfony\TestCase\KernelServiceContainer;
use Zalas\PHPUnit\Injector\TestCase\ServiceContainerTestCase;
use Zalas\PHPUnit\Injector\Tests\Symfony\TestCase\Fixtures\TestKernel;

class KernelServiceContainerTest extends KernelTestCase implements ServiceContainerTestCase
{
    use KernelServiceContainer;

    /**
     * @var \stdClass
     * @inject foo
     */
    private $foo;

    public function test_it_initializes_the_container_by_booting_the_symfony_kernel()
    {
        $container = $this->createContainer();

        $this->assertNotNull(self::$kernel);
        $this->assertNotSame(self::$kernel->getContainer(), $container);
        $this->assertInstanceOf(ServiceLocator::class, $container);
        $this->assertTrue($container->has('foo'), 'The private service is available in tests.');
        $this->assertInstanceOf(\stdClass::class, $container->get('foo'));
    }

    protected static function getKernelClass()
    {
        return TestKernel::class;
    }
}
