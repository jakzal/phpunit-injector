<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\PhpDocumentor;

use PHPUnit\Framework\TestCase;
use Zalas\PHPUnit\DependencyInjection\PhpDocumentor\ReflectionServicePropertyExtractor;
use Zalas\PHPUnit\DependencyInjection\Service\MissingServiceIdException;
use Zalas\PHPUnit\DependencyInjection\Service\ServiceProperty;
use Zalas\PHPUnit\DependencyInjection\Service\ServicePropertyExtractor;
use Zalas\PHPUnit\DependencyInjection\Tests\PhpDocumentor\Fixtures\DuplicatedInjectExample;
use Zalas\PHPUnit\DependencyInjection\Tests\PhpDocumentor\Fixtures\DuplicatedVarExample;
use Zalas\PHPUnit\DependencyInjection\Tests\PhpDocumentor\Fixtures\FieldInjectionExample;
use Zalas\PHPUnit\DependencyInjection\Tests\PhpDocumentor\Fixtures\Foo\Foo;
use Zalas\PHPUnit\DependencyInjection\Tests\PhpDocumentor\Fixtures\MissingTypeExample;

class ReflectionServicePropertyExtractorTest extends TestCase
{
    /**
     * @var ReflectionServicePropertyExtractor
     */
    private $servicePropertyExtractor;

    protected function setUp()
    {
        $this->servicePropertyExtractor = new ReflectionServicePropertyExtractor();
    }

    public function test_it_is_a_property_extractor()
    {
        $this->assertInstanceOf(ServicePropertyExtractor::class, $this->servicePropertyExtractor);
    }

    public function test_it_extracts_service_definitions_from_properties()
    {
        $serviceProperties = $this->servicePropertyExtractor->extract(FieldInjectionExample::class);

        $this->assertContainsOnlyInstancesOf(ServiceProperty::class, $serviceProperties);
        $this->assertCount(3, $serviceProperties);
        $this->assertEquals(new ServiceProperty(FieldInjectionExample::class, 'fieldWithServiceIdNoVar', 'foo.bar'), $serviceProperties[0]);
        $this->assertEquals(new ServiceProperty(FieldInjectionExample::class, 'fieldWithVarNoServiceId', Foo::class), $serviceProperties[1]);
        $this->assertEquals(new ServiceProperty(FieldInjectionExample::class, 'fieldWithVarAndServiceId', 'foo.bar'), $serviceProperties[2]);
    }

    public function test_it_ignores_a_duplicated_type()
    {
        $serviceProperties = $this->servicePropertyExtractor->extract(DuplicatedVarExample::class);

        $this->assertContainsOnlyInstancesOf(ServiceProperty::class, $serviceProperties);
        $this->assertCount(1, $serviceProperties);
        $this->assertEquals(new ServiceProperty(DuplicatedVarExample::class, 'fooWithDuplicatedVar', Foo::class), $serviceProperties[0]);
    }

    public function test_it_ignores_a_duplicated_inject()
    {
        $serviceProperties = $this->servicePropertyExtractor->extract(DuplicatedInjectExample::class);

        $this->assertContainsOnlyInstancesOf(ServiceProperty::class, $serviceProperties);
        $this->assertCount(1, $serviceProperties);
        $this->assertEquals(new ServiceProperty(DuplicatedInjectExample::class, 'fooWithDuplicatedInject', 'foo.bar'), $serviceProperties[0]);
    }

    public function test_it_throws_missing_service_id_exception_if_there_is_no_service_id_nor_type()
    {
        $this->expectException(MissingServiceIdException::class);

        $this->servicePropertyExtractor->extract(MissingTypeExample::class);
    }
}
