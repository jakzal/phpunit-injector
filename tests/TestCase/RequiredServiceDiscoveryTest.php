<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\TestCase;

use Prophecy\Prophecy\ObjectProphecy;
use Zalas\PHPUnit\DependencyInjection\Service\Extractor;
use Zalas\PHPUnit\DependencyInjection\Service\RequiredService;
use Zalas\PHPUnit\DependencyInjection\TestCase\RequiredServiceDiscovery;
use PHPUnit\Framework\TestCase;
use Zalas\PHPUnit\DependencyInjection\Tests\TestCase\Fixtures\Service1;
use Zalas\PHPUnit\DependencyInjection\Tests\TestCase\Fixtures\TestCase1;
use Zalas\PHPUnit\DependencyInjection\Tests\TestCase\Fixtures\TestCase2;

class RequiredServiceDiscoveryTest extends TestCase
{
    /**
     * @var RequiredServiceDiscovery
     */
    private $discovery;

    /**
     * @var Extractor|ObjectProphecy
     */
    private $extractor;

    protected function setUp()
    {
        $this->extractor = $this->prophesize(Extractor::class);
        $this->discovery = new RequiredServiceDiscovery($this->extractor->reveal());
    }

    public function test_it_discovers_required_services_in_service_container_test_cases_from_the_given_namespace()
    {
        $requiredService1 = new RequiredService(TestCase1::class, 'service1', Service1::class);
        $requiredService2 = new RequiredService(TestCase1::class, 'service2', Service1::class);
        $requiredService2again = new RequiredService(TestCase2::class, 'service2', Service2::class);

        $this->extractor->extract(TestCase1::class)->willReturn([$requiredService1, $requiredService2]);
        $this->extractor->extract(TestCase2::class)->willReturn([$requiredService2again]);

        $requiredServices = $this->discovery->run('Zalas\PHPUnit\DependencyInjection\Tests\TestCase\Fixtures');

        $this->assertNotEmpty($requiredServices);
        $this->assertCount(3, $requiredServices);
        $this->assertSame([$requiredService1, $requiredService2, $requiredService2again], $requiredServices);
    }

    public function test_it_returns_no_services_if_there_is_no_required_service_test_cases_in_the_given_namespace()
    {
        $this->assertEmpty($this->discovery->run('Foo\Bar\Baz'));
    }
}
