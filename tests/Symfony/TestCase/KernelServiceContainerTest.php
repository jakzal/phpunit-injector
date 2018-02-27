<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\Symfony\TestCase;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zalas\PHPUnit\DependencyInjection\Symfony\TestCase\KernelServiceContainer;
use Zalas\PHPUnit\DependencyInjection\Tests\Symfony\TestCase\Fixtures\TestKernel;

class KernelServiceContainerTest extends KernelTestCase
{
    use KernelServiceContainer;

    public function test_it_initializes_the_container_by_booting_the_symfony_kernel()
    {
        $container = $this->createContainer();

        $this->assertNotNull(self::$kernel);
        $this->assertSame(self::$kernel->getContainer(), $container);
    }

    protected static function getKernelClass()
    {
        return TestKernel::class;
    }
}
