<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\PhpDocumentor\Fixtures;

use Zalas\PHPUnit\DependencyInjection\Tests\PhpDocumentor\Fixtures\Foo\Bar;
use Zalas\PHPUnit\DependencyInjection\Tests\PhpDocumentor\Fixtures\Foo\Foo;

class DuplicatedVarExample
{
    /**
     * @var Foo
     * @var Bar
     * @inject
     */
    private $fooWithDuplicatedVar;
}