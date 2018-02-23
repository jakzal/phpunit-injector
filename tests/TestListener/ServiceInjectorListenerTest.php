<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\TestListener;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestListener;
use Zalas\PHPUnit\DependencyInjection\TestListener\ServiceInjectorListener;

class ServiceInjectorListenerTest extends TestCase
{
    public function test_it_is_a_phpunit_listener()
    {
        $this->assertInstanceOf(TestListener::class, new ServiceInjectorListener());
    }
}
