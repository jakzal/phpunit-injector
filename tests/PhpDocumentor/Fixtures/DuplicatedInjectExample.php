<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\PhpDocumentor\Fixtures;

class DuplicatedInjectExample
{
    /**
     * @inject foo.bar
     * @inject bar.foo
     */
    private $fooWithDuplicatedInject;

}
