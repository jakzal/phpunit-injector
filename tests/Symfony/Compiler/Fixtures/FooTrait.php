<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Tests\Symfony\Compiler\Fixtures;

trait FooTrait
{
    /**
     * Reproduces https://github.com/jakzal/phpunit-injector/issues/3
     */
    protected function bar(): string
    {
        return Service1::class;
    }
}
