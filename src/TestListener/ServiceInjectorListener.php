<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\TestListener;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use Zalas\Injector\Factory\DefaultExtractorFactory;
use Zalas\Injector\Service\Injector;
use Zalas\PHPUnit\DependencyInjection\TestCase\ServiceContainerTestCase;

class ServiceInjectorListener implements TestListener
{
    use TestListenerDefaultImplementation;

    public function startTest(Test $test): void
    {
        if ($test instanceof ServiceContainerTestCase) {
            $injector = new Injector(new TestCaseContainerFactory($test), new DefaultExtractorFactory());
            $injector->inject($test);
        }
    }
}
