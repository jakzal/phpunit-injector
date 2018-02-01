<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\Service;

use PHPUnit\Framework\TestCase;
use Zalas\PHPUnit\DependencyInjection\Service\ServiceProperty;

class ServicePropertyTest extends TestCase
{
    public function test_it_exposes_the_property_name_and_service_id()
    {
        $serviceProperty = new ServiceProperty('myProperty', 'my.service.id');

        $this->assertSame('myProperty', $serviceProperty->getPropertyName());
        $this->assertSame('my.service.id', $serviceProperty->getServiceId());
    }
}
