<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\Injector\Tests\TestListener;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestListener;
use Zalas\PHPUnit\Injector\TestListener\ServiceInjectorListener;

class ServiceInjectorListenerTest extends TestCase
{
    public function test_it_is_a_phpunit_listener()
    {
        $this->assertInstanceOf(TestListener::class, new ServiceInjectorListener());
    }
}
