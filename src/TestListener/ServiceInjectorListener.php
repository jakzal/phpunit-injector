<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\TestListener;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use Zalas\PHPUnit\DependencyInjection\PhpDocumentor\ReflectionExtractor;
use Zalas\PHPUnit\DependencyInjection\Service\Injector;

class ServiceInjectorListener implements TestListener
{
    use TestListenerDefaultImplementation;

    public function startTest(Test $test): void
    {
        if ($test instanceof ServiceContainerTestCase) {
            $injector = new Injector(new ReflectionExtractor(), new TestCaseContainerFactory($test));
            $injector->inject($test);
        }
    }
}
