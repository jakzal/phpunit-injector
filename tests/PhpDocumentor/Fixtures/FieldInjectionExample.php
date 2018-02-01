<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\PhpDocumentor\Fixtures;

use Zalas\PHPUnit\DependencyInjection\Tests\PhpDocumentor\Fixtures\Foo\Foo;

class FieldInjectionExample
{
    /**
     * @inject foo.bar
     */
    private $fieldWithServiceIdNoVar;

    /**
     * @var Foo
     * @inject
     */
    private $fieldWithVarNoServiceId;

    /**
     * @var Foo
     * @inject foo.bar
     */
    private $fieldWithVarAndServiceId;

    /**
     * @var Foo
     */
    private $fieldWithNoInject;

    private $fieldWithNoDocBlock;
}
