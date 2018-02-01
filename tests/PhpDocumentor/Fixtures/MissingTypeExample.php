<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\PhpDocumentor\Fixtures;

class MissingTypeExample
{
    /**
     * @inject
     */
    private $fooWithNoServiceIdAndVar;
}